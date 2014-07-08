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

class DocGenResource extends APIObject implements DocGenResourceInterface
{

    /**
     * Constructs the proper values for the Apigee DocGen API.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(OrgCOnfig $config)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
    }

    /**
     * Loads all of the resources of a given revision with a model.
     *
     * {@inheritDoc}
     */
    public function loadResources($apiId, $revId)
    {
        $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '/resources?expand=yes');
        return $this->responseObj;
    }


    /**
     * Loads a single resource.
     *
     * {@inheritDoc}
     */
    public function loadResource($apiId, $revId, $resId)
    {
      // /{apiId}/revisions/{revisionId}/resources/{resourceId}
      $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '/resources/' . $resId);
      return $this->responseObj;
    }

    /**
     * Creates a resource for a given revision and model.
     *
     * @param $mid
     * @param $rev
     * @param $payload
     * @return array
     */
    public function createResource($apiId, $revId, $payload) {
      $this->post(rawurlencode($apiId) . '/revisions/' . $revId . '/resources', $payload, 'application/json; charset=utf-8');
      return $this->responseObj;
    }

    /**
     * Updates a resource for a given revision and model.
     *
     * @param $mid
     * @param $rev
     * @param $payload
     * @return array
     */
    public function updateResource($apiId, $revId, $resId, $payload) {
      $this->put(rawurlencode($apiId) . '/revisions/' . $revId . '/resources/' . $resId, $payload, 'application/json; charset=utf-8');
      return $this->responseObj;
    }
}