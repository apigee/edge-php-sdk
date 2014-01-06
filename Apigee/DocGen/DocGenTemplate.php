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

class DocGenTemplate extends APIObject implements DocGenTemplateInterface {

  /**
   * Constructs the proper values for the Apigee DocGen API
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgConfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

  /**
   * Gets Index Template
   *
   * @param $apiId
   * @return array|string
   */
  public function getIndexTemplate($apiId) {
    $this->get(rawurlencode($apiId) . '/docTemplate?type=index', 'text/html');
    return $this->responseText;
  }

  /**
   * Gets Operation HTML
   *
   * @param $apiId
   * @return array|string
   */
  public function getOperationTemplate($apiId) {
    $this->get(rawurlencode($apiId) . '/docTemplate?type=method', 'text/html');
    return $this->responseText;
  }

  /**
   * Saves the template back to the modeling API
   */
  public function saveTemplate($apiId, $type, $html) {
    $headers = array();
    $this->post(rawurlencode($apiId) . '/docTemplate?type=' . $type, $html, 'text/html', 'text/html', $headers);
    return $this->responseText;
  }

}