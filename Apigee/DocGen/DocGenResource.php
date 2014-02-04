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

class DocGenResource extends APIObject implements DocGenResourceInterface {

  /**
   * Constructs the proper values for the Apigee DocGen API.
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgCOnfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

  public function loadResources($apiId, $revId) {
    $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '/resources?expand=yes');
    return $this->responseObj;
  }

}