<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * @author Brian Hasselbeck <bhasselbeck@apigee.com>
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

class DocGenModel extends APIObject implements DocGenModelInterface {

  /**
   * Constructs the proper values for the Apigee DocGen API
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgConfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

  /**
   * Adds an API resource, with a name and a description.
   * The actual API description is added using a different set of methods
   * that are described in the following sections.
   *
   * @param array $payload
   * @return array
   */
  public function createModel($payload = array()) {
    $this->post(NULL, $payload);
    return $this->responseObj;
  }

  /**
   * Returns the descriptions of all APIs in the organization.
   *
   * @return array
   */
  public function getModels() {
    $this->get();
    return $this->responseObj;
  }

  /**
   * Imports the given API description to apihub repository.
   *
   * @param string $apiId
   * @param string $xml
   * @return array
   */
  public function importWADL($apiId, $xml) {
    $this->post(rawurlencode($apiId) . '/revisions?action=import&format=WADL', $xml, 'application/xml; charset=utf-8');
    return $this->responseObj;
  }

  /**
   * Returns the details of an API, such as its name, description, list of revisions and metadata.
   *
   * @param string $apiId
   * @return array
   */
  public function getModel($apiId) {
    $this->get(rawurlencode($apiId));
    return $this->responseObj;
  }

  /**
   * Updates an API resource.
   *
   * @param $apiId
   * @param $update
   * @return array
   */
  public function updateModel($apiId, $update) {
    $this->put(rawurlencode($apiId), $update);
    return $this->responseObj;
  }

  /**
   * Deletes an API resource and all its associated data.
   *
   * @param $apiId
   * @return array
   */
  public function deleteModel($apiId) {
    $this->http_delete(rawurlencode($apiId));
    return $this->responseObj;
  }

}