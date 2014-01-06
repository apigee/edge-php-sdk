<?php
namespace Apigee\Mint\DataStructures;

class RatePlanDetail {

  /**
   * RatePlanDetail id
   * @var string
   */
  public $id;

  /**
   * Rate plan detail type.
   * @var string Allowed values: [REVSHARE|REVSHARE_RATECARD|RATECARD|NON_CHARGEABLE]
   */
  public $type;

  /**
   * Revenue type.
   * @var string Allowed values: [NET|GROSS]
   */
  public $revenueType;

  /**
   * Metering Type.
   * @var string Allowed values: [UNIT|VOLUME|STAIR_STEP]
   */
  public $meteringType;

  /**
   * Rating Parameter for the rate card. Can be VOLUME or a CUSTOM_ATTRIBUTE_NAME or sum(CUSTOM_ATTRIBUTE_NAME)
   * @var string
   */
  public $ratingParameter;

  /**
   * what is the unit of rating paramter value. e.g. MB, minutes, words etc
   * @var string
   */
  public $ratingParameterUnit;

  /**
   * Duration
   * @var int
   */
  public $duration;

  /**
   * Duration Type.
   * @var string Allowed values: [DAY|WEEK|MONTH|QUARTER|YEAR]
   */
  public $durationType;

  /**
   * Freemium duration
   * @var int
   */
  public $freemiumDuration;

  /**
   * Freemium number of units
   * @var int
   */
  public $freemiumUnit;

  /**
   * Freemium Duration Type.
   * @var string Allowed values: [DAY|WEEK|MONTH|QUARTER|YEAR]
   */
  public $freemiumDurationType;

  /**
   * Rate card details
   * @var array Array must elements must be instances of Apigee\Mint\DataStructure\RatePlanRate
   */
  public $ratePlanRates = array();

  /**
   * Organization
   * @var \Apigee\Mint\Organization
   */
  public $organization;

  /**
   * Product
   * @var \Apigee\Mint\Product
   */
  public $product;

  /**
   * Rate Plan Detail currency
   * @var \Apigee\Mint\DataStructures\SupportedCurrency
   */
  public $currency;

  /**
   * Class constructor.
   * @param array|null $data
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct($data = NULL, \Apigee\Util\OrgConfig $config) {
    if (is_array($data)) {

      if (isset($data['ratePlanRates'])) {
        foreach ($data['ratePlanRates'] as $ratePlanRate) {
          $this->ratePlanRates[] = new RatePlanRate($ratePlanRate);
        }
      }

      if (isset($data['currency'])) {
        $this->currency = new SupportedCurrency($data['currency']);
      }

      if (isset($data['organization'])) {
        $organization = new \Apigee\Mint\Organization($config);
        $organization->loadFromRawData($data['organization']);
        $this->organization = $organization;
      }

      if (isset($data['product'])) {
        $product = new \Apigee\Mint\Product($config);
        $product->loadFromRawData($data['product']);
        $this->product = $product;
      }

      $excluded_properties = array('ratePlanRates', 'organization', 'product', 'currency');
      foreach (array_keys(get_object_vars($this)) as $var) {
        if (isset($data[$var]) && !in_array($var, $excluded_properties)) {
          $this->$var = $data[$var];
        }
      }
    }
  }

  public function __toString() {
    $obj = array();
    $properties = array_keys(get_object_vars($this));
    foreach ($properties as $property) {
      if (isset($this->$property)) {
        if (is_object($this->$property)) {
          $obj[$property] = json_decode((string) $this->$property, TRUE);
        }
        else {
          $obj[$property] = $this->$property;
        }
      }
    }
    return json_encode($obj);
  }

  public function getOrganization() {
    return $this->organization;
  }
}

