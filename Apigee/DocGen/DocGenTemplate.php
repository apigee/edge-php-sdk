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

class DocGenTemplate extends APIObject implements DocGenTemplateInterface {

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
  public function getIndexTemplate($apiId) {
    $this->get(rawurlencode($apiId) . '/docTemplate?type=index', 'text/html');
    return $this->responseText;
  }

  /**
   * {@inheritDoc}
   */
  public function getOperationTemplate($apiId) {
    $this->get(rawurlencode($apiId) . '/docTemplate?type=method', 'text/html');
    return $this->responseText;
  }

  /**
   * {@inheritDoc}
   */
  public function saveTemplate($apiId, $type, $html) {
    $headers = array();
    $this->post(rawurlencode($apiId) . '/docTemplate?type=' . $type, $html, 'text/html', 'text/html', $headers);
    return $this->responseText;
  }

}