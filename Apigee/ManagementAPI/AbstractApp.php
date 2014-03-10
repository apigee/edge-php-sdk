<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ParameterException as ParameterException;

/**
 * Superclass of DeveloperApps and CompanyApps.
 *
 * @author djohnson
 */
abstract class AbstractApp extends Base implements AppInterface
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
     * {@inheritDOc}
     */
    public function getApiProducts()
    {
        return $this->apiProducts;
    }

    /**
     * {@inheritDOc}
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
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($attr)
    {
        return (array_key_exists($attr, $this->attributes) ? $this->attributes[$attr] : null);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attr, $value)
    {
        $this->attributes[$attr] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function setCallbackUrl($url)
    {
        $this->callbackUrl = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($descr)
    {
        $this->description = $descr;
        $this->attributes['description'] = $descr;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function setAccessType($type)
    {
        if ($type != 'read' && $type != 'write' && $type != 'both') {
            throw new ParameterException('Invalid access type ' . $type . '.');
        }
        $this->accessType = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDOc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDOc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Sets the username of the developer who created the app.
     * @param string
     */
    public function setCreatedBy($who)
    {
        $this->createdBy = $who;
    }

    /**
     * {@inheritDOc}
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
     * {@inheritDoc}
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
     * {@inheritDOc}
     */
    public function getCredentialAttribute($attr_name)
    {
        if (isset($this->credentialAttributes[$attr_name])) {
            return $this->credentialAttributes[$attr_name];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setCredentialAttribute($name, $value)
    {
        $this->credentialAttributes[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentialAttributes()
    {
        return $this->credentialAttributes;
    }

    /**
     * {@inheritDOc}
     */
    public function clearCredentialAttributes()
    {
        $this->credentialAttributes = array();
    }

    /**
     * {@inheritDoc}
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Sets the GUID of this app.
     * @param string
     */
    protected function setAppId($id)
    {
        $this->appId = $id;
    }


    /**
     * {@inheritDoc}
     */
    public function getAppFamily()
    {
        return $this->appFamily;
    }

    /**
     * {@inheritDoc}
     */
    public function setAppFamily($family)
    {
        $this->appFamily = $family;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDOc}
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

    public function setApiProductCache(array $cache)
    {
        $this->cachedApiProducts = $cache;
    }

    // TODO: write other getters/setters

    /**
     * Loads a DeveloperApp/CompanyApp object with the contents of a raw
     * Management API response.
     *
     * This must be public because DeveloperApps should be able to invoke it
     * on CompanyApps.
     *
     * @static
     * @param AppInterface $obj
     * @param array $response
     */
    protected static function loadFromResponse(AppInterface &$obj, array $response, $owner_identifier = null)
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
     * {@inheritDoc}
     */
    public function load($name = null)
    {
        $name = $name ? : $this->name;
        $this->get(rawurlencode($name));
        $response = $this->responseObj;
        self::loadFromResponse($this, $response, $this->{$this->ownerIdentifierField});
    }

    /**
     * {@inheritDoc}
     */
    public function validate($name = null)
    {
        $name = $name ? : $this->name;
        try {
            $this->get(rawurlencode($name));
            return false;
        } catch (\Apigee\Exceptions\ResponseException $e) {
        }
        return true;
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
     * {@inheritDoc}
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
            $created_new_key = true;
        }

        // Let subclasses fiddle with the payload here
        self::preSave($payload, $this);

        $this->post($url, $payload);
        $response = $this->responseObj;

        $credential_response = null;

        if (count($response['credentials']) > 0) {
            $credential_attributes = array();
            $current_credential = null;
            // Find credential -- it should have the maximum issuedAt date.
            $max_issued_at = -1;
            $credential_index = null;
            foreach ($response['credentials'] as $i => $cred) {
                $issued_at = (array_key_exists('issuedAt', $cred) ? intval($cred['issuedAt']) : 0);
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
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
     */
    public function getList()
    {
        $this->get();
        return $this->responseObj;
    }

    /**
     * {@inheritDoc}
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
        $new_credential['apiProducts'] = $this->getCredentialApiProducts();
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
            $this->setCredentialIssueDate($credential['issuedAt']);
            $this->setCredentialExpiryDate($credential['expiresAt']);
            $this->clearCredentialAttributes();
            foreach ($credential['attributes'] as $attribute) {
                $this->setCredentialAttribute($attribute['name'], $attribute['value']);
            }
        }
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
    public function toArray()
    {
        $output = array();
        foreach (self::getAppProperties() as $property) {
            switch ($property) {
                case 'debugData':
                    $output[$property] = $this->getDebugData();
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
    public static function getAppProperties()
    {
        $properties = array_keys(get_class_vars(__CLASS__));

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
     * @param AppInterface $obj
     * @param array $response
     * @param string|null $owner_identifier
     */
    public static function afterLoad(AppInterface &$obj, array $response, $owner_identifier)
    {
        // Do Nothing
    }

    /**
     * Dummy placeholder to allow subclasses to modify the payload of the
     * app-save call right before it is invoked.
     *
     * @param array $payload
     * @param AppInterface $obj
     */
    protected static function preSave(array &$payload, AppInterface $obj)
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

    protected function alterAttributes(array &$payload)
    {

    }
}
