<?php

/**
 * @file
 * Abstracts the API Product object in the Management API and allows clients
 * to manipulate it.
 *
 * Write support is purely experimental and should not be used unless you're
 * feeling adventurous.
 *
 * @author djohnson
 */
namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ParameterException;

/**
 * Abstracts the API Product object in the Management API and allows clients
 * to manipulate it.
 *
 * Write support is purely experimental and is not recommended.
 *
 * @author djohnson
 */
class APIProduct extends Base
{

    /**
     * Array of API resources bundled in the API Product.
     * @var array
     */
    protected $apiResources;
    /**
     * @var string
     * The API product approval type as 'manual' or 'auto'.
     */
    protected $approvalType;
    /**
     * UNIX time when the API Product was created.
     * @var int
     */
    protected $createdAt;
    /**
     * @var string
     * The username of the user who created the API Product.
     * This property is read-only.
     */
    protected $createdBy;
    /**
     * @var int
     * UNIX time when the API Product was most recently updated.
     * This property is read-only.
     */
    protected $modifiedAt;
    /**
     * @var string
     * The username of the user who most recently updated the API Product.
     * This property is read-only.
     */
    protected $modifiedBy;
    /**
     * @var string
     * A string describing the API Product.
     * This property is read-only.
     */
    protected $description;
    /**
     * @var string
     * The name to be displayed in the UI or developer portal to developers
     * registering for API access.
     */
    protected $displayName;
    /**
     * @var array
     * Array of environment names in an organization.
     * Requests to environments not listed are rejected.
     */
    protected $environments;
    /**
     * @var string
     * The internal name of the API Product.
     */
    protected $name;
    /**
     * @var array
     * Array of API proxy names in an organization.
     * Requests to API proxies not listed are rejected.
     */
    protected $proxies;
    /**
     * @var int
     * The number of request messages permitted by this API product.
     * It's safer to use attributes['developer.quota.limit'] instead.
     */
    protected $quota;
    /**
     * @var int
     * The time interval over which the number of request messages is calculated.
     * It's safer to use attributes['developer.quota.interval'] instead.
     */
    protected $quotaInterval;
    /**
     * @var string
     * The time unit defined for the $quotaInterval.
     * It's safer to use attributes['developer.quota.timeunit'] instead.
     */
    protected $quotaTimeUnit;
    /**
     * @var array
     * Array of scopes.
     * These must map to the scopes defined in an Oauth policy associated
     * with the API Product.
     */
    protected $scopes;

    /**
     * @var array
     * Arbitrary name/value pairs.
     * Attributes must be protected because Base wants to twiddle with it.
     */
    protected $attributes;
    /**
     * @var bool
     * Indicate whether an API Product's details have been loaded from KMS.
     */
    protected $loaded;

    /**
     * Initializes all member variables
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $baseUrl = '/o/' . rawurlencode($config->orgName) . '/apiproducts';
        $this->init($config, $baseUrl);
        $this->blankValues();
    }

    /**
     * Queries the Management API and populates self's properties from
     * the result.
     *
     * If neither $name nor $result is passed, tries to load from $this->name.
     * If $name is passed, loads from $name instead of $this->name.
     * If $response is passed, bypasses API query and uses the given array
     * instead.
     *
     * @param null|string $name
     * @param null|array $response
     */
    public function load($name = null, $response = null)
    {
        $name = $name ? : $this->name;
        if (!isset($response)) {
            $this->get(rawurlencode($name));
            $response = $this->responseObj;
        }
        $this->apiResources = $response['apiResources'];
        $this->approvalType = $response['approvalType'];
        $this->readAttributes($response);
        $this->createdAt = $response['createdAt'];
        $this->createdBy = $response['createdBy'];
        $this->modifiedAt = $response['lastModifiedAt'];
        $this->modifiedBy = $response['lastModifiedBy'];
        $this->description = $response['description'];
        $this->displayName = $response['displayName'];
        $this->environments = $response['environments'];
        $this->name = $response['name'];
        $this->proxies = $response['proxies'];
        $this->quota = isset($response['quota']) ? $response['quota'] : null;
        $this->quotaInterval = isset($response['quotaInterval']) ? $response['quotaInterval'] : null;
        $this->quotaTimeUnit = isset($response['quotaTimeUnit']) ? $response['quotaTimeUnit'] : null;
        $this->scopes = $response['scopes'];

        $this->loaded = true;
    }

