<?php
/**
 * @file
 * Contains code for the Template Auths
 */

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ResponseException;
use Apigee\Exceptions\ParameterException;

/**
 * Abstraction of new Template auth system of SmartDocs.
 *
 * @package Apigee\SmartDocs
 * @author Sudheesh
 */
class TemplateAuth extends APIObject {

  /**
   * The type of the template auth scheme.
   * @var string
   */
  public $type;

  /**
   * Initializes this object.
   *
   * @param \Apigee\Util\OrgConfig $config
   *   The mangement endpoint config.
   * @param string $modelId
   *   The model id
   * @param string $type
   *   The type of the template auth scheme.
   */
  public function __construct(OrgConfig $config, $modelId, $type = NULL) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($modelId) . '/templateauths');

    if (!empty($type)) {
      $this->type = $type;
    }
  }

  /**
   * Load a template auth scheme by name.
   *
   * @param string $name
   *   The name of the scheme to load.
   *
   * @return object
   *   A scheme object.
   */
  public function load($name) {
    try {
      $this->get($name);
      $this->type = $this->responseObj['type'];
      switch ($this->type) {
        case 'OAUTH2':
          $scheme = new Oauth2TemplateScheme($this->responseObj);
          break;

        case 'APIKEY':
          $scheme = new ApikeyTemplateScheme($this->responseObj);
          break;
      }
    }
    catch (ResponseException $e) {

    }
    return $scheme;
  }

  /**
   * Load all the template auth scheme.
   * @return array
   *   An array of schemes.
   */
  public function loadAllSchemes() {
    try {
      $this->get();
      $schemes = array();
      foreach ($this->responseObj as $scheme) {
        $schemes[$scheme['name']] = $scheme;
      }
    }
    catch (ResponseException $e) {

    }
    return !empty($schemes) ? $schemes : array();
  }

  /**
   * Saves a template auth scheme.
   *
   * @param array $payload
   *   The payload array that depending on the type of the scheme.
   * @param bool $is_update
   *   Indicates if a scheme should be created or updated.
   */
  public function save(array $payload, $is_update = FALSE) {

    switch ($payload['type']) {
      case 'OAUTH2':
        $scheme = new Oauth2TemplateScheme($payload);
        $this->saveOauth2TemplateScheme($scheme, $is_update);
        break;

      case 'APIKEY':
        $scheme = new ApikeyTemplateScheme($payload);
        $this->saveApikeyTemplateScheme($scheme, $is_update);
        break;
    }
  }

  /**
   * Save a scheme of type Oauth 2.
   *
   * @param \Apigee\SmartDocs\Oauth2TemplateScheme $scheme
   *   The Oauth2TemplateScheme object.
   * @param bool $is_update
   *   Indicates if a scheme should be created or updated.
   */
  public function saveOauth2TemplateScheme(Oauth2TemplateScheme &$scheme, $is_update = FALSE) {
    if ($is_update) {
      try {
        $payload = $scheme->toArray();
        unset($payload['type']);
        $this->put($scheme->getName(), $payload);
        $scheme = new Oauth2TemplateScheme($this->responseObj);
      }
      catch (ResponseException $e) {

      }
    }
    else {
      try{
        $this->post(NULL, $scheme->toArray());
        $scheme = new Oauth2TemplateScheme($this->responseObj);
      }
      catch (ResponseException $e) {

      }
    }
  }

  /**
   * Save a scheme of type apikey.
   *
   * @param \Apigee\SmartDocs\ApikeyTemplateScheme $scheme
   *   The ApikeyTemplateScheme object.
   * @param bool $is_update
   *   Indicates if a scheme should be created or updated.
   */
  public function saveApikeyTemplateScheme(ApikeyTemplateScheme &$scheme, $is_update = FALSE) {
    if ($is_update) {
      try {
        $payload = $scheme->toArray();
        unset($payload['type']);
        $this->put($scheme->getName(), $payload);
        $scheme = new ApikeyTemplateScheme($this->responseObj);
      }
      catch (ResponseException $e) {

      }
    }
    else {
      try{
        $this->post(NULL, $scheme->toArray());
        $scheme = new ApikeyTemplateScheme($this->responseObj);
      }
      catch (ResponseException $e) {

      }
    }
  }
}
