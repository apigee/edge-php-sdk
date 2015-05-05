<?php
/**
 * @file
 * Code for the Basic Scheme
 */

namespace Apigee\SmartDocs;


/**
 * Holds the information for the Basic Scheme
 *
 * @author Sudheesh
 */
class BasicScheme {

  /**
   * The name of the scheme.
   * @var string
   */
  private $name;

  /**
   * The type of the scheme.
   * @var string
   */
  private $type = 'BASIC';

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
   *
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
   * Populates this object with values from the payload.
   *
   * @param array $payload
   *   Associative array with keys as object property names and values the
   *   corresponding property value.
   */
  public function __construct(array $payload = NULL) {
    foreach (get_object_vars($this) as $key => $value) {
      if (array_key_exists($key, $payload)) {
        $this->$key = $payload[$key];
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
