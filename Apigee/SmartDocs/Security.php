<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ResponseException;
use Apigee\SmartDocs\Security\SecurityScheme;

class Security extends APIObject
{

  /**
   * Initializes the Security object and sets its base URL.
   *
   * @param OrgConfig $config
   *   Contains configuration info for connecting to the Modeling API.
   * @param string $modelId
   *   Model name or UUID for which we want security info.
   * @param string $revisionId
   *   Revision number or UUID for which we want security info.
   */
    public function __construct(OrgConfig $config, $modelId, $revisionId)
    {
        $baseUrl = '/o/' . rawurlencode($config->orgName)
            . '/apimodels/' . rawurlencode($modelId)
            . '/revisions/' . $revisionId
            . '/security';
        $this->init($config, $baseUrl);
    }

    /**
     * Loads all schemes associated with the current model + revision.
     *
     * @throws ResponseException
     *
     * @return array
     *   Each member of the array is a subclass of SecurityScheme.
     */
    public function loadAllSchemes()
    {
        $scheme_objects = array();
        $this->get();
        $schemes = $this->responseObj;
        foreach ($schemes as $scheme_array) {
            $scheme_objects[] = SecurityScheme::fromArray($scheme_array);
        }

        return $scheme_objects;
    }

    /**
     * Loads a security scheme by name.
     *
     * @throws ResponseException
     *
     * @param string $name
     *
     * @return SecurityScheme
     */
    public function load($name)
    {
        $scheme = null;

        $this->get(rawurlencode($name));
        if (array_key_exists('type', $this->responseObj)) {
            $scheme = SecurityScheme::fromArray($this->responseObj);
        }

        return $scheme;
    }

    /**
     * Saves security scheme to modeling API.
     *
     * @throws ResponseException
     *
     * @param SecurityScheme $scheme
     * @param bool $is_update
     *   If true, will attempt a PUT; otherwise a POST. Be aware that if a PUT
     *   is attempted and the security scheme resource does not exist, the save
     *   will fail.
     *
     * @return SecurityScheme
     *   On success, returns the saved security scheme.
     */
    public function save(SecurityScheme $scheme, $is_update = false)
    {
        $payload = $scheme->toArray($is_update);
        if ($is_update) {
            $method = 'put';
            $path = rawurlencode($scheme->getName());
        } else {
            $method = 'post';
            $path = null;
        }
        $this->$method($path, $payload);
        return SecurityScheme::fromArray($this->responseObj);
    }

    /**
     * Deletes a named security scheme from the Modeling API.
     *
     * @throws ResponseException
     *
     * @param string $name
     *   Name of the scheme to be deleted.
     */
    public function delete($name)
    {
        $this->httpDelete(rawurlencode($name));
    }
}