    /**
     * POSTs self's properties to Management API. This handles both
     * inserts and updates.
     */
    public function save()
    {
        $payload = array(
            'apiResources' => $this->apiResources,
            'approvalType' => $this->approvalType,
            'description' => $this->description,
            'displayName' => $this->displayName,
            'environments' => $this->environments,
            'name' => $this->name,
            'proxies' => $this->proxies,
            'quota' => $this->quota,
            'quotaInterval' => $this->quotaInterval,
            'quotaTimeUnit' => $this->quotaTimeUnit,
            'scopes' => $this->scopes
        );
        $this->writeAttributes($payload);
        $url = null;
        if ($this->modifiedBy) {
            $url = $this->name;
        }
        $this->post($url, $payload);
    }

    /**
     * Deletes an API Product.
     *
     * If $name is not passed, uses $this->name.
     *
     * @param null|string $name
     */
    public function delete($name = null)
    {
        $name = $name ? : $this->name;
        $this->delete(rawurlencode($name));
        if ($name == $this->name) {
            $this->blankValues();
        }
    }

    /**
     * Determines whether an API Product should be displayed to the public.
     *
     * If $product is passed, we expect it to be a raw response array as returned
     * from the Management API, and determination is based on those contents.
     *
     * If $product is not passed, we assume that $this is already loaded, and we
     * make the determination based on self's properties.
     *
     * @param null|array $product
     * @return bool
     */
    public function isPublic($product = null)
    {
        if (!isset($product)) {
            if (isset($this->attributes['access']) && ($this->attributes['access'] == 'internal' || $this->attributes['access'] == 'private')) {
                return false;
            }
        } else {
            foreach ($product['attributes'] as $attr) {
                if ($attr['name'] == 'access') {
                    return ($attr['value'] != 'internal' && $attr['value'] != 'private');
                }
            }
        }
        return true;
    }

    /**
     * Reads, caches and returns a detailed list of org's API Products.
     *
     * @return array
     */
    protected function getProductsCache()
    {
        static $api_products;
        if (!isset($api_products)) {
            $api_products = array();
        }
        $org = $this->config->orgName;
        if (!isset($api_products[$org])) {
            $api_products[$org] = array();
            $this->get('?expand=true');
            $response = $this->responseObj;
            foreach ($response['apiProduct'] as $prod) {
                $product = new self($this->getConfig());
                $product->load(null, $prod);
                $api_products[$org][] = $product;
            }
        }
        return $api_products[$org];
    }

    /**
     * Returns a detailed list of all products. This list may have been cached
     * from a previous call.
     *
     * If $show_nonpublic is true, even API Products which are marked as hidden
     * or internal are returned.
     *
     * @param bool $show_nonpublic
     * @return array
     */
    public function listProducts($show_nonpublic = false)
    {
        $products = $this->getProductsCache();
        if (!$show_nonpublic) {
            foreach ($products as $i => $product) {
                if (!$product->isPublic()) {
                    unset ($products[$i]);
                }
            }
        }
        return $products;
    }

    /**
     * Returns the attributes array of name/value pairs.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Clears the attributes array.
     */
    public function clearAttributes()
    {
        $this->attributes = array();
    }

    /**
     * Returns a specific attribute value, or null if the attribute does not exist.
     *
     * @param $name
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * Sets an attribute value.
     *
     * @param $name
     * @param $value
     */
    public function setAttribute($name, $value)
    {
        if (isset($value) || !isset($this->attributes[$name])) {
            $this->attributes[$name] = $value;
        } else {
            unset($this->attributes[$name]);
        }
    }

    /**
     * Returns the UNIX time when the API Product was created.
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Returns the username of the user who created the API Product.
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Returns the UNIX time when the API Product was most recently updated.
     *
     * @return integer
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Returns the username of the user who most recently updated the API Product.
     *
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Returns the array of environment names in which this API Product is
     * available.
     *
     * @return array
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * Returns the internal name of the API Product.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns an array of API proxy names used by this API Product.
     *
     * @return array
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * Returns the number of request messages permitted by this API product.
     *
     * @return integer|null
     */
    public function getQuotaLimit()
    {
        if (isset($this->attributes['developer.quota.limit'])) {
            return $this->attributes['developer.quota.limit'];
        } elseif (!empty($this->quota)) {
            return $this->quota;
        }
        return null;
    }

