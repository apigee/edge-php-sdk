<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * This class is deprecated. Please use Apigee\SmartDocs\Revision instead.
 *
 * @author bhasselbeck
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

/**
 * Class DocGenRevision
 * @deprecated
 * @package Apigee\DocGen
 */
class DocGenRevision extends APIObject
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
     * Gets all of the revisions for a given model.
     *
     * @param string $apiId
     * @return array
     */
    public function getAllRevisions($apiId)
    {
        $this->get(rawurlencode($apiId) . '/revisions');
        return $this->responseObj;
    }

    /**
     * Gets all of the revisions for a given model.
     *
     * @param string $apiId
     * @param int|string $revId
     * @return array
     */
    public function getRevision($apiId, $revId)
    {
        $this->get(rawurlencode($apiId) . '/revisions/' . $revId);
        return $this->responseObj;
    }

    /**
     * Adds an authscheme to a revision.
     *
     * @param string $apiId
     * @param int|string $rev
     * @param array $auth
     * @return array
     */
    public function addAuth($apiId, $rev, $auth)
    {
        $path = rawurlencode($apiId) . '/revisions/' . $rev . '/authschemes';
        $this->post($path, $auth);
        return $this->responseObj;
    }

    /**
     * Updates an authscheme in a revision.
     *
     * @param string $apiId
     * @param int|string $rev
     * @param array $auth
     * @return array
     */
    public function updateAuth($apiId, $rev, $auth)
    {
        $path = rawurlencode($apiId) . '/revisions/' . $rev . '/authschemes';
        $this->post($path, $auth);
        return $this->responseObj;
    }

    /**
     * Gets OAuth credentials for a revision.
     *
     * @param string $apiId
     * @param int|string $rev
     * @return array
     */
    public function getOAuthCredentials($apiId, $rev)
    {
        try {
            $path = rawurlencode($apiId) . '/revisions/' . $rev . '/authschemes/oauth2webserverflow';
            $this->get($path);
            return array(
                'code' => (int)$this->responseCode,
                'data' => $this->responseObj
            );
        } catch (\Exception $e) {
            return $this->responseCode;
        }
    }

    /**
     * Gets Custom Token credentials for a revision.
     *
     * @param string $apiId
     * @param int|string $rev
     * @return array
     */
    public function getTokenCredentials($apiId, $rev)
    {
        try {
            $path = rawurlencode($apiId) . '/revisions/' . $rev . '/authschemes/custom';
            $this->get($path);
            return array(
                'code' => (int)$this->responseCode,
                'data' => $this->responseObj
            );
        } catch (\Exception $e) {
            return $this->responseCode;
        }
    }

    /**
     * Loads a revision verbosely.
     *
     * @param string $apiId
     * @param int|string $rev
     * @return array
     */
    public function loadVerbose($apiId, $revId)
    {
        $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '?expand=yes');
        return $this->responseObj;
    }

    /**
     * Makes a new revision for a given model
     *
     * @param string $apiId
     * @param array $payload
     */
    public function newRevision($apiId, $payload)
    {
        $this->post(rawurlencode($apiId) . '/revisions', $payload);
        return $this->responseObj;
    }

    /**
     * Makes a new revision for a given model
     *
     * @param string $apiId
     * @param array $payload
     */
    public function updateRevision($apiId, $revId, $payload)
    {
        $this->put(rawurlencode($apiId) . '/revisions/' . $revId, $payload);
        return $this->responseObj;
    }

}