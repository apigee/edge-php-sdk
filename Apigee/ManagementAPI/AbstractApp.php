<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ParameterException;
use Apigee\Exceptions\ResponseException;

/**
 * Superclass of DeveloperApps and CompanyApps.
 *
 * @author djohnson
 */
abstract class AbstractApp extends Base
{

    /**
     * @var string
     * Contains 'read', 'write', or 'both' (empty is also valid).
     * This property doesn't appear to ever be used.
     */
    protected $accessType;
    /**
     * @var array
     * An array of API products with which the app is associated.
     */
    protected $apiProducts;
    /**
     * @var string.
     * The app family containing the app.
     * This property is read-only.
     */
    protected $appFamily;
    /**
     * @var string
     * GUID of this app.
     * This property is read-only.
     */
    protected $appId;
    /**
     * @var array
     * Name/value pairs used to extend the default app profile.
     * This is protected because Base wants to twiddle with it.
     */
    protected $attributes;
    /**
     * @var string
     * The callbackUrl is used by OAuth 2.0 authorization servers to
     * communicate authorization codes back to apps.
     */
    protected $callbackUrl;
    /**
     * @var int
     * Unix time when the app was created.
     * This property is read-only.
     */
    protected $createdAt;
    /**
     * @var string
     * Username of the Apigee developer who created the app.
     * This property is read-only.
     */
    protected $createdBy;
    /**
     * @var int
     * Unix time when the app was last modified.
     * This property is read-only.
     */
    protected $modifiedAt;
    /**
     * @var string
     * Username of the Apigee developer who last modified the app.
     * This property is read-only.
     */
    protected $modifiedBy;

    /**
     * @var string
     * The app name.
     * The primary key within this org/developer's app list.
     */
    protected $name;
    /**
     * @var array
     */
    protected $scopes;
    /**
     * @var string
     * Status of the app: 'approved' or 'unapproved'.
     */
    protected $status;
    /**
     * @var string
     * The description of the app.
     */
    protected $description;

    /**
     * @var array
     * The status of the consumer key for each API Product: 'approved' or 'pending'.
     * Each member of this array is itself an associative array, with keys of
     * 'apiproduct' and 'status'.
     */
    protected $credentialApiProducts;
    /**
     * @var string
     * The value of the consumer key for the app.
     */
    protected $consumerKey;
    /**
     * @var string
     * The value of the consumer secret for the app.
     */
    protected $consumerSecret;
    /**
     * @var array
     * The scope of the active credentials.
     */
    protected $credentialScopes;
    /**
     * @var string
     * The status of the consumer key for the app: 'approved' or 'pending'.
     */
    protected $credentialStatus;
    /**
     * @var array
     * Name/value pairs used to extend the default credential's profile.
     */
    protected $credentialAttributes;

    /**
     * @var int
     * Unix time when the credentials were issued.
     */
    protected $credentialIssuedAt;

    /**
     * @var int
     * Unix time when the credentials expire.
     */
    protected $credentialExpiresAt;

    /**
     * @var array
     * Used internally to compare an old API product with a new one.
     */
    protected $cachedApiProducts;

    /**
     * @var string
     * Used internally to point to the entity (Developer or Company) that owns
     * the app.
     */
    protected $ownerIdentifierField;

    /* Accessors (getters/setters) */

    /**
     * Returns the array of API products with which the app is associated.
     * @return array
     */
    public function getApiProducts()
    {
        return $this->apiProducts;
    }

    /**
     * Sets the array of API products with which the app is associated.
     * @param array
     */
    public function setApiProducts($products)
    {
        if (!is_array($products)) {
            $products = array($products);
        }
        $this->cachedApiProducts = $this->apiProducts;
        $this->apiProducts = $products;
    }