    /**
     * Returns the time interval over which the number of request messages is calculated.
     *
     * @return integer|null
     */
    public function getQuotaInterval()
    {
        if (isset($this->attributes['developer.quota.interval'])) {
            return $this->attributes['developer.quota.interval'];
        } elseif (!empty($this->quotaInterval)) {
            return $this->quotaInterval;
        }
        return null;
    }

    /**
     * Returns the time unit defined for the quota interval.
     *
     * @return string|null
     */
    public function getQuotaTimeUnit()
    {
        if (isset($this->attributes['developer.quota.timeunit'])) {
            return $this->attributes['developer.quota.timeunit'];
        } elseif (!empty($this->quotaTimeUnit)) {
            return $this->quotaTimeUnit;
        }
        return null;
    }

  /**
   * Returns the name to be displayed in the User Interface to developers
   * registering for API access.
   *
   * @return string
   */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Returns the string describing the API Product.
     *
     * @return string
     */
    public function getDescription()
    {
        if (!empty($this->description)) {
            return $this->description;
        }
        if (isset($this->attributes['description'])) {
            return $this->attributes['description'];
        }
        return null;
    }

    /**
     * Adds an API resource to the API Product.
     *
     * @param $resource
     */
    public function addApiResource($resource)
    {
        $this->apiResources[] = $resource;
    }

    /**
     * Removes an API resource from the API Product.
     *
     * @param string $resource
     */
    public function removeApiResource($resource)
    { // was delApiResource
        $index = array_search($resource, $this->apiResources);
        if ($index !== false) {
            unset($this->apiResources[$index]);
            // reindex this array to be sequential zero-based.
            $this->apiResources = array_values($this->apiResources);
        }
    }

    /**
     * Returns the array of API resources.
     *
     * @return array
     */
    public function getApiResources()
    {
        return $this->apiResources;
    }

    /**
     * Returns the API product approval type as 'manual' or 'auto'.
     *
     * @return string
     */
    public function getApprovalType()
    {
        return $this->approvalType;
    }

    /**
     * Sets the API product approval type as 'manual' or 'auto'.
     *
     * @param string $type
     */
    public function setApprovalType($type)
    {
        if ($type != 'auto' && $type != 'manual') {
            throw new ParameterException('Invalid approval type ' . $type . '; allowed values are "auto" and "manual".');
        }
        $this->approvalType = $type;
    }

    /**
     * Initializes this object to a blank state.
     */
    protected function blankValues()
    {
        $this->apiResources = array();
        $this->approvalType = 'auto';
        $this->attributes = array();
        $this->createdAt = null;
        $this->createdBy = null;
        $this->modifiedAt = null;
        $this->modifiedBy = null;
        $this->description = '';
        $this->displayName = '';
        $this->environments = array();
        $this->name = '';
        $this->proxies = array();
        $this->quota = '';
        $this->quotaInterval = '';
        $this->quotaTimeUnit = '';
        $this->scopes = array();

        $this->loaded = false;
    }


    /**
     * Turns this object's properties into an array for external use.
     *
     * @return array
     */
    public function toArray()
    {
        $output = array();
        foreach (self::getAPIProductProperties() as $property) {
            if ($property == 'debugData') {
                $output[$property] = $this->getDebugData();
            } else {
                $output[$property] = $this->$property;
            }
        }
        return $output;
    }

    /**
     * Returns an array of all property names that can be returned from a call to
     * self::toArray().
     *
     * @return array
     */
    public static function getAPIProductProperties()
    {
        $properties = array_keys(get_class_vars(__CLASS__));

        $parent_class = get_parent_class();
        $grandparent_class = get_parent_class($parent_class);

        $excluded_properties = array_keys(get_class_vars($parent_class));
        if ($grandparent_class) {
            $excluded_properties = array_merge($excluded_properties, array_keys(get_class_vars($grandparent_class)));
        }
        $excluded_properties[] = 'loaded';

        $count = count($properties);
        for ($i = 0; $i < $count; $i++) {
            if (in_array($properties[$i], $excluded_properties)) {
                unset($properties[$i]);
            }
        }
        $properties[] = 'debugData';
        return array_values($properties);
    }

    /**
     * Populates this object based on an incoming array generated by the
     * toArray() method.
     *
     * @param $array
     */
    public function fromArray($array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key) && $key != 'debugData' && $key != 'loaded') {
                $this->{$key} = $value;
            }
        }
        $this->loaded = true;
    }
}
