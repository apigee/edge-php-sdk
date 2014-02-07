<?php
/**
 * @file
 * Abstracts the Developer App object in the Management API and allows clients
 * to manipulate it.
 *
 * @author djohnson
 */

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ParameterException as ParameterException;

/**
 * Abstracts the Developer App object in the Management API and allows clients
 * to manipulate it.
 *
 * @author djohnson
 */
class DeveloperApp extends Base implements DeveloperAppInterface
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
     * The developer_id attribute of the developer who
     * owns this app.
     * This property is read-only.
     */
    protected $developerId;
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
     * @var string
     * The email address of the developer who created the app.
     */
    protected $developer;
    /**
     * @var array
     * Used internally to compare an old API product with a new one.
     */
    protected $cachedApiProducts;

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
     * {@inheritDOc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritDOc}
     */
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }

    /**
     * {@inheritDOc}
     */
    public function getAttribute($attr)
    {
        return (array_key_exists($attr, $this->attributes) ? $this->attributes[$attr] : NULL);
    }

    /**
     * {@inheritDOc}
     */
    public function setAttribute($attr, $value)
    {
        $this->attributes[$attr] = $value;
    }

    /**
     * {@inheritDOc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDOc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDOc}
     */
    public function setCallbackUrl($url)
    {
        $this->callbackUrl = $url;
    }

    /**
     * {@inheritDOc}
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * {@inheritDOc}
     */
    public function setDescription($descr)
    {
        $this->description = $descr;
        $this->attributes['description'] = $descr;
    }

    /**
     * {@inheritDOc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritDOc}
     */
    public function setAccessType($type)
    {
        if ($type != 'read' && $type != 'write' && $type != 'both') {
            throw new ParameterException('Invalid access type ' . $type . '.');
        }
        $this->accessType = $type;
    }

    /**
     * {@inheritDOc}
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * {@inheritDOc}
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
     * {@inheritDOc}
     */
    public function getDeveloperId()
    {
        return $this->developerId;
    }

    /**
     * {@inheritDOc}
     */
    public function getDeveloperMail()
    {
        return $this->developer;
    }

    /**
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
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
        return NULL;
    }

    /**
     * {@inheritDOc}
     */
    public function setCredentialAttribute($name, $value)
    {
        $this->credentialAttributes[$name] = $value;
    }

    /**
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
     */
    public function getAppFamily()
    {
        return $this->appFamily;
    }

    /**
     * {@inheritDOc}
     */
    public function setAppFamily($family)
    {
        $this->appFamily = $family;
    }

    /**
     * {@inheritDOc}
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
                return TRUE;
            }
        }
        return FALSE;
    }

    public function setApiProductCache(array $cache)
    {
        $this->cachedApiProducts = $cache;
    }

    // TODO: write other getters/setters


    /**
     * Initializes this object
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param mixed $developer
     */
    public function __construct(\Apigee\Util\OrgConfig $config, $developer)
    {
        if ($developer instanceof DeveloperInterface) {
            $this->developer = $developer->getEmail();
        } else {
            // $developer may be either an email or a developerId.
            $this->developer = $developer;
        }
        $baseUrl = '/o/' . rawurlencode($config->orgName) . '/developers/' . $this->developer . '/apps';
        $this->init($config, $baseUrl);
        $this->blankValues();
    }

    /**
     * Loads a DeveloperApp object with the contents of a raw Management API
     * response.
     *
     * @static
     * @param DeveloperApp $obj
     * @param array $response
     */
    protected static function loadFromResponse(DeveloperApp &$obj, array $response, $developer_mail = NULL)
    {
        $obj->accessType = $response['accessType'];
        $obj->appFamily = (isset($response['appFamily']) ? $response['appFamily'] : NULL);
        $obj->appId = $response['appId'];
        $obj->callbackUrl = $response['callbackUrl'];
        $obj->createdAt = $response['createdAt'];
        $obj->createdBy = $response['createdBy'];
        $obj->modifiedAt = $response['lastModifiedAt'];
        $obj->modifiedBy = $response['developerId'];
        $obj->name = $response['name'];
        $obj->scopes = $response['scopes'];
        $obj->status = $response['status'];
        $obj->developerId = $response['developerId'];
        if (!empty($developer_mail)) {
            $obj->developer = $developer_mail;
        } elseif (!preg_match('!^[^@]+@[^@]+$!', $obj->developer)) {
            $obj->developer = $obj->getDeveloperMailById($response['developerId']);
        }

        $obj->readAttributes($response);

        if (!empty($response['description'])) {
            $obj->description = $response['description'];
        } elseif (isset($obj->attributes['description'])) {
            $obj->description = $obj->getAttribute('description');
        } else {
            $obj->description = NULL;
        }

        self::loadCredentials($obj, $response['credentials']);

        // Let subclasses twiddle here
        self::afterLoad($obj, $response);
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
    protected static function loadCredentials(DeveloperApp &$obj, $credentials)
    {
        // Find the credential with the max issuedAt attribute which isn't expired.
        if (count($credentials) > 0) {
            $credential = NULL;
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
            }
            elseif (!empty($credential['attributes'])) {
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
     * {@inheritDOc}
     */
    public function load($name = NULL)
    {
        $name = $name ? : $this->name;
        $this->get(rawurlencode($name));
        $response = $this->responseObj;
        self::loadFromResponse($this, $response, $this->developer);
    }

    /**
     * {@inheritDOc}
     */
    public function validate($name = NULL)
    {
        $name = $name ? : $this->name;
        try {
            $this->get(rawurlencode($name));
            return FALSE;
        } catch (\Apigee\Exceptions\ResponseException $e) {
        }
        return TRUE;
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
    public function save($force_update = FALSE)
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
            if (strpos($display_name, ' ') === FALSE) {
                $display_name = ucwords(str_replace(array('_', '-'), ' ', $display_name));
            }
            $this->attributes['DisplayName'] = $display_name;
        }
        // Set other attributes that Enterprise UI sets by default.
        $this->attributes['Developer'] = $this->developer;
        $this->attributes['lastModified'] = gmdate('Y-m-d H:i A');
        $this->attributes['lastModifier'] = $this->config->user_mail;
        if (!$is_update && !array_key_exists('creationDate', $this->attributes)) {
            $this->attributes['creationDate'] = gmdate('Y-m-d H:i A');
        }

        $this->writeAttributes($payload);

        $url = NULL;
        if ($is_update) {
            $url = rawurlencode($this->getName());
        }
        $created_new_key = FALSE;
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
            $created_new_key = TRUE;
        }

        // Let subclasses fiddle with the payload here
        self::preSave($payload, $this);

        $this->post($url, $payload);
        $response = $this->responseObj;

        $credential_response = NULL;

        if (count($response['credentials']) > 0) {
            $credential_attributes = array();
            $current_credential = NULL;
            // Find credential -- it should have the maximum issuedAt date.
            $max_issued_at = -1;
            $credential_index = NULL;
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
        self::loadFromResponse($this, $response, $this->developer);
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
        }
        elseif (!empty($a['attributes'])) {
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
        }
        elseif (!empty($b['attributes'])) {
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
    public function setKeyStatus($status, $also_set_apiproduct = TRUE)
    {
        if ($status === 0 || $status === FALSE) {
            $status = 'revoke';
        } elseif ($status === 1 || $status === TRUE) {
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
    public function delete($name = NULL)
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
     * {@inheritDOc}
     */
    public function getListDetail($developer_mail = NULL)
    {
        $developer_mail = $developer_mail ? : $this->developer;

        $this->setBaseUrl('/o/' . rawurlencode($this->config->orgName) . '/developers/' . rawurlencode($developer_mail) . '/apps');

        $this->get('?expand=true');
        $list = $this->responseObj;
        $this->restoreBaseUrl();

        $app_list = array();
        if (!array_key_exists('app', $list) || empty($list['app'])) {
            return $app_list;
        }
        foreach ($list['app'] as $response) {
            $app = new DeveloperApp($this->getConfig(), $developer_mail);
            self::loadFromResponse($app, $response, $developer_mail);
            $app_list[] = $app;
        }
        return $app_list;
    }

    /**
     * {@inheritDOc}
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
     * {@inheritDOc}
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
     * {@inheritDOc}
     */
    public function listAllOrgApps()
    {
        $url = '/o/' . rawurlencode($this->config->orgName);
        $this->setBaseUrl($url);
        $this->get('apps?expand=true');
        $response = $this->responseObj;
        $this->restoreBaseUrl();
        $app_list = array();
        foreach ($response['app'] as $app_detail) {
            $developer = $this->getDeveloperMailById($app_detail['developerId']);
            $app = new self($this->config, $developer);
            self::loadFromResponse($app, $app_detail);
            $app_list[] = $app;
        }
        return $app_list;
    }

    /**
     * Loads a developer app, given its appId (which is a UUID).
     *
     * Normally you'd find an app by listing its developer's apps and looking
     * for the name you want. However, if you already know the app's unique id,
     * you can load without knowing its developer.
     *
     * If you pass TRUE as the second parameter here, the DeveloperApp object
     * will be changed so that it pulls apps from this developer by default.
     *
     * @param string $appId
     * @param bool $reset_developer
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function loadByAppId($appId, $reset_developer = FALSE)
    {
        if (!preg_match('!^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$!', $appId)) {
            throw new ParameterException('Invalid UUID passed as appId.');
        }

        $url = '/o/' . rawurlencode($this->config->orgName) . '/apps';
        $this->setBaseUrl($url);
        $this->get($appId);
        $this->restoreBaseUrl();
        $response = $this->responseObj;
        $developer = $this->getDeveloperMailById($response['developerId']);
        self::loadFromResponse($this, $response, $developer);
        // Must load developer to get email
        if ($reset_developer) {
            $this->client->setBaseUrl('/o/' . rawurlencode($this->config->orgName) . '/developers/' . rawurlencode($developer));
        }
    }

    private function getDeveloperMailById($id)
    {
        static $devs = array();
        if (!isset($devs[$id])) {
            $dev = new Developer($this->config);
            $dev->load($id);
            $devs[$id] = $dev->getEmail();
        }
        return $devs[$id];
    }

    /**
     * {@inheritDOc}
     */
    public function blankValues()
    {
        $this->accessType = 'read';
        $this->apiProducts = array();
        $this->appFamily = NULL;
        $this->appId = NULL;
        $this->attributes = array();
        $this->callbackUrl = '';
        $this->createdAt = NULL;
        $this->createdBy = NULL;
        $this->modifiedAt = NULL;
        $this->modifiedBy = NULL;
        $this->developerId = NULL;
        $this->name = NULL;
        $this->scopes = array();
        $this->status = 'pending';
        $this->description = NULL;

        $this->credentialApiProducts = array();
        $this->consumerKey = NULL;
        $this->consumerSecret = NULL;
        $this->credentialScopes = array();
        $this->credentialStatus = NULL;
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
        }
        $excluded_properties[] = 'cachedApiProducts';
        $excluded_properties[] = 'baseUrl';

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
     * Dummy placeholder to allow subclasses to modify the DeveloperApp
     * object as it is finishing the load process.
     *
     * @param DeveloperAppInterface $obj
     * @param array $response
     */
    protected static function afterLoad(DeveloperAppInterface &$obj, array $response)
    {
        // Do Nothing
    }

    /**
     * Dummy placeholder to allow subclasses to modify the payload of the
     * app-save call right before it is invoked.
     *
     * @param array $payload
     * @param DeveloperAppInterface $obj
     */
    protected static function preSave(array &$payload, DeveloperAppInterface $obj)
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
}