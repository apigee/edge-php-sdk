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

class DocGenDoc extends APIObject implements DocGenDocInterface {

  /**
   * Constructs the proper values for the Apigee DocGen API
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgConfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

  /**
   * Grabs the html of a given operation
   *
   * @param $data = array('nid', 'revision', 'resource', 'operation')
   *     Revision, Resource, and Operation should all be UUIDs
   * @param $mid
   * @return array
   */
  public function requestOperation($data, $mid) {
    $path = $mid . '/revisions/' . $data['revision'] . '/resources/' . $data['resource'] . '/methods/' . $data['operation'] . '/doc';
    $this->get($path, 'text/html');
    return $this->responseText;
  }

}