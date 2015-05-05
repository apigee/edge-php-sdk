<?php
/**
 * @file
 * Code for Apikey Scheme.
 */

namespace Apigee\SmartDocs;

/**
 * Holds information for Apikey Scheme
 *
 * @author Sudheesh
 */
class ApikeyScheme {

  /**
   * The scheme name.
   * @var string
   */
  private $name;

  /**
   * The param name.
   * @var string
   */
  private $paramName;

  /**
   * The location of the param either header, query or body.
   * @var string
   */
  private $in;

  /**
   * The type of the scheme
   * @var type
   */
  private $type = 'APIKEY';

  /**
   * Get the name of the scheme.
   *
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
   * Get the param name of the scheme.
   * @return string
   *   The parameter name of the scheme.
   */
  public function getParamName() {
    return $this->paramName;
  }

  /**
   * Sets the param name of the scheme.
   *
   * @param string $paramName
   *   The param name.
   */
  public function setParamName($paramName) {
    $this->paramName = $paramName;
  }

  /**
   * Gets the location of the parameter.
   *
   * @return string
   *   The location of the parameter, the values can be header, query or body.
   */
  public function getIn() {
    return $this->in;
  }

  /**
   * Set the location of the parameter of the scheme.
   *
   * @param string $in
   *   The location of the paramete, values can be header, query or body.
   */
  public function setIn($in) {
    $this->in = $in;
  }

  /**
   * Gets the type of the scheme.
   * @return string
   *   The type of the scheme.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Sets the type of the scheme.
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
