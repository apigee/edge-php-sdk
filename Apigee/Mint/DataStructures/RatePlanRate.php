<?php
namespace Apigee\Mint\DataStructures;

final class RatePlanRate {

  /**
   * RatePlanRate id
   * @var string
   */
  public $id;

  /**
   * Rate plan rate type.
   * Possible values:
   * @var string Allowed values: [REVSHARE|RATECARD]
   */
  public $type;

  /**
   * Price
   * @var double
   */
  public $rate;

  /**
   * Revshare
   * @var string
   */
  public $revshare;

  /**
   * Unit range start
   * @var int
   */
  public $startUnit;

  /**
   * Unit range end
   * @var int
   */
  public $endUnit;

  /**
   * Class constructor.
   * @param array $data
   */
  public function __construct($data = NULL) {
    if (is_array($data)) {
      foreach (array_keys(get_object_vars($this)) as $var) {
        if (isset($data[$var])) {
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
}