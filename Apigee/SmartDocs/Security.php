<?php

/**
 * @file
 * Code for SmartDocs security scheme.
 */

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ResponseException;
use Apigee\Exceptions\ParameterException;

/**
 * Abstraction of new Security sub system of SmartDocs.
 *
 * @package Apigee\SmartDocs
 * @author Sudheesh
 */
class Security extends APIObject {

  public $type;

  /**
   * Initializes this object.
   *
   * @param OrgConfig $config
   *   The management Endpoint config object.
   * @param string $modelId
   *   The SmartDocs model Id.
   * @param string|int $revisionId
   *   The revision Id of the model.
   * @param string $type
   *   The type of security scheme, values can be
   *   -OAUTH2
   *   -APIKEY
   *   -BASIC
   */
  public function __construct(OrgConfig $config, $modelId, $revisionId, $type = NULL) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($modelId) . '/revisions/' . $revisionId . '/security');

    if (!empty($type)) {
      $this->type = $type;
    }
  }

  /**
   * Load all the available security schemes for a model's revision.
   * @return array
   *   An indexed array of security schemes.
   */
  public function loadAllSchemes() {
    try {
      $this->get();
      $schemes = $this->responseObj;
    }
    catch (ResponseException $e){

    }
    return !empty($schemes) ? $schemes : array();
  }

  /**
   * Load a give security scheme for a model.
   *
   * @param string $name
   *   The name of the scheme to load.
   *
   * @return object
   *   The scheme object. Based on the type of the scheme the object can be an
   *   instance of
   *   Apigee\SmartDocs\Oauth2Scheme
   *   Apigee\SmartDocs\ApikeyScheme
   *   Apigee\SmartDocs\BasicScheme
   */
  public function load($name) {
    try {
      $this->get($name);
      $this->type = $this->responseObj['type'];
      switch ($this->type) {
        case 'OAUTH2':
          $scheme = new Oauth2Scheme($this->responseObj);
          break;

        case 'APIKEY':
          $scheme = new ApikeyScheme($this->responseObj);
          break;

        case 'BASIC':
          $scheme = new BasicScheme($this->responseObj);
          break;
      }
    }
    catch (ResponseException $e) {

    }
    return $scheme;
  }

  /**
   * Save a security scheme.
   *
   * @param array $payload
   *   The payload to instantiate the appropriate scheme object.
   * @param bool $is_update
   *   This indicates whether to create a new scheme or to update and existing
   *   scheme.
   *
   * @throws ParameterException
   */
  public function save(array $payload, $is_update = FALSE) {
    if (empty($payload)) {
      throw new ParameterException('Cannot create a scheme with an empty payload');
    }
    switch ($this->type) {
      case 'OAUTH2':
        $oauth2scheme = new Oauth2Scheme($payload);
        try{
          $this->saveOauth2Scheme($oauth2scheme, $is_update);
        }
        catch (ResponseException $e) {

        }
        break;

      case 'APIKEY':
        $apikeyscheme = new ApikeyScheme($payload);
        try{
          $this->saveApikeyScheme($apikeyscheme, $is_update);
        }
        catch (ResponseException $e) {

        }
        break;

      case 'BASIC':
        $basicscheme = new BasicScheme($payload);
        try{
          $this->saveBasicScheme($basicscheme, $is_update);
        }
        catch (ResponseException $e) {

        }
        break;
    }
  }

  /**
   * Delete a particular scheme.
   *
   * @param string $name
   *   The name of the scheme to delete.
   */
  public function delete($name) {
    try {
      $this->http_delete($name);
    }
    catch (ResponseException $e) {

    }
  }

  /**
   * Save an Oauth 2.0 Scheme.
   *
   * @param \Apigee\SmartDocs\Oauth2Scheme $scheme
   *   The scheme object to save.
   * @param bool $is_update
   *   This indicates if a scheme should be created or updated.
   */
  public function saveOauth2Scheme(Oauth2Scheme &$scheme, $is_update = FALSE) {
    if ($is_update) {
      try{
        $payload = $scheme->toArray();
        unset($payload['type']);
        unset($payload['grantType']);
        $this->put($scheme->getName(), $payload);
      }
      catch (ResponseException $e) {

      }
    }
    else {
      try{
        $this->post(NULL, $scheme->toArray());
      }
      catch (ResponseException $e) {

      }
    }
    $scheme = new Oauth2Scheme($this->responseObj);
  }

  /**
   * Save an Apikey scheme.
   *
   * @param \Apigee\SmartDocs\ApikeyScheme $scheme
   *   The apikey scheme to be saved.
   * @param bool $is_update
   *   Indicates if a scheme should be created or updated.
   */
  public function saveApikeyScheme(ApikeyScheme &$scheme, $is_update = FALSE) {
    if ($is_update) {
      try{
        $payload = $scheme->toArray();
        unset($payload['type']);
        $this->put($scheme->getName(), $payload);
      }
      catch (ResponseException $e) {

      }
    }
    else {
      try{
        $this->post(NULL, $scheme->toArray());
      }
      catch (ResponseException $e) {

      }
    }
    $scheme = new ApikeyScheme($this->responseObj);
  }

  /**
   * Save a basic scheme.
   *
   * @param \Apigee\SmartDocs\BasicScheme $scheme
   *   The Basic scheme object to be saved.
   * @param bool $is_update
   *   Indicates if a scheme should be creaed or updated.
   */
  public function saveBasicScheme(BasicScheme &$scheme, $is_update = FALSE) {
    if ($is_update) {
      try{
        $payload = $scheme->toArray();
        unset($payload['type']);
        $this->put($scheme->getName(), $payload);
      }
      catch (ResponseException $e) {

      }
    }
    else {
      try{
        $this->post(NULL, $scheme->toArray());
      }
      catch (ResponseException $e) {

      }
    }
    $scheme = new BasicScheme($this->responseObj);
  }

}
