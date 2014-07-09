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

class DocGenRevision extends APIObject implements DocGenRevisionInterface
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
     * {@inheritDoc}
     */
    public function getAllRevisions($apiId)
    {
      $this->get(rawurlencode($apiId) . '/revisions');
      return $this->responseObj;
    }

    /**
     * Gets all of the revisions for a given model.
     *
     * {@inheritDoc}
     */
    public function getRevision($apiId, $revId)
    {
      $this->get(rawurlencode($apiId) . '/revisions/' . $revId);
      return $this->responseObj;
    }

    /**
     * Gets all of the revisions for a given model.
     *
     * {@inheritDoc}
     */
    public function addAuth($apiId, $rev, $auth)
    {
      $path = rawurlencode($apiId) . '/revisions/' . $rev . '/authschemes';
      $this->post($path, $auth, 'application/json; charset=utf-8');
      return $this->responseObj;
    }

    /**
     * Gets all of the revisions for a given model.
     *
     * {@inheritDoc}
     */
    public function updateAuth($apiId, $rev, $auth)
    {
      $path = rawurlencode($apiId) . '/revisions/' . $rev . '/authschemes';
      $this->post($path, $auth, 'application/json; charset=utf-8');
      return $this->responseObj;
    }

    /**
     * Gets all of the revisions for a given model.
     *
     * {@inheritDoc}
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
     * Gets all of the revisions for a given model.
     *
     * {@inheritDoc}
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
     * Loads a verbose object of a revision.
     *
     * {@inheritDoc}
     */
    public function loadVerbose($apiId, $revId)
    {
        $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '?expand=yes');
        return $this->responseObj;
    }

    /**
     * Makes a new revision for a given model
     *
     * @param $apiId
     * @param $payload
     */
    public function newRevision($apiId, $payload)
    {
        $this->post(rawurlencode($apiId) . '/revisions', $payload, 'application/json; charset=utf-8');
        return $this->responseObj;
    }

    /**
     * Makes a new revision for a given model
     *
     * @param $apiId
     * @param $payload
     */
    public function updateRevision($apiId, $revId, $payload)
    {
      $this->put(rawurlencode($apiId) . '/revisions/' . $revId, $payload, 'application/json; charset=utf-8');
      return $this->responseObj;
    }

}