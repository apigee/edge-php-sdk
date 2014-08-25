<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * @author bhasselbeck
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

class DocGenMethod extends APIObject implements DocGenMethodInterface
{

    /**
     * Constructs the proper values for the Apigee DocGen API.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(OrgConfig $config)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
    }

    /**
     * Updates a method
     *
     * @param $apiId
     * @param $revisionId
     * @param $resourceId
     * @param $methodId
     * @param $payload
     * @return array|string
     */
    public function updateMethod($apiId, $revisionId, $resourceId, $methodId, $payload)
    {
      $path = rawurlencode($apiId) . '/revisions/' . $revisionId . '/resources/' . $resourceId . '/methods/' . $methodId;
      $this->put($path, $payload, 'application/json', array(), array());
      return $this->responseObj;
    }

    /**
     * Creates a method
     *
     * @param $apiId
     * @param $revisionId
     * @param $resourceId
     * @param $payload
     * @return array
     */
    public function createMethod($apiId, $revisionId, $resourceId, $payload)
    {
      $this->post(rawurlencode($apiId) . '/revisions/' . $revisionId . '/resources/' . $resourceId . '/methods', $payload, 'application/json; charset=utf-8');
      return $this->responseObj;
    }

    /**
     * Gets a method
     *
     * @param $apiId
     * @param $revisionId
     * @param $resourceId
     * @param $methodId
     * @return array
     */
    public function getMethod($apiId, $revisionId, $resourceId, $methodId)
    {
      $path = rawurlencode($apiId) . '/revisions/' . $revisionId . '/resources/' . $resourceId . '/methods/' . $methodId;
      $this->get($path, 'application/json', array(), array());
      return $this->responseObj;
    }

}