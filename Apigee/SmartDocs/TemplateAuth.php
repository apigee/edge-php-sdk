<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ResponseException;
use Apigee\SmartDocs\Security\TemplateAuthScheme;

class TemplateAuth extends APIObject
{

    /**
     * Initializes this object.
     *
     * @param \Apigee\Util\OrgConfig $config
     *   The management endpoint config.
     * @param string $modelId
     *   The model id
     */
    public function __construct(OrgConfig $config, $modelId)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($modelId) . '/templateauths');
    }

    /**
     * Load a template auth scheme by name.
     *
     * @param string $name
     *   The name of the scheme to load.
     *
     * @return object
     *   A scheme object.
     */
    public function load($name)
    {
        $scheme = null;
        try {
            $this->get(rawurlencode($name));
            if (array_key_exists('type', $this->responseObj)) {
                $scheme = TemplateAuthScheme::fromArray($this->responseObj);
            }
        }
        catch (ResponseException $e) {
        }
        return $scheme;
    }

    /**
     * Loads all the template auth schemes associated with the current model.
     *
     * @return array
     *   An array of TemplateAuthScheme subclasses.
     */
    public function loadAllSchemes()
    {
        try {
            $this->get();
            $schemes = array();
            foreach ($this->responseObj as $scheme_array) {
                $scheme = TemplateAuthScheme::fromArray($scheme_array);
                $schemes[$scheme->getName()] = $scheme;
            }
        }
        catch (ResponseException $e) {

        }
        return !empty($schemes) ? $schemes : array();
    }

    /**
     * Saves a template auth scheme to modeling Api.
     *
     * @param TemplateAuthScheme $scheme
     * @param bool $is_update
     * If true, will attempt a PUT; otherwise a POST. Be aware that if a PUT
     * is attempted and the security scheme resource does not exist, the save
     * will fail.
     */
    public function save(TemplateAuthScheme $scheme, $is_update = FALSE)
    {

        $payload = $scheme->toArray($is_update);
        if ($is_update) {
            $method = 'put';
            $path   = rawurlencode($scheme->getName());
        } else {
            $method = 'post';
            $path   = null;
        }
        try {
            $this->$method($path, $payload);
            return TemplateAuthScheme::fromArray($this->responseObj);
        }
        catch (ResponseException $e) {
            return null;
        }
    }

    /**
     * Delete a template auth scheme.
     *
     * @param string $name
     * The name of the template auth scheme to delete.
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
