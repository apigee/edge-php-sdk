<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * This class is deprecated. Please use Apigee\SmartDocs\Resource instead.
 *
 * @author bhasselbeck
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

/**
 * Class DocGenResource
 * @deprecated
 * @package Apigee\DocGen
 */
class DocGenResource extends APIObject
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
     * @param string $apiId
     * @param int|string $revId
     * @return array
     */
    public function loadResources($apiId, $revId)
    {
        $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '/resources?expand=yes');
        return $this->responseObj;
    }


    /**
     * Loads a single resource.
     *
     * @param string $apiId
     * @param int|string $revId
     * @param string $resId
     * @return array
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
     * @param string $apiId
     * @param int|string $revId
     * @param array $payload
     * @return array
     */
    public function createResource($apiId, $revId, $payload) {
        $this->post(rawurlencode($apiId) . '/revisions/' . $revId . '/resources', $payload);
        return $this->responseObj;
    }

    /**
     * Updates a resource for a given revision and model.
     *
     * @param string $apiId
     * @param int|string $revId
     * @param string $resId
     * @param array $payload
     * @return array
     */
    public function updateResource($apiId, $revId, $resId, $payload) {
        $this->put(rawurlencode($apiId) . '/revisions/' . $revId . '/resources/' . $resId, $payload);
        return $this->responseObj;
    }
}