<?php
/**
 * @file
 * Contains code for the Oauth 2 template scheme.
 */

namespace Apigee\SmartDocs;

/**
 * Holds the information for the Oauth 2 template scheme.
 *
 * @author Sudheesh
 */
class Oauth2TemplateScheme {
  /**
   * The name of the scheme.
   * @var string
   */
  private $name;

  /**
   * The type of the scheme
   * @var string
   */
  private $type = 'OAUTH2';

  /**
   * The Oauth object with the keys clientId and clientSecret.
   * @var object
   */
  private $oauth;

  /**
   * Gets the name of the scheme.
   * @return string
   *   The name of the scheme.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Sets the name of the scheme.
   *
   * @param string $name
   *   The name of the scheme.
   */
  public function setName($name) {
    $this->name = $name;
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
   *   The type of scheme.
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * Get the oauth object.
   * @return object
   *   The oauth object
   */
  public function getOauth() {
    return $this->oauth;
  }

  /**
   * Set the oauth object.
   *
   * @param object $oauth
   *   The Oauth object
   */
  public function setOauth($oauth) {
    $this->oauth = $oauth;
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
        if ($key == 'oauth') {
          $this->oauth = (object) $payload[$key];
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