    /**
     * Returns the array of name/value pairs used to extend the default app
     * profile with which the app is associated.
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns true if the app attributes array contains $attr.
     * @param string $attr
     * @return bool
     */
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }

    /**
     * Returns the value of the specified app attribute, or null if the
     * attribute does not exist.
     * @param string $attr
     */
    public function getAttribute($attr)
    {
        return (array_key_exists($attr, $this->attributes) ? $this->attributes[$attr] : null);
    }

    /**
     * Sets the value of the app attribute.
     * @param string $attr
     * @param
     */
    public function setAttribute($attr, $value)
    {
        $this->attributes[$attr] = $value;
    }

    /**
     * Sets the app name.
     * @param string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the app name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the callback URL.
     * @param string
     */
    public function setCallbackUrl($url)
    {
        $this->callbackUrl = $url;
    }

    /**
     * Returns the app name.
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Sets the app description.
     * @param string
     */
    public function setDescription($descr)
    {
        $this->description = $descr;
        $this->attributes['description'] = $descr;
    }

    /**
     * Returns the app description.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the app access type as 'read', 'write', or 'both'.
     * @param string
     */
    public function setAccessType($type)
    {
        if ($type != 'read' && $type != 'write' && $type != 'both') {
            throw new ParameterException('Invalid access type ' . $type . '.');
        }
        $this->accessType = $type;
    }

    /**
     * Returns the app access type as 'read', 'write', or 'both'.
     * @return string
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * Returns the app status of the app: 'approved' or 'unapproved'.
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the app access type as 'read', 'write', or 'both'.
     * @param string
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the status of the consumer key for each API Product:
     * 'approved' or 'pending'.
     * Each member of this array is itself an associative array, with keys
     * of 'apiproduct' and 'status'.
     * @return array
     */
    public function getCredentialApiProducts()
    {
        return $this->credentialApiProducts;
    }

    /**
     * Sets the status of the consumer key for each API Product:
     * 'approved' or 'pending'.
     * Each member of this array is itself an associative array, with keys
     * of 'apiproduct' and 'status'.
     * @param array
     */
    protected function setCredentialApiProducts(array $list)
    {
        $this->credentialApiProducts = $list;
    }

    /**
     * Returns the value of the consumer key for the app.
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * Sets the value of the consumer key for the app.
     * @param string
     */
    public function setConsumerKey($key)
    {
        $this->consumerKey = $key;
    }

    /**
     * Returns the value of the consumer secret for the app.
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * Sets the value of the consumer secret for the app.
     * @param string
     */
    public function setConsumerSecret($secret)
    {
        $this->consumerSecret = $secret;
    }

    /**
     * Returns the scope(s) of the active credentials.
     * @return array
     */
    public function getCredentialScopes()
    {
        return $this->credentialScopes;
    }

    /**
     * Sets the value of the credential's scope.
     * @param array
     */
    protected function setCredentialScopes(array $scopes)
    {
        $this->credentialScopes = $scopes;
    }

    /**
     * Returns the status of the consumer key for the app: 'approved' or 'pending'.
     * @return string
     */
    public function getCredentialStatus()
    {
        return $this->credentialStatus;
    }

    /**
     * Sets the status of the consumer key for the app: 'approved' or 'pending'.
     * @param string
     */
    protected function setCredentialStatus($status)
    {
        $this->credentialStatus = $status;
    }

    /**
     * Returns the Unix time when the credentials were created.
     * @return integer
     */
    public function getCredentialIssueDate()
    {
        return $this->credentialIssuedAt;
    }

    /**
     * Sets the Unix time when the credentials were created.
     * @param integer
     */
    protected function setCredentialIssueDate($timestamp)
    {
        $this->credentialIssuedAt = intval($timestamp);
    }

    /**
     * Returns the Unix time when the credentials expire.
     * @return integer
     */
    public function getCredentialExpiryDate()
    {
        return $this->credentialExpiresAt;
    }

    /**
     * Sets the Unix time when the credentials expire.
     * @param integer
     */
    protected function setCredentialExpiryDate($timestamp)
    {
        $this->credentialExpiresAt = intval($timestamp);
    }

    /**
     * Returns the Unix time when the app was created.
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the Unix time when the app was created.
     * @param integer
     */
    protected function setCreatedAt($time_in_milliseconds)
    {
        $this->createdAt = floatval($time_in_milliseconds);
    }

    /**
     * Returns the username of the user who created the app.
     * Note that this is usually not the developer who owns the app, but
     * rather the user (usually an org-admin) which is logged into Edge.
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Sets the username of the user who created the app.
     * @param string
     */
    public function setCreatedBy($who)
    {
        $this->createdBy = $who;
    }

    /**
     * Returns the Unix time when the app was last modified.
     * @return integer
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Sets the Unix time when the app was last modified.
     * @param integer
     */
    protected function setModifiedAt($time_in_milliseconds)
    {
        $this->modifiedAt = $time_in_milliseconds;
    }

    /**
     * Returns the username of the user who last modified the app.
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Sets the username of the developer who last modified the app.
     * @param string
     */
    public function setModifiedBy($who)
    {
        $this->modifiedBy = $who;
    }

    /**
     * Returns the value of an attribute used to extend the
     * default credential's profile.
     * @param string
     * @return
     */
    public function getCredentialAttribute($attr_name)
    {
        if (isset($this->credentialAttributes[$attr_name])) {
            return $this->credentialAttributes[$attr_name];
        }
        return null;
    }

    /**
     * Sets a name/value pair used to extend the default credential's profile.
     * @param string
     * @param
     */
    public function setCredentialAttribute($name, $value)
    {
        $this->credentialAttributes[$name] = $value;
    }

    /**
     * Returns the array of name/value pairs used to extend the default
     * credential's profile.
     * @return array
     */
    public function getCredentialAttributes()
    {
        return $this->credentialAttributes;
    }

    /**
     * Clears the array of name/value pairs used to extend the default
     * credential's profile.
     */
    public function clearCredentialAttributes()
    {
        $this->credentialAttributes = array();
    }

    /**
     * Returns the UUID of this app.
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Sets the UUID of this app.
     * @param string
     */
    protected function setAppId($id)
    {
        $this->appId = $id;
    }

    /**
     * Returns the name of the app family containing the app, if any.
     * @return string
     */
    public function getAppFamily()
    {
        return $this->appFamily;
    }

    /**
     * Sets the name of the app family containing the app.
     * @param string
     */
    public function setAppFamily($family)
    {
        $this->appFamily = $family;
    }

    /**
     * Returns the scope of the app.
     * @return string
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Sets the scope of the app.
     * @param string
     */
    protected function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * Returns true if the $credentialApiproducts, $consumerKey, $consumerSecret,
     * $credentialScopes, and $credentialStatus properties are all set
     * to non-null values.
     * Otherwise, returns false.
     * @return bool
     */
    public function hasCredentialInfo()
    {
        $credential_fields = array(
            'credentialApiproducts',
            'consumerKey',
            'consumerSecret',
            'credentialScopes',
            'credentialStatus'
        );
        foreach ($credential_fields as $field) {
            if (!empty($this->$field)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sets the cached version of API products associated with this app. This
     * is useful when performing before-versus-after comparisons of API
     * Products, which is necessary when ensuring that Consumer Key/Secrets
     * remain unchanged.
     * @param array $cache
     */
    public function setApiProductCache(array $cache)
    {
        $this->cachedApiProducts = $cache;
    }

    /**
     * Loads a DeveloperApp/CompanyApp object with the contents of a raw
     * Management API response.
     *
     * @static
     * @param AbstractApp $obj
     * @param array $response
     */
    protected static function loadFromResponse(AbstractApp &$obj, array $response, $owner_identifier = null)
    {
        $obj->accessType = $response['accessType'];
        $obj->appFamily = (isset($response['appFamily']) ? $response['appFamily'] : null);
        $obj->appId = $response['appId'];
        $obj->callbackUrl = $response['callbackUrl'];
        $obj->createdAt = $response['createdAt'];
        $obj->createdBy = $response['createdBy'];
        $obj->modifiedAt = $response['lastModifiedAt'];
        $obj->modifiedBy = $response['lastModifiedBy'];
        $obj->name = $response['name'];
        $obj->scopes = $response['scopes'];
        $obj->status = $response['status'];

        $obj->readAttributes($response);

        if (!empty($response['description'])) {
            $obj->description = $response['description'];
        } elseif (isset($obj->attributes['description'])) {
            $obj->description = $obj->getAttribute('description');
        } else {
            $obj->description = null;
        }

        self::loadCredentials($obj, $response['credentials']);

        // Let subclasses twiddle here

        $obj::afterLoad($obj, $response, $owner_identifier);
    }

    /**
     * Reads the credentials array from the API response and sets object
     * properties.
     *
     * We try to find the approved credential with the most recent issued-at
     * timestamp that is not also expired. Failing that, we return the
     * earliest-issued credential in the list.
     *
     * @static
     * @param DeveloperApp $obj
     * @param $credentials
     */
    protected static function loadCredentials(AbstractApp &$obj, $credentials)
    {
        // Find the credential with the max issuedAt attribute which isn't expired.
        if (count($credentials) > 0) {
            $credential = null;
            // Sort credentials by issuedAt descending.
            usort($credentials, array(__CLASS__, 'sortCredentials'));
            // Look for the first member of the array that is approved.
            $m_now = time() * 1000;
            foreach ($credentials as $c) {
                if ($c['status'] == 'approved' && ($c['expiresAt'] == -1 || $c['expiresAt'] > $m_now)) {
                    $credential = $c;
                    break;
                }
            }
            // If none were approved, use the first member of the array.
            if (!isset($credential)) {
                $credential = $credentials[0];
            }
            $obj->credentialApiProducts = $credential['apiProducts'];
            $obj->consumerKey = $credential['consumerKey'];
            $obj->consumerSecret = $credential['consumerSecret'];
            $obj->credentialScopes = $credential['scopes'];
            $obj->credentialStatus = $credential['status'];
            $obj->credentialExpiresAt = $credential['expiresAt'];
            $obj->credentialIssuedAt = 0;
            if (isset($credential['issuedAt'])) {
                $obj->credentialIssuedAt = $credential['issuedAt'];
            } elseif (!empty($credential['attributes'])) {
                foreach ($credential['attributes'] as $attrib) {
                    if ($attrib['name'] == 'create_date') {
                        $obj->credentialIssuedAt = intval($attrib['value']) * 1000;
                        break;
                    }
                }
            }

            $obj->credentialAttributes = array();
            foreach ($credential['attributes'] as $attribute) {
                $obj->credentialAttributes[$attribute['name']] = $attribute['value'];
            }

            // Some apps may be misconfigured and need to be populated with their apiproducts based on credential.
            if (count($obj->apiProducts) == 0) {
                $obj->apiProducts = array();
                foreach ($obj->credentialApiProducts as $product) {
                    $obj->apiProducts[] = $product['apiproduct'];
                }
            }
        }
    }

    /**
     * Finds the overall status of this app. First check app status, then
     * credential status, then credential->apiproduct status. If any are
     * 'revoked', return 'revoked'; if any are 'pending', return 'pending;
     * else return 'approved'.
     *
     * @return string
     */
    public function getOverallStatus()
    {
        static $statuses;
        if (!isset($statuses)) {
            $statuses = array(
                'approved' => 0,
                'pending' => 1,
                'revoked' => 2
            );
        }
        $app_status = (array_key_exists($this->status, $statuses) ? $statuses[$this->status] : 0);
        $cred_status = (array_key_exists($this->credentialStatus, $statuses) ? $statuses[$this->credentialStatus] : 0);

        $current_status = max($app_status, $cred_status);
        if ($current_status < 2) {
            foreach ($this->credentialApiProducts as $api_product) {
                if (!array_key_exists($api_product['status'], $statuses)) {
                    continue;
                }
                $current_status = max($current_status, $statuses[$api_product['status']]);
                if ($current_status == 2) {
                    break;
                }
            }
        }
        return array_search($current_status, $statuses);
    }

    /**
     * Populates this object with information retrieved from the Management API.
     *
     * If $name is not passed, $this->name is used.
     *
     * @param null|string $name
     */
    public function load($name = null)
    {
        $name = $name ? : $this->name;
        $this->get(rawurlencode($name));
        $response = $this->responseObj;
        self::loadFromResponse($this, $response, $this->{$this->ownerIdentifierField});
    }

    /**
     * Checks to see if an app with the given name exists for this
     * developer/company.
     *
     * If $name is not passed, $this->name is used.
     *
     * @param null|string $name
     * @return bool
     */
    public function validate($name = null)
    {
        $name = $name ? : $this->name;
        $cached_logger = null;
        // Make sure that errors are not logged by replacing the logger with a
        // dummy that routes errors to /dev/null
        if (!(self::$logger instanceof \Psr\Log\NullLogger)) {
            $cached_logger = self::$logger;
            self::$logger = new \Psr\Log\NullLogger();
        }
        try {
            $this->get(rawurlencode($name));
            $app_exists = true;
        } catch (ResponseException $e) {
            $app_exists = false;
        }
        if (!empty($cached_logger)) {
            self::$logger = $cached_logger;
        }
        return $app_exists;
    }

    /**
     * Determines difference between cached version of API Products array for
     * this app and current version. Returned object enumerates which API
     * Products are due for removal (if any), and which should be added.
     *
     * @return \stdClass
     */
    protected function apiProductsDiff()
    {
        $cache = $this->cachedApiProducts;
        for ($i = 0; $i < count($cache); $i++) {
            if (is_array($cache[$i]) && array_key_exists('apiproduct', $cache[$i])) {
                $cache[$i] = $cache[$i]['apiproduct'];
            }
        }
        // Find apiproducts that we will have to delete.  These are found in the
        // cached list but not in the live list.
        $to_delete = array();
        foreach ($cache as $api_product) {
            if (!in_array($api_product, $this->apiProducts)) {
                $to_delete[] = $api_product;
            }
        }
        // Find apiproducts that we will have to add. These are found in the
        // live list but not in the cached list.
        $to_add = array();
        foreach ($this->apiProducts as $api_product) {
            if (!in_array($api_product, $cache)) {
                $to_add[] = $api_product;
            }
        }
        return (object)array('to_delete' => $to_delete, 'to_add' => $to_add);
    }

    /**
     * Write this app's data to the Management API, preserving client key/secret.
     *
     * The function attempts to determine if this should be an insert or an
     * update automagically. However, when $force_update is set to true, this
     * determination is short-circuited and an update is assumed.
     *
     * @param bool $force_update
     */
    public function save($force_update = false)
    {
        $is_update = ($force_update || $this->modifiedAt);

        $payload = array(
            'accessType' => $this->getAccessType(),
            'name' => $this->getName(),
            'callbackUrl' => $this->getCallbackUrl()
        );
        // Make sure DisplayName attribute is saved. It seems to be required or
        // expected on the Enterprise UI.
        if (!array_key_exists('DisplayName', $this->attributes)) {
            $display_name = $this->name;
            if (strpos($display_name, ' ') === false) {
                $display_name = ucwords(str_replace(array('_', '-'), ' ', $display_name));
            }
            $this->attributes['DisplayName'] = $display_name;
        }
        // Set other attributes that Enterprise UI sets by default.
        $this->attributes['lastModified'] = gmdate('Y-m-d H:i A');
        $this->attributes['lastModifier'] = $this->config->user_mail;
        if (!$is_update && !array_key_exists('creationDate', $this->attributes)) {
            $this->attributes['creationDate'] = gmdate('Y-m-d H:i A');
        }

        $this->writeAttributes($payload);

        $this->alterAttributes($payload);

        $url = null;
        if ($is_update) {
            $url = rawurlencode($this->getName());
        }
        $created_new_key = false;
        // NOTE: On update, we send APIProduct information separately from other
        // fields, in order to preserve the client-key/secret pair. Updates to
        // APIProducts must be made separately against the app's client-key,
        // rather than just against the app. Additionally, deletions from the
        // APIProducts list must be handled separately from additions.
        $consumer_key = $this->getConsumerKey();
        if ($is_update && !empty($consumer_key)) {
            $key_uri = ltrim("$url/keys/", '/') . rawurlencode($consumer_key);
            $diff = $this->apiProductsDiff();
            // api-product deletions must happen one-by-one.
            foreach ($diff->to_delete as $api_product) {
                $delete_uri = "$key_uri/apiproducts/" . rawurlencode($api_product);
                $this->http_delete($delete_uri);
            }
            // api-product additions can happen in a batch.
            if (count($diff->to_add) > 0) {
                $this->post($key_uri, array('apiProducts' => $diff->to_add));
            }
        } else {
            $payload['apiProducts'] = $this->getApiProducts();
        }

        // Let subclasses fiddle with the payload here
        self::preSave($payload, $this);

        $this->post($url, $payload);
        $response = $this->responseObj;

        $credential_response = null;

        if (count($response['credentials']) > 0) {
            $current_credential = null;
            // Find credential -- it should have the maximum issuedAt date.
            $max_issued_at = -1;
            $credential_index = null;
            foreach ($response['credentials'] as $i => $cred) {
                $issued_at = (array_key_exists('issuedAt', $cred) ? intval($cred['issuedAt']) : 0);
                if ($issued_at == 0 && array_key_exists('attributes', $cred)) {
                    foreach ($cred['attributes'] as $attrib) {
                        if ($attrib['name'] == 'create_date') {
                            $issued_at = $attrib['value'] * 1000;
                            break;
                        }
                    }
                }
                if ($max_issued_at == -1 || $issued_at > $max_issued_at) {
                    $max_issued_at = $issued_at;
                    $credential_index = $i;
                }
            }
            if (isset($credential_index)) {
                $current_credential =& $response['credentials'][$credential_index];
                $consumer_key = $current_credential['consumerKey'];
            }

            // If any credential attributes are present, save them
            if ($current_credential && count($this->credentialAttributes) > 0) {
                $payload = $current_credential;
                $payload['attributes'] = array();
                foreach ($this->credentialAttributes as $name => $val) {
                    $payload['attributes'][] = array('name' => $name, 'value' => $val);
                }
                // Payload only has to send bare minimum for update.
                unset($payload['apiProducts'], $payload['scopes'], $payload['status']);
                $url = rawurlencode($this->name) . '/keys/' . $consumer_key;

                // Let subclasses fiddle with the payload here
                self::preSaveCredential($payload, $current_credential, $response);
                // POST that sucker!
                $this->post($url, $payload);
                $credential_response = $this->responseObj;
            }
        }

        // Refresh our fields so we get latest autogenerated data such as consumer key etc.
        self::loadFromResponse($this, $response, $this->{$this->ownerIdentifierField});
        // If we updated the key's metadata, merge in our response data.
        if (isset($credential_response)) {
            $this->credentialApiProducts = $credential_response['apiProducts'];
            $this->credentialAttributes = $credential_response['attributes'];
            $this->consumerKey = $credential_response['consumerKey'];
            $this->consumerSecret = $credential_response['consumerSecret'];
            $this->credentialStatus = $credential_response['status'];
        }
    }

    /**
     * Usort callback to sort credentials by create date (most recent first).
     *
     * @static
     * @param $a
     * @param $b
     * @return int
     */
    protected static function sortCredentials($a, $b)
    {
        $a_issued_at = 0;
        if (array_key_exists('issuedAt', $a)) {
            $a_issued_at = $a['issuedAt'];
        } elseif (!empty($a['attributes'])) {
            foreach ($a['attributes'] as $attrib) {
                if ($attrib['name'] == 'create_date') {
                    $a_issued_at = intval($attrib['value']) * 1000;
                    break;
                }
            }
        }
        $b_issued_at = 0;
        if (array_key_exists('issuedAt', $b)) {
            $b_issued_at = $b['issuedAt'];
        } elseif (!empty($b['attributes'])) {
            foreach ($b['attributes'] as $attrib) {
                if ($attrib['name'] == 'create_date') {
                    $b_issued_at = intval($attrib['value']) * 1000;
                    break;
                }
            }
        }
        if ($a_issued_at == $b_issued_at) {
            return 0;
        }
        return ($a_issued_at > $b_issued_at) ? -1 : 1;
    }

    /**
     * Approves or revokes a client key for an app, and optionally also for all
     * API Products associated with that app.
     *
     * @param mixed $status
     *        May be true, false, 0, 1, 'approve' or 'revoke'.
     * @param bool $also_set_apiproduct
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function setKeyStatus($status, $also_set_apiproduct = true)
    {
        if ($status === 0 || $status === false) {
            $status = 'revoke';
        } elseif ($status === 1 || $status === true) {
            $status = 'approve';
        } elseif ($status != 'revoke' && $status != 'approve') {
            throw new ParameterException('Invalid key status ' . $status);
        }

        if (strlen($this->getName()) == 0) {
            throw new ParameterException('No app specified; cannot set key status.');
        }
        if (strlen($this->getConsumerKey()) == 0) {
            throw new ParameterException('App has no consumer key; cannot set key status.');
        }
        $base_url = rawurlencode($this->getName()) . '/keys/' . rawurlencode($this->getConsumerKey());
        // First, approve or revoke the overall key for the app.
        $app_url = $base_url . '?action=' . $status;
        $this->post($app_url, '');

        // Now, unless specified otherwise, approve or revoke the same key for all
        // associated API Products.
        if ($also_set_apiproduct) {
            $api_products = $this->getApiProducts();
            if (!empty($api_products)) {
                foreach ($api_products as $api_product) {
                    $product_url = $base_url . '/apiproducts/' . rawurlencode($api_product) . '?action=' . $status;
                    $this->post($product_url, '');
                }
            }
        }
    }

    /**
     * Deletes an app from the Management API.
     *
     * If $name is not passed, $this->name is used.
     *
     * @param null|string $name
     */
    public function delete($name = null)
    {
        $name = $name ? : $this->name;
        $this->http_delete(rawurlencode($name));
        if ($name == $this->getName()) {
            $this->blankValues();
        }
    }

    /**
     * Returns names of all apps belonging to this developer/company.
     *
     * @return array
     */
    public function getList()
    {
        $this->get();
        return $this->responseObj;
    }

    /**
     * Creates a key/secret pair for this app against its component APIProducts.
     *
     * @param string $consumer_key
     * @param string $consumer_secret
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function createKey($consumer_key, $consumer_secret)
    {
        if (strlen($consumer_key) < 5 || strlen($consumer_secret) < 5) {
            throw new ParameterException('Consumer Key and Consumer Secret must each be at least 5 characters long.');
        }
        // This is by nature a two-step process. API Products cannot be added
        // to a new key at the time of key creation, for some reason.
        $payload = array(
            'consumerKey' => $consumer_key,
            'consumerSecret' => $consumer_secret,
            'scopes' => $this->getCredentialScopes(),
        );

        $url = rawurlencode($this->name) . '/keys/create';
        $this->post($url, $payload);

        $new_credential = $this->responseObj;
        // We now have the new key, sans apiproducts. Let us add them now.
        $new_credential['apiProducts'] = array();
        foreach($this->getCredentialApiProducts() as $apiproduct) {
            $new_credential['apiProducts'][] = $apiproduct['apiproduct'];
        }
        $new_credential['attributes'] = array();
        foreach ($this->getCredentialAttributes() as $name => $value) {
            if ($name == 'create_date') {
                continue;
            }
            $new_credential['attributes'][] = array('name' => $name, 'value' => $value);
        }
        $new_credential['attributes'][] = array('name' => 'create_date', 'value' => (string)time());
        $key = $new_credential['consumerKey'];
        $url = rawurlencode($this->getName()) . '/keys/' . rawurlencode($key);

        $this->post($url, $new_credential);
        $credential = $this->responseObj;

        if ($credential['status'] == 'approved' || empty($this->consumerKey)) {
            // Update $this with new credential info ONLY if the key is auto-approved
            // or if there are no keys yet.
            $this->setCredentialApiProducts($credential['apiProducts']);
            $this->setConsumerKey($credential['consumerKey']);
            $this->setConsumerSecret($credential['consumerSecret']);
            $this->setCredentialScopes($credential['scopes']);
            $this->setCredentialStatus($credential['status']);
            if (array_key_exists('issuedAt', $credential)) {
                $this->setCredentialIssueDate($credential['issuedAt']);
            }
            $this->setCredentialExpiryDate($credential['expiresAt']);
            $this->clearCredentialAttributes();
            foreach ($credential['attributes'] as $attribute) {
                $this->setCredentialAttribute($attribute['name'], $attribute['value']);
            }
        }
    }

    /**
     * Deletes an attribute from an app. Returns true if successful, else false.
     *
     * @param string $attr_name
     * @return bool
     */
    public function deleteAttribute($attr_name)
    {
        $cached_logger = null;
        // Make sure that errors are not logged by replacing the logger with a
        // dummy that routes errors to /dev/null
        if (!(self::$logger instanceof \Psr\Log\NullLogger)) {
            $cached_logger = self::$logger;
            self::$logger = new \Psr\Log\NullLogger();
        }
        $returnVal = false;
        $url = rawurlencode($this->getName()) . '/attributes/' . rawurlencode($attr_name);
        try {
            $this->http_delete($url);
            $returnVal = true;
        } catch (ResponseException $e) {
        }
        // Restore logger to its previous state
        if (!empty($cached_logger)) {
            self::$logger = $cached_logger;
        }
        if ($returnVal && array_key_exists($attr_name, $this->attributes)) {
            unset($this->attributes[$attr_name]);
        }
        return $returnVal;
    }

    /**
     * Deletes an attribute from the active credential of an app. Returns true if successful, else false.
     *
     * @param $attr_name
     */
    public function deleteCredentialAttribute($attr_name)
    {
        $cached_logger = null;
        // Make sure that errors are not logged by replacing the logger with a
        // dummy that routes errors to /dev/null
        if (!(self::$logger instanceof \Psr\Log\NullLogger)) {
            $cached_logger = self::$logger;
            self::$logger = new \Psr\Log\NullLogger();
        }
        $returnVal = false;
        $url = rawurlencode($this->getName()) . '/keys/' . rawurlencode($this->getConsumerKey()) . '/attributes/' . rawurlencode($attr_name);
        try {
            $this->http_delete($url);
            $returnVal = true;
        } catch (ResponseException $e) {
        }
        // Restore logger to its previous state
        if (!empty($cached_logger)) {
            self::$logger = $cached_logger;
        }
        if ($returnVal && array_key_exists($attr_name, $this->credentialAttributes)) {
            unset($this->credentialAttributes[$attr_name]);
        }
        return $returnVal;
    }

    /**
     * Deletes a given key from an app.
     *
     * @param string $consumer_key
     */
    public function deleteKey($consumer_key)
    {
        $url = rawurlencode($this->getName()) . '/keys/' . rawurlencode($consumer_key);
        try {
            $this->http_delete($url);
        } catch (\Apigee\Exceptions\ResponseException $e) {
        }
        // We ignore whether or not the delete was successful. Either way, we can
        // be sure it doesn't exist now, if it did before.

        // Reload app to repopulate credential fields.
        $this->load();
    }

    /**
     * Restores this object to its pristine state.
     */
    public function blankValues()
    {
        $this->accessType = 'read';
        $this->apiProducts = array();
        $this->appFamily = null;
        $this->appId = null;
        $this->attributes = array();
        $this->callbackUrl = '';
        $this->createdAt = null;
        $this->createdBy = null;
        $this->modifiedAt = null;
        $this->modifiedBy = null;
        $this->name = null;
        $this->scopes = array();
        $this->status = 'pending';
        $this->description = null;

        $this->credentialApiProducts = array();
        $this->consumerKey = null;
        $this->consumerSecret = null;
        $this->credentialScopes = array();
        $this->credentialStatus = null;
        $this->credentialAttributes = array();
        $this->credentialIssuedAt = 0;
        $this->credentialExpiresAt = -1;

        $this->cachedApiProducts = array();
    }

    /**
     * Turns this object's properties into an array for external use.
     *
     * @return array
     */
    public function toArray($include_debug_data = true)
    {
        $output = array();
        foreach ($this->getAppProperties() as $property) {
            switch ($property) {
                case 'debugData':
                    $output[$property] = $include_debug_data ? $this->getDebugData() : null;
                    break;
                case 'overallStatus':
                    $output[$property] = $this->getOverallStatus();
                    break;
                default:
                    $output[$property] = $this->$property;
                    break;
            }
        }
        return $output;
    }

    /**
     * Returns an array of all property names that can be returned
     * from a call to self::toArray().
     *
     * @return array
     */
    public function getAppProperties($class = __CLASS__)
    {
        $properties = array_keys(get_class_vars($class));

        $parent_class = get_parent_class();
        $grandparent_class = get_parent_class($parent_class);

        $excluded_properties = array_keys(get_class_vars($parent_class));
        if ($grandparent_class) {
            $excluded_properties += array_keys(get_class_vars($grandparent_class));
            $gg_class = get_parent_class($grandparent_class);
            if ($gg_class) {
                $excluded_properties += array_keys(get_class_vars($gg_class));
            }
        }
        $excluded_properties[] = 'cachedApiProducts';
        $excluded_properties[] = 'baseUrl';
        $excluded_properties[] = 'ownerIdentifierField';

        $count = count($properties);
        for ($i = 0; $i < $count; $i++) {
            if (in_array($properties[$i], $excluded_properties)) {
                unset($properties[$i]);
            }
        }
        $properties[] = 'debugData';
        $properties[] = 'overallStatus';
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
            if (property_exists($this, $key) && $key != 'debugData') {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Dummy placeholder to allow subclasses to modify the App object as it is
     * finishing the load process.
     *
     * @param AbstractApp $obj
     * @param array $response
     * @param string|null $owner_identifier
     */
    public static function afterLoad(AbstractApp &$obj, array $response, $owner_identifier)
    {
        // Do Nothing
    }

    /**
     * Dummy placeholder to allow subclasses to modify the payload of the
     * app-save call right before it is invoked.
     *
     * @param array $payload
     * @param AbstractApp $obj
     */
    protected static function preSave(array &$payload, AbstractApp $obj)
    {
        // Do Nothing
    }

    /**
     * Dummy placeholder to allow subclasses to modify the payload of the
     * credential-save call right before it is invoked.
     *
     * @param array $payload
     * @param array $credential
     * @param array $kms_response
     */
    protected static function preSaveCredential(array &$payload, array $credential, array $kms_response)
    {
        // Do Nothing
    }

    /**
     * Allows subclasses to change or augment attribute name-value pairs before
     * an app is saved.
     *
     * @param array $payload
     */
    protected function alterAttributes(array &$payload)
    {

    }
}
