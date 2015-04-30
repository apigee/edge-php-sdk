<?php
/**
 * @file
 * Contains code for the Apikey template scheme.
 */

namespace Apigee\SmartDocs;

/**
 * Holds the information for the Apikey template scheme.
 *
 * @author Sudheesh
 */
class ApikeyTemplateScheme {

  /**
   * The name of the apikey scheme.
   * @var string
   */
  private $name;

  /**
   * The type of the scheme.
   * @var string
   */
  private $type = 'APIKEY';

  /**
   * The api key object with key as the paramName and value as paramValue.
   * @var object
   */
  private $apikey;

  /**
   * Get the name of the scheme.
   * @return string
   *   The name of the scheme.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the name of the scheme.
   *
   * @param string $name
   *   The name of the scheme.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get the type of the scheme.
   * @return string
   *   The type of the scheme.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Set the type of the scheme.
   *
   * @param string $type
   *   The type of the scheme.
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * Get the apikey object.
   * @return object
   *   The apikey object
   */
  public function getApikey() {
    return $this->apikey;
  }

  /**
   * Set the apikey object of the scheme.
   *
   * @param object $apikey
   *   The apikey object.
   */
  public function setApikey($apikey) {
    $this->apikey = $apikey;
  }
  /**
   * Populates this object with values from the payload.
   *
   * @param array $payload
   *   Associative array with keys as object property names and values the
   *   corresponding property value.
   */
  public function __construct(array $payload = NULL) {
    foreach (get_object_vars($this) as $key => $value) {
      if (array_key_exists($key, $payload)) {
        if ($key == 'apikey') {
          $this->$key = (object) $payload[$key];
        }
        else {
          $this->$key = $payload[$key];
        }
      }
    }
  }

  /**
   * Persists this object as an array.
   *
   * @return array
   *   The array of object properties.
   */
  public function toArray() {
    $vars = get_object_vars($this);
    return $vars;
  }


}
