<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ResponseException;

/**
 * Abstraction of the AuthScheme subsystem of SmartDocs.
 *
 * Note that AuthSchemes are going away sometime in the near future, in favor of
 * Security.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class AuthScheme extends APIObject
{

    /**
     * Initializes this object.
     *
     * @param OrgConfig $config
     * @param string $modelId
     * @param string|int $revisionId
     */
    public function __construct(OrgConfig $config, $modelId, $revisionId)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($modelId) . '/revisions/' . $revisionId . '/authschemes');
    }

    /**
     * Returns list of all auth scheme names that are configured for the current
     * model & revision.
     *
     * @return array
     */
    public function listAuthSchemeNames()
    {
        $this->get();
        return $this->responseObj;
    }

    /**
     * Fetches and returns the 'custom' authscheme.
     *
     * @return TokenCredentials
     */
    public function loadTokenCredentials()
    {
        $this->get('custom');
        return new TokenCredentials($this->responseObj);
    }

    /**
     * Saves the 'custom' authscheme. It is presumed that this single method will
     * work for both inserts and updates.
     *
     * @param TokenCredentials $authscheme
     */
    public function saveTokenCredentials(TokenCredentials &$authscheme)
    {
        $this->post(null, $authscheme->toArray());
        $authscheme = new TokenCredentials($this->responseObj);
    }

    /**
     * Deletes the 'custom' authscheme.
     */
    public function deleteTokenCredentials()
    {
        $this->http_delete('custom');
    }

    /**
     * Fetches and returns the 'oauth2webserverflow' authscheme.
     *
     * @return Oauth2Credentials
     */
    public function loadOauth2Credentials()
    {
        $this->get('oauth2webserverflow');
        return new Oauth2Credentials($this->responseObj);
    }

    /**
     * Saves the 'oauth2webserverflow' authscheme.
     *
     * @param Oauth2Credentials $authscheme
     */
    public function saveOauth2Credentials(Oauth2Credentials &$authscheme)
    {
        $this->post(null, $authscheme->toArray());
        $authscheme = new Oauth2Credentials($this->responseObj);
    }

    /**
     * Deletes the 'oauth2webserverflow' authscheme.
     */
    public function deleteOauth2Credentials()
    {
        $this->http_delete('oauth2webserverflow');
    }
}

