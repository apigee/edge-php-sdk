<?php

/**
 * @file
 * Code for Oauth2Scheme.
 */

namespace Apigee\SmartDocs;


/**
 * Holds information for Oauth2 Scheme.
 *
 * @package Apigee\SmartDocs
 * @author Sudheesh
 */
class Oauth2Scheme {

  /**
   * The name of the scheme.
   * @var string
   */
  private $name;

  /**
   * The type of the scheme.
   * @var string
   */
  private $type = 'OAUTH2';

  /**
   * The grantType of the scheme.
   * @var string
   */
  private $grantType;

  /**
   * The authorization url of the scheme.
   * @var string
   */
  private $authorizationUrl;

  /**
   * The authorization verb of the scheme.
   * @var string
   */
  private $authorizationVerb;

  /**
   * The access token url of the scheme.
   * @var string
   */
  private $accessTokenUrl;

  /**
   * The access token param name of the scheme.
   * @var string
   */
  private $accessTokenParamName;

  /**
   * The scopes of the scheme.
   * @var object
   */
  private $scopes;

  /**
   * Gets the name of the scheme.
   * @return string
   *   The name of the scheme
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Sets the name of a scheme.
   *
   * @param string $name
   *   The name of the scheme.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get the grant type of a scheme.
   *
   * @return string
   *   The grant type of the scheme.
   */
  public function getGrantType() {
    return $this->grantType;
  }

  /**
   * Set the grant type of the scheme.
   *
   * @param string $grantType
   *   The grant type.
   */
  public function setGrantType($grantType) {
    $this->grantType = $grantType;
  }

  /**
   * Get the authorization url of the scheme.
   * @return string
   *   The authorization url.
   */
  public function getAuthorizationUrl() {
    return $this->authorizationUrl;
  }

  /**
   * Set the authorization url of the scheme.
   *
   * @param string $authorizationUrl
   *   The authorization url.
   */
  public function setAuthorizationUrl($authorizationUrl) {
    $this->authorizationUrl = $authorizationUrl;
  }

  /**
   * Get the authorization verb of the scheme.
   * @return string
   *   The authorization verb.
   */
  public function getAuthorizationVerb() {
    return $this->authorizationVerb;
  }

  /**
   * Set the authorization verb of the scheme.
   *
   * @param string $authorizationVerb
   *   The authorization verb.
   */
  public function setAuthorizationVerb($authorizationVerb) {
    $this->authorizationVerb = $authorizationVerb;
  }

  /**
   * Get the access token url of the scheme.
   * @return string
   *   The access token url.
   */
  public function getAccessTokenUrl() {
    return $this->accessTokenUrl;
  }

  /**
   * Set the access token url.
   *
   * @param string $accessTokenUrl
   *   The access token url.
   */
  public function setAccessTokenUrl($accessTokenUrl) {
    $this->accessTokenUrl = $accessTokenUrl;
  }

  /**
   * Get the access token parameter name.
   * @return string
   *   The access token parameter name.
   */
  public function getAccessTokenParamName() {
    return $this->accessTokenParamName;
  }

  /**
   * Set the access token parameter name.
   *
   * @param string $accessTokenParamName
   *   The access token parameter name.
   */
  public function setAccessTokenParamName($accessTokenParamName) {
    $this->accessTokenParamName = $accessTokenParamName;
  }

  /**
   * Get the scopes of the scheme.
   *
   * @return array
   *   The scopes associated with the scheme.
   */
  public function getScopes() {
    return (array) $this->scopes;
  }

  /**
   * Set the scopes of the scheme.
   *
   * @param array $scopes
   *   The scopes associated with the scheme.
   */
  public function setScopes(array $scopes) {
    $this->scopes = (object) $scopes;
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
        if ($key == 'scopes') {
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
