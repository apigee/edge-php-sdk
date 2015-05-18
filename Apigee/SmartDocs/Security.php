<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ResponseException;
use Apigee\SmartDocs\Security\SecurityScheme;

class Security extends APIObject {

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
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($modelId) . '/revisions/' . $revisionId . '/security');
    }

    /**
     * Loads all schemes associated with the current model + revision.
     *
     * @return array
     *   Each member of the array is a subclass of SecurityScheme.
     */
    public function loadAllSchemes()
    {
        $scheme_objects = array();
        try {
            $this->get();
            $schemes = $this->responseObj;
            foreach ($schemes as $scheme_array) {
                $scheme_objects[] = SecurityScheme::fromArray($scheme_array);
            }
        }
        catch (ResponseException $e) {
        }
        return $scheme_objects;
    }

    /**
     * Loads a security scheme by name, or null if named scheme does not exist.
     *
     * @param string $name
     *
     * @return SecurityScheme|null
     */
    public function load($name)
    {
        $scheme = null;
        try {
            $this->get(rawurlencode($name));
            if (array_key_exists('type', $this->responseObj)) {
                $scheme = SecurityScheme::fromArray($this->responseObj);
            }
        }
        catch (ResponseException $e) {
        }
        return $scheme;
    }

    /**
     * Saves security scheme to modeling API.
     *
     * @param SecurityScheme $scheme
     * @param bool $is_update
     *   If true, will attempt a PUT; otherwise a POST. Be aware that if a PUT
     *   is attempted and the security scheme resource does not exist, the save
     *   will fail.
     *
     * @return SecurityScheme|null
     *   On success, returns the saved security scheme. On failure, returns
     *   null.
     */
    public function save(SecurityScheme $scheme, $is_update = false)
    {
        $payload = $scheme->toArray($is_update);
        if ($is_update) {
            $method = 'put';
            $path = rawurlencode($scheme->getName());
        }
        else {
            $method = 'post';
            $path = null;
        }
        try {
            $this->$method($path, $payload);
            return SecurityScheme::fromArray($this->responseObj);
        }
        catch (ResponseException $e) {
            return null;
        }
    }

    /**
     * Deletes a named security scheme from the Modeling API.
     *
     * @param string $name
     *   Name of the scheme to be deleted.
     */
    public function delete($name)
    {
        try {
            $this->http_delete(rawurlencode($name));
        }
        catch (ResponseException $e) {

        }
    }
}
