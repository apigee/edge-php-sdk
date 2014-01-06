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

class DeveloperApp extends Base implements DeveloperAppInterface {

  /**
   * @var string
   * 'read', 'write', or 'both' (empty is also valid). This property doesn't
   * appear to ever be used.
   */
  protected $accessType;
  /**
   * @var array
   */
  protected $apiProducts;
  /**
   * @var string.
   * Read-only. Purpose of this field is unknown at this time.
   */
  protected $appFamily;
  /**
   * @var string
   * Read-only. GUID of this app.
   */
  protected $appId;
  /**
   * @var array
   * This is protected because Base wants to twiddle with it.
   */
  protected $attributes;
  /**
   * @var string
   */
  protected $callbackUrl;
  /**
   * @var int
   * Read-only.
   */
  protected $createdAt;
  /**
   * @var string
   * Read-only.
   */
  protected $createdBy;
  /**
   * @var int
   * Read-only.
   */
  protected $modifiedAt;
  /**
   * @var string
   * Read-only.
   */
  protected $modifiedBy;
  /**
   * @var string
   * Read-only. Corresponds to the developer_id attribute of the developer who
   * owns this app.
   */
  protected $developerId;
  /**
   * @var string
   * Primary key (within this org/developer's app list)
   */
  protected $name;
  /**
   * @var array
   * The purpose of this field remains unknown.
   */
  protected $scopes;
  /**
   * @var string
   * There is probably a finite number of possible values, but I haven't found
   * a definitive list yet.
   */
  protected $status;
  /**
   * @var string
   */
  protected $description;

  /**
   * @var array
   * Each member of this array is itself an associative array, with keys of
   * 'apiproduct' and 'status'.
   */
  protected $credentialApiProducts;
  /**
   * @var string
   */
  protected $consumerKey;
  /**
   * @var string
   */
  protected $consumerSecret;
  /**
   * @var array
   * The purpose of this field is unknown at this time.
   */
  protected $credentialScopes;
  /**
   * @var string
   */
  protected $credentialStatus;
  /**
   * @var array
   */
  protected $credentialAttributes;

  /**
   * @var string
   */
  protected $developer;
  /**
   * @var array
   */
  protected $cachedApiProducts;
  /**
   * @var string
   */
  protected $baseUrl;

  /* Accessors (getters/setters) */
  public function getApiProducts() {
    return $this->apiProducts;
  }

  public function setApiProducts($products) {
    if (!is_array($products)) {
      $products = array($products);
    }
    $this->cachedApiProducts = $this->apiProducts;
    $this->apiProducts = $products;
  }

  public function getAttributes() {
    return $this->attributes;
  }

  public function hasAttribute($attr) {
    return array_key_exists($attr, $this->attributes);
  }

  public function getAttribute($attr) {
    return (array_key_exists($attr, $this->attributes) ? $this->attributes[$attr] : NULL);
  }

