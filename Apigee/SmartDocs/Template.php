<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;
use Apigee\Exceptions\ResponseException;

/**
 * Loads and saves template HTML.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Template extends APIObject
{

    /**
     * Initializes this object's base URL.
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param string $modelId
     */
    public function __construct(OrgConfig $config, $modelId)
    {
        $baseUrl = '/o/' . rawurlencode($config->orgName)
            . '/apimodels/' . rawurlencode($modelId)
            . '/templates';
        $this->init($config, $baseUrl);
    }

    /**
     * Loads a template of a given name and type.
     *
     * @param string $name
     * @param string $type
     * @return string
     * @throws ParameterException
     */
    public function load($name, $type)
    {
        if ($type != 'index' && $type != 'method') {
            $message = 'Invalid template type ‘%s’ (valid values are ‘index’ and ‘method’).';
            throw new ParameterException(sprintf($message, $type));
        }
        $this->get($name . '?type=' . $type, 'text/html');
        return $this->responseText;
    }

    /**
     * Saves HTML to a template of a given name and type.
     *
     * @param string $name
     * @param string $type
     * @param string $html
     * @param bool $update
     * @return string
     * @throws ParameterException
     */
    public function save($name, $type, $html, $update = false)
    {
        if ($type != 'index' && $type != 'method') {
            $message = 'Invalid template type ‘%s’ (valid values are ‘index’ and ‘method’).';
            throw new ParameterException(sprintf($message, $type));
        }
        if ($update) {
            $uri = rawurlencode($name) . '?type=' . $type;
            $method = 'put';
        } else {
            $uri = '?type=' . $type . '&name=' . urlencode($name);
            $method = 'post';
        }
        try {
            $this->$method($uri, $html, 'text/html', 'text/html');
        } catch (ResponseException $e) {
            // If update failed, try insert.
            if ($update && $e->getCode() == 404) {
                $this->save($name, $type, $html, false);
            } else {
                throw $e;
            }
        }
        return $this->responseText;
    }

    // TODO: delete?
}
