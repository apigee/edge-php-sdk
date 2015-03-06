<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;

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
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($modelId) . '/templates');
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
            throw new ParameterException('Invalid template type ‘' . $type . '’ (valid values are ‘index’ and ‘method’).');
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
    public function save($name, $type, $html, $update = FALSE)
    {
        if ($type != 'index' && $type != 'method') {
            throw new ParameterException('Invalid template type ‘' . $type . '’ (valid values are ‘index’ and ‘method’).');
        }
        if ($update) {
            $uri = rawurlencode($name) . '?type=' . $type;
            $method = 'put';
        } else {
            $uri = '?type=' . $type . '&name=' . urlencode($name);
            $method = 'post';
        }
        $this->$method($uri, $html, 'text/html', 'text/html');
        return $this->responseText;
    }

    // TODO: delete?
}