  public function setAttribute($attr, $value) {
    $this->attributes[$attr] = $value;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setCallbackUrl($url) {
    $this->callbackUrl = $url;
  }

  public function getCallbackUrl() {
    return $this->callbackUrl;
  }

  public function setDescription($descr) {
    $this->description = $descr;
    $this->attributes['description'] = $descr;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setAccessType($type) {
    if ($type != 'read' && $type != 'write' && $type != 'both') {
      throw new ParameterException('Invalid access type ' . $type . '.');
    }
    $this->accessType = $type;
  }

  public function getAccessType() {
    return $this->accessType;
  }

  public function getStatus() {
    return $this->status;
  }

  protected function setStatus($status) {
    $this->status = $status;
  }

  public function getDeveloperId() {
    return $this->developerId;
  }

  public function getDeveloperMail() {
    return $this->developer;
  }

  public function getCredentialApiProducts() {
    return $this->credentialApiProducts;
  }

  protected function setCredentialApiProducts(array $list) {
    $this->credentialApiProducts = $list;
  }

  public function getConsumerKey() {
    return $this->consumerKey;
  }

  public function setConsumerKey($key) {
    $this->consumerKey = $key;
  }

  public function getConsumerSecret() {
    return $this->consumerSecret;
  }

  public function setConsumerSecret($secret) {
    $this->consumerSecret = $secret;
  }

  public function getCredentialScopes() {
    return $this->credentialScopes;
  }

  protected function setCredentialScopes(array $scopes) {
    $this->credentialScopes = $scopes;
  }

  public function getCredentialStatus() {
    return $this->credentialStatus;
  }

  protected function setCredentialStatus($status) {
    $this->credentialStatus = $status;
  }

  public function getCreatedAt() {
    return $this->createdAt;
  }

  protected function setCreatedAt($time_in_milliseconds) {
    $this->createdAt = floatval($time_in_milliseconds);
  }

  public function getCreatedBy() {
    return $this->createdBy;
  }

  public function setCreatedBy($who) {
    $this->createdBy = $who;
  }

  public function getModifiedAt() {
    return $this->modifiedAt;
  }

  protected function setModifiedAt($time_in_milliseconds) {
    $this->modifiedAt = $time_in_milliseconds;
  }

  public function getModifiedBy() {
    return $this->modifiedBy;
  }

  public function setModifiedBy($who) {
    $this->modifiedBy = $who;
  }

  public function getCredentialAttribute($attr_name) {
    if (isset($this->credentialAttributes[$attr_name])) {
      return $this->credentialAttributes[$attr_name];
    }
    return NULL;
  }

  public function setCredentialAttribute($name, $value) {
    $this->credentialAttributes[$name] = $value;
  }

  public function getCredentialAttributes() {
    return $this->credentialAttributes;
  }

  public function clearCredentialAttributes() {
    $this->credentialAttributes = array();
  }

  public function getAppId() {
    return $this->appId;
  }

  protected function setAppId($id) {
    $this->appId = $id;
  }


  public function getAppFamily() {
    return $this->appFamily;
  }

  public function setAppFamily($family) {
    $this->appFamily = $family;
  }

  public function getScopes() {
    return $this->scopes;
  }

  protected function setScopes(array $scopes) {
    $this->scopes = $scopes;
  }


  public function hasCredentialInfo() {
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

  public function setApiProductCache(array $cache) {
    $this->cachedApiProducts = $cache;
  }

  // TODO: write other getters/setters


  /**
   * Initializes this object
   *
   * @param \Apigee\Util\OrgConfig $config
   * @param mixed $developer
   */
  public function __construct(\Apigee\Util\OrgConfig $config, $developer) {
    if ($developer instanceof DeveloperInterface) {
      $this->developer = $developer->getEmail();
    }
    else {
      // $developer may be either an email or a developerId.
      $this->developer = $developer;
    }
    $baseUrl = '/o/' . $this->urlEncode($config->orgName) . '/developers/' . $this->developer . '/apps';
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
  protected static function loadFromResponse(DeveloperApp &$obj, array $response, $developer_mail = NULL) {
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
    }
    elseif (!preg_match('!^[^@]+@[^@]+$!', $obj->developer)) {
      $obj->developer = $obj->getDeveloperMailById($response['developerId']);
    }

    $obj->readAttributes($response);

    if (!empty($response['description'])) {
      $obj->description = $response['description'];
    }
    elseif (isset($obj->attributes['description'])) {
      $obj->description = $obj->getAttribute('description');
    }
    else {
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
   * @static
   * @param DeveloperApp $obj
   * @param $credentials
   */
  protected static function loadCredentials(DeveloperApp &$obj, $credentials) {
    // Find the credential with the max create_date attribute.
    if (count($credentials) > 0) {
      $credential = NULL;
      // Sort credentials by create_date descending.
      usort($credentials, array(__CLASS__, 'sortCredentials'));
      // Look for the first member of the array that is approved.
      foreach ($credentials as $c) {
        if ($c['status'] == 'approved') {
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
   * Finds the overall status of this app. We first check app status, then
   * credential status, then credential->apiproduct status. If any are
   * 'revoked', we return revoked; otherwise if any are 'pending', that's what
   * we return; else we return 'approved'.
   *
   * @return string
   */
  public function getOverallStatus() {
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
  public function load($name = NULL) {
    $name = $name ?: $this->name;
    $this->get($this->urlEncode($name));
    $response = $this->responseObj;
    self::loadFromResponse($this, $response, $this->developer);
  }

  /**
   * Checks to see if an app with the given name exists for this developer.
   *
   * If $name is not passed, $this->name is used.
   *
   * @param null|string $name
   * @return bool
   */
  public function validate($name = NULL) {
    $name = $name ?: $this->name;
    try {
      $this->get($this->urlEncode($name));
      return FALSE;
    }
    catch (\Apigee\Exceptions\ResponseException $e) {}
    return TRUE;
  }

  /**
   * Determines difference between cached version of API Products array for
   * this app and current version. Returned object enumerates which API
   * Products are due for removal (if any), and which should be added.
   *
   * @return \stdClass
   */
  protected function apiProductsDiff() {
    // Find apiproducts that we will have to delete.  These are found in the
    // cached list but not in the live list.
    $to_delete = array();
    foreach ($this->cachedApiProducts as $api_product) {
      if (!in_array($api_product['apiproduct'], $this->apiProducts)) {
        $to_delete[] = $api_product['apiproduct'];
      }
    }
    // Find apiproducts that we will have to add. These are found in the
    // live list but not in the cached list.
    $to_add = array();
    foreach ($this->apiProducts as $api_product) {
      if (!in_array($api_product, $this->cachedApiProducts)) {
        $to_add[] = $api_product;
      }
    }
    return (object) array('to_delete' => $to_delete, 'to_add' => $to_add);
  }

  /**
   * Write this app's data to the Management API, preserving client key/secret.
   *
   * The function attempts to determine if this should be an insert or an
   * update automagically. However, when $force_update is set to TRUE, this
   * determination is short-circuited and an update is assumed.
   *
   * @param bool $force_update
   */
  public function save($force_update = FALSE) {
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
      $url = $this->urlEncode($this->getName());
    }
    $created_new_key = FALSE;
    // NOTE: On update, we send APIProduct information separately from other
    // fields, in order to preserve the client-key/secret pair. Updates to
    // APIProducts must be made separately against the app's client-key,
    // rather than just against the app. Additionally, deletions from the
    // APIProducts list must be handled separately from additions.
    $consumer_key = $this->getConsumerKey();
    if ($is_update && !empty($consumer_key)) {
      $key_uri = ltrim("$url/keys/", '/') . $this->urlEncode($consumer_key);
      $diff = $this->apiProductsDiff();
      // api-product deletions must happen one-by-one.
      foreach ($diff->to_delete as $api_product) {
        $delete_uri = "$key_uri/apiproducts/" . $this->urlEncode($api_product);
        $this->http_delete($delete_uri);
      }
      // api-product additions can happen in a batch.
      if (count($diff->to_add) > 0) {
        $this->post($key_uri, array('apiProducts' => $diff->to_add));
      }
    }
    else {
      $payload['apiProducts'] = $this->getApiProducts();
      $created_new_key = TRUE;
    }

    self::preSave($payload, $this);

    $this->post($url, $payload);
    $response = $this->responseObj;

    // If we created a new key, add a create_date attribute to it.
    if ($created_new_key && count($response['credentials']) > 0) {
      $credentials = $response['credentials'];
      $no_timestamp_index = NULL;
      // Look for the first credential that has no create_date timestamp.
      foreach ($credentials as $i => $cred) {
        $attrs = $cred['attributes'];
        $found_create_date = FALSE;
        foreach ($attrs as $attr) {
          if ($attr['name'] == 'create_date') {
            $found_create_date = TRUE;
            break;
          }
        }
        if (!$found_create_date) {
          $no_timestamp_index = $i;
          break;
        }
      }
      // If all credentials have a create_date timestamp, there's nothing
      // for us to update here.

      if (isset($no_timestamp_index)) {
        // Get reference to array member so we are actually updating $response
        $new_credential =& $response['credentials'][$no_timestamp_index];

        $create_date = time();
        $key = $new_credential['consumerKey'];

        // Set our create_date attribute.
        $new_credential['attributes'][] = array('name' => 'create_date', 'value' => strval($create_date));
        $payload = $new_credential;
        // Payload only has to send bare minimum for update.
        unset($payload['apiProducts'], $payload['scopes'], $payload['status']);
        $url = $this->urlEncode($this->name) . '/keys/' . $key;

        self::preSaveCredential($payload, $new_credential, $response);
        // POST that sucker!
        $this->post($url, $payload);
      }
    }

    // Refresh our fields so we get latest autogenerated data such as consumer key etc.
    self::loadFromResponse($this, $response, $this->developer);
  }

  /**
   * Usort callback to sort credentials by create date (most recent first).
   *
   * @static
   * @param $a
   * @param $b
   * @return int
   */
  protected static function sortCredentials($a, $b) {
    $a_create_date = 0;
    foreach ($a['attributes'] as $attr) {
      if ($attr['name'] == 'create_date') {
        $a_create_date = intval($attr['value']);
        break;
      }
    }
    $b_create_date = 0;
    foreach ($b['attributes'] as $attr) {
      if ($attr['name'] == 'create_date') {
        $b_create_date = intval($attr['value']);
        break;
      }
    }
    if ($a_create_date == $b_create_date) {
      return 0;
    }
    return ($a_create_date > $b_create_date) ? -1 : 1;
  }

  /**
   * Approves or revokes a client key for an app, and optionally also for all
   * API Products associated with that app.
   *
   * @param mixed $status
   *        May be TRUE, FALSE, 0, 1, 'approve' or 'revoke'
   * @param bool $also_set_apiproduct
   * @throws \Apigee\Exceptions\ParameterException
   */
  public function setKeyStatus($status, $also_set_apiproduct = TRUE) {
    if ($status === 0 || $status === FALSE) {
      $status = 'revoke';
    }
    elseif ($status === 1 || $status === TRUE) {
      $status = 'approve';
    }
    elseif ($status != 'revoke' && $status != 'approve') {
      throw new ParameterException('Invalid key status ' . $status);
    }

    if (strlen($this->getName()) == 0) {
      throw new ParameterException('No app specified; cannot set key status.');
    }
    if (strlen($this->getConsumerKey()) == 0) {
      throw new ParameterException('App has no consumer key; cannot set key status.');
    }
    $base_url = $this->urlEncode($this->getName()) . '/keys/' . $this->urlEncode($this->getConsumerKey());
    // First, approve or revoke the overall key for the app.
    $app_url = $base_url . '?action=' . $status;
    $this->post($app_url, '');

    // Now, unless specified otherwise, approve or revoke the same key for all
    // associated API Products.
    if ($also_set_apiproduct) {
      $api_products = $this->getApiProducts();
      if (!empty($api_products)) {
        foreach ($api_products as $api_product) {
          $product_url = $base_url . '/apiproducts/' . $this->urlEncode($api_product) . '?action=' . $status;
          $this->post($product_url, '');
        }
      }
    }
  }

  /**
   * Deletes a developer app from the Management API.
   *
   * If $name is not passed, $this->name is used.
   *
   * @param null|string $name
   */
  public function delete($name = NULL) {
    $name = $name ?: $this->name;
    $this->http_delete($this->urlEncode($name));
    if ($name == $this->getName()) {
      $this->blankValues();
    }
  }

  /**
   * Returns names of all apps belonging to this developer.
   *
   * @return array
   */
  public function getList() {
    $this->get();
    return $this->responseObj;
  }

  /**
   * Returns array of all DeveloperApp objects belonging to this developer.
   *
   * @param string|NULL $developer_mail
   * @return array
   */
  public function getListDetail($developer_mail = NULL) {
    $developer_mail = $developer_mail ?: $this->developer;

    $this->setBaseUrl('/o/' . $this->urlEncode($this->config->orgName) . '/developers/' . $this->urlEncode($developer_mail) . '/apps');

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
   * Creates a key/secret pair for this app against its component APIProducts.
   *
   * @todo Find out if we need to individually set the key on each APIProduct.
   *
   * @param string $consumer_key
   * @param string $consumer_secret
   * @throws \Apigee\Exceptions\ParameterException
   */
  public function createKey($consumer_key, $consumer_secret) {
    if (strlen($consumer_key) < 5 || strlen($consumer_secret) < 5) {
      throw new ParameterException('Consumer Key and Consumer Secret must each be at least 5 characters long.');
    }
    // This is by nature a two-step process. API Products cannot be added
    // to a new key at the time of key creation, for some reason.
    $create_date = strval(time());
    $payload = array(
      'attributes' => array(array('name' => 'create_date', 'value' => $create_date)),
      'consumerKey' => $consumer_key,
      'consumerSecret' => $consumer_secret,
      'scopes' => $this->getCredentialScopes(),
    );

    $url = $this->urlEncode($this->name) . '/keys/create';
    $this->post($url, $payload);

    $new_credential = $this->responseObj;
    // We now have the new key, sans apiproducts. Let us add them now.
    $new_credential['apiProducts'] = $this->getCredentialApiProducts();
    $key = $new_credential['consumerKey'];
    $url = $this->urlEncode($this->getName()) . '/keys/' . $this->urlEncode($key);
    $this->post($url, $new_credential);
    // The following line may throw an exception if the POST was unsuccessful
    // (e.g. consumer_key already exists, etc.)
    $credential = $this->responseObj;

    if ($credential['status'] == 'approved' || empty($this->consumerKey)) {
      // Update $this with new credential info ONLY if the key is auto-approved
      // or if there are no keys yet.
      $this->setCredentialApiProducts($credential['apiProducts']);
      $this->setConsumerKey($credential['consumerKey']);
      $this->setConsumerSecret($credential['consumerSecret']);
      $this->setCredentialScopes($credential['scopes']);
      $this->setCredentialStatus($credential['status']);
      $this->clearCredentialAttributes();
      foreach ($credential['attributes'] as $attribute) {
        $this->setCredentialAttribute($attribute['name'], $attribute['value']);
      }
    }
  }

  /**
   * Deletes a given key from a developer app.
   *
   * @param string $consumer_key
   */
  public function deleteKey($consumer_key) {
    $url = $this->urlEncode($this->getName()) . '/keys/' . $this->urlEncode($consumer_key);
    try {
      $this->http_delete($url);
    }
    catch (\Apigee\Exceptions\ResponseException $e) {}
    // We ignore whether or not the delete was successful. Either way, we can
    // be sure it doesn't exist now, if it did before.

    // Reload app to repopulate credential fields.
    $this->load();
  }

  /**
   * Lists all apps within the org. Each member of the returned array is a
   * fully-populated DeveloperApp product.
   *
   * @return array
   */
  public function listAllOrgApps() {
    $url = '/o/' . $this->urlEncode($this->config->orgName);
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

  public function loadByAppId($appId, $reset_developer = FALSE) {
    if (!preg_match('!^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$!', $appId)) {
      throw new ParameterException('Invalid UUID passed as appId.');
    }

    $url = '/o/' . $this->urlEncode($this->config->orgName) . '/apps';
    $this->setBaseUrl($url);
    $this->get($appId);
    $this->restoreBaseUrl();
    $response = $this->responseObj;
    $developer = $this->getDeveloperMailById($response['developerId']);
    self::loadFromResponse($this, $response, $developer);
    // Must load developer to get email
    if ($reset_developer) {
      $this->client->setBaseUrl('/o/' . $this->urlEncode($this->config->orgName) . '/developers/' . $this->urlEncode($developer));
    }
  }

  private function getDeveloperMailById($id) {
    static $devs = array();
    if (!isset($devs[$id])) {
      $dev = new Developer($this->config);
      $dev->load($id);
      $devs[$id] = $dev->getEmail();
    }
    return $devs[$id];
  }

  /**
   * Restores this object to its pristine state.
   */
  public function blankValues() {
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

    $this->cachedApiProducts = array();
  }

  /**
   * Turns this object's properties into an array for external use.
   *
   * @return array
   */
  public function toArray() {
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

  public static function getAppProperties() {
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
   * toArray() method above.
   *
   * @param $array
   */
  public function fromArray($array) {
    foreach($array as $key => $value) {
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
  protected static function afterLoad(DeveloperAppInterface &$obj, array $response) {
    // Do Nothing
  }

  /**
   * Dummy placeholder to allow subclasses to modify the payload of the
   * app-save call right before it is invoked.
   *
   * @param array $payload
   * @param DeveloperAppInterface $obj
   */
  protected static function preSave(array &$payload, DeveloperAppInterface $obj) {
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
  protected static function preSaveCredential(array &$payload, array $credential, array $kms_response) {
    // Do Nothing
  }
}