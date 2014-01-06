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

class APIProduct extends Base implements APIProductInterface {

  /**
   * @var array
   */
  protected $apiResources;
  /**
   * @var string
   * 'manual' or 'auto'
   */
  protected $approvalType;
  /**
   * @var int
   */
  protected $createdAt;
  /**
   * @var string
   * read-only
   */
  protected $createdBy;
  /**
   * @var int
   * read-only
   */
  protected $modifiedAt;
  /**
   * @var string
   * read-only
   */
  protected $modifiedBy;
  /**
   * @var string
   * read-only
   */
  protected $description;
  /**
   * @var string
   */
  protected $displayName;
  /**
   * @var array
   */
  protected $environments;
  /**
   * @var string
   */
  protected $name;
  /**
   * @var array
   * FIXME: the purpose of this member is unknown
   */
  protected $proxies;
  /**
   * @var int
   * Quota limit. It's safer to use attributes['developer.quota.limit'] instead.
   */
  protected $quota;
  /**
   * @var int
   * It's safer to use attributes['developer.quota.interval'] instead.
   */
  protected $quotaInterval;
  /**
   * @var string
   * It's safer to use attributes['developer.quota.timeunit'] instead.
   */
  protected $quotaTimeUnit;
  /**
   * @var array
   * FIXME: the purpose of this member is unknown
   */
  protected $scopes;

  /**
   * @var array
   * Attributes must be protected because Base wants to twiddle with it.
   */
  protected $attributes;
  /**
   * @var bool
   */
  protected $loaded;

  /**
   * Initializes all member variables
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(\Apigee\Util\OrgConfig $config) {
    $baseUrl = '/o/' . $this->urlEncode($config->orgName) . '/apiproducts';
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
  public function load($name = NULL, $response = NULL) {
    $name = $name ?: $this->name;
    if (!isset($response)) {
      $this->get($this->urlEncode($name));
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
   * POSTs self's properties to Management API. This handles both
   * inserts and updates.
   */
  public function save() {
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
   * Deletes an API Product.
   *
   * If $name is not passed, uses $this->name.
   *
   * @param null|string $name
   */
  public function delete($name = NULL) {
    $name = $name ?: $this->name;
    $this->delete($this->urlEncode($name));
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
   * If $product is NOT passed, we assume that $this is already loaded, and we
   * make the determination based on self's properties.
   *
   * @param null|array $product
   * @return bool
   */
  public function isPublic($product = NULL) {
    if (!isset($product)) {
      if (isset($this->attributes['access']) && ($this->attributes['access'] == 'internal' || $this->attributes['access'] == 'private')) {
        return FALSE;
      }
    }
    else {
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
  protected function getProductsCache() {
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
   * Returns a detailed list of all products. This list may have been cached
   * from a previous call.
   *
   * If $show_nonpublic is TRUE, even API Products which are marked as hidden
   * or internal are returned.
   *
   * @param bool $show_nonpublic
   * @return array
   */
  public function listProducts($show_nonpublic = FALSE) {
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
  public function getAttributes() {
    return $this->attributes;
  }
  public function clearAttributes() {
    $this->attributes = array();
  }
  public function getAttribute($name) {
    if (isset($this->attributes[$name])) {
      return $this->attributes[$name];
    }
    return NULL;
  }
  public function setAttribute($name, $value) {
    if (isset($value) || !isset($this->attributes[$name])) {
      $this->attributes[$name] = $value;
    }
    else {
      unset($this->attributes[$name]);
    }
  }
  public function getCreatedAt() {
    return $this->createdAt;
  }
  public function getCreatedBy() {
    return $this->createdBy;
  }
  public function getModifiedAt() {
    return $this->modifiedAt;
  }
  public function getModifiedBy() {
    return $this->modifiedBy;
  }
  public function getEnvironments() {
    return $this->environments;
  }
  public function getName() {
    return $this->name;
  }
  public function getProxies() {
    return $this->proxies;
  }
  public function getQuotaLimit() {
    if (isset($this->attributes['developer.quota.limit'])) {
      return $this->attributes['developer.quota.limit'];
    }
    elseif (!empty($this->quota)) {
      return $this->quota;
    }
    return NULL;
  }
  public function getQuotaInterval() {
    if (isset($this->attributes['developer.quota.interval'])) {
      return $this->attributes['developer.quota.interval'];
    }
    elseif (!empty($this->quotaInterval)) {
      return $this->quotaInterval;
    }
    return NULL;
  }
  public function getQuotaTimeUnit() {
    if (isset($this->attributes['developer.quota.timeunit'])) {
      return $this->attributes['developer.quota.timeunit'];
    }
    elseif (!empty($this->quotaTimeUnit)) {
      return $this->quotaTimeUnit;
    }
    return NULL;
  }
  public function getDisplayName() {
    return $this->displayName;
  }

  public function getDescription() {
    if (!empty($this->description)) {
      return $this->description;
    }
    if (isset($this->attributes['description'])) {
      return $this->attributes['description'];
    }
    return NULL;
  }

  public function addApiResource($resource) {
    $this->apiResources[] = $resource;
  }
  public function removeApiResource($resource) { // was delApiResource
    $index = array_search($resource, $this->apiResources);
    if ($index !== FALSE) {
      unset($this->apiResources[$index]);
      // reindex this array to be sequential zero-based.
      $this->apiResources = array_values($this->apiResources);
    }
  }
  public function getApiResources() {
    return $this->apiResources;
  }
  public function getApprovalType() {
    return $this->approvalType;
  }
  public function setApprovalType($type) {
    if ($type != 'auto' && $type != 'manual') {
      throw new \Exception('Invalid approval type ' . $type . '; allowed values are "auto" and "manual".'); // TODO: use custom exception class
    }
    $this->approvalType = $type;
  }
  //TODO: populate getters/setters for other properties


  /**
   * Initializes this object to its pristine blank state.
   */
  protected function blankValues() {
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
  public function toArray() {
    $output = array();
    foreach (self::getAPIProductProperties() as $property) {
      if ($property == 'debugData') {
        $output[$property] = $this->getDebugData();
      }
      else {
        $output[$property] = $this->$property;
      }
    }
    return $output;
  }

  public static function getAPIProductProperties() {
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
   * toArray() method above.
   *
   * @param $array
   */
  public function fromArray($array) {
    foreach($array as $key => $value) {
      if (property_exists($this, $key) && $key != 'debugData' && $key != 'loaded') {
        $this->{$key} = $value;
      }
    }
    $this->loaded = TRUE;
  }

}