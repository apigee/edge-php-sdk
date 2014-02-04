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

class DocGenDoc extends APIObject implements DocGenDocInterface {

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
  public function requestOperation($data, $mid) {
    $path = $mid . '/revisions/' . $data['revision'] . '/resources/' . $data['resource'] . '/methods/' . $data['operation'] . '/doc';
    $this->get($path, 'text/html');
    return $this->responseText;
  }

}