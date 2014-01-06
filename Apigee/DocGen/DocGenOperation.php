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

class DocGenOperation extends APIObject implements DocGenOperationInterface {

  /**
   * Constructs the proper values for the Apigee DocGen API
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgConfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

}