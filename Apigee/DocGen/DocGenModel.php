<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * @author Brian Hasselbeck
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

class DocGenModel extends APIObject implements DocGenModelInterface {

  /**
   * Constructs the proper values for the Apigee DocGen API.
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgConfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

  /**
   * {@inheritDoc}
   */
  public function createModel($payload = array()) {
    $this->post(NULL, $payload);
    return $this->responseObj;
  }

  /**
   * {@inheritDoc}
   */
  public function getModels() {
    $this->get();
    return $this->responseObj;
  }

  /**
   * {@inheritDoc}
   */
  public function importWADL($apiId, $xml) {
    $this->post(rawurlencode($apiId) . '/revisions?action=import&format=WADL', $xml, 'application/xml; charset=utf-8');
    return $this->responseObj;
  }

  /**
   * {@inheritDoc}
   */
  public function getModel($apiId) {
    $this->get(rawurlencode($apiId));
    return $this->responseObj;
  }

  /**
   * {@inheritDoc}
   */
  public function updateModel($apiId, $update) {
    $this->put(rawurlencode($apiId), $update);
    return $this->responseObj;
  }

  /**
   * {@inheritDoc}
   */
  public function deleteModel($apiId) {
    $this->http_delete(rawurlencode($apiId));
    return $this->responseObj;
  }

}