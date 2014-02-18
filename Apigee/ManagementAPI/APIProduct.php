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

use Apigee\Util\Cache as Cache;

/**
 * Abstracts the API Product object in the Management API and allows clients
 * to manipulate it.
 *
 * Write support is purely experimental and is not recommended.
 *
 * @author djohnson
 */
class APIProduct extends Base implements APIProductInterface
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
     * {@inheritDoc}
     */
    public function load($name = NULL, $response = NULL)
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
        $this->quota = isset($response['quota']) ? $response['quota'] : NULL;
        $this->quotaInterval = isset($response['quotaInterval']) ? $response['quotaInterval'] : NULL;
        $this->quotaTimeUnit = isset($response['quotaTimeUnit']) ? $response['quotaTimeUnit'] : NULL;
        $this->scopes = $response['scopes'];

        $this->loaded = TRUE;
    }

    /**
     * {@inheritDoc}
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
        $url = NULL;
        if ($this->modifiedBy) {
            $url = $this->name;
        }
        $this->post($url, $payload);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($name = NULL)
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
    public function isPublic($product = NULL)
    {
        if (!isset($product)) {
            if (isset($this->attributes['access']) && ($this->attributes['access'] == 'internal' || $this->attributes['access'] == 'private')) {
                return FALSE;
            }
        } else {
            foreach ($product['attributes'] as $attr) {
                if ($attr['name'] == 'access') {
                    return ($attr['value'] != 'internal' && $attr['value'] != 'private');
                }
            }
        }
        return TRUE;
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
            $this->get('?expand=true');
            $response = $this->responseObj;
            foreach ($response['apiProduct'] as $prod) {
                $product = new self($this->getConfig());
                $product->load(NULL, $prod);
                $api_products[] = $product;
            }
        }
        return $api_products;
    }

    /**
     * {@inheritDoc}
     */
    public function listProducts($show_nonpublic = FALSE)
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

    /* Accessors (getters/setters) */
    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAttributes()
    {
        $this->attributes = array();
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuotaLimit()
    {
        if (isset($this->attributes['developer.quota.limit'])) {
            return $this->attributes['developer.quota.limit'];
        } elseif (!empty($this->quota)) {
            return $this->quota;
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuotaInterval()
    {
        if (isset($this->attributes['developer.quota.interval'])) {
            return $this->attributes['developer.quota.interval'];
        } elseif (!empty($this->quotaInterval)) {
            return $this->quotaInterval;
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuotaTimeUnit()
    {
        if (isset($this->attributes['developer.quota.timeunit'])) {
            return $this->attributes['developer.quota.timeunit'];
        } elseif (!empty($this->quotaTimeUnit)) {
            return $this->quotaTimeUnit;
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        if (!empty($this->description)) {
            return $this->description;
        }
        if (isset($this->attributes['description'])) {
            return $this->attributes['description'];
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
     */
    public function addApiResource($resource)
    {
        $this->apiResources[] = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function removeApiResource($resource)
    { // was delApiResource
        $index = array_search($resource, $this->apiResources);
        if ($index !== FALSE) {
            unset($this->apiResources[$index]);
            // reindex this array to be sequential zero-based.
            $this->apiResources = array_values($this->apiResources);
        }
    }

    /**
     * {{@inheritDoc}}
     */
    public function getApiResources()
    {
        return $this->apiResources;
    }

    /**
     * {@inheritDoc}
     */
    public function getApprovalType()
    {
        return $this->approvalType;
    }

    /**
     * {@inheritDoc}
     */
    public function setApprovalType($type)
    {
        if ($type != 'auto' && $type != 'manual') {
            throw new \Exception('Invalid approval type ' . $type . '; allowed values are "auto" and "manual".'); // TODO: use custom exception class
        }
        $this->approvalType = $type;
    }

    //TODO: populate getters/setters for other properties


    /**
     * Initializes this object to a blank state.
     */
    protected function blankValues()
    {
        $this->apiResources = array();
        $this->approvalType = 'auto';
        $this->attributes = array();
        $this->createdAt = NULL;
        $this->createdBy = NULL;
        $this->modifiedAt = NULL;
        $this->modifiedBy = NULL;
        $this->description = '';
        $this->displayName = '';
        $this->environments = array();
        $this->name = '';
        $this->proxies = array();
        $this->quota = '';
        $this->quotaInterval = '';
        $this->quotaTimeUnit = '';
        $this->scopes = array();

        $this->loaded = FALSE;
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
        $this->loaded = TRUE;
    }

}