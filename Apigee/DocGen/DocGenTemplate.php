<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * This class is deprecated. Please use Apigee\SmartDocs\Template instead.
 *
 * @author bhasselbeck
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

/**
 * Class DocGenTemplate
 * @deprecated
 * @package Apigee\DocGen
 */
class DocGenTemplate extends APIObject
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
     * Gets the template HTML that lists all of the operations of a given model.
     *
     * @param string $apiId
     * @param string $name
     * @return string
     */
    public function getIndexTemplate($apiId, $name)
    {
        $this->get(rawurlencode($apiId) . '/templates/' . $name . '?type=index', 'text/html');
        return $this->responseText;
    }

    /**
     * Gets the operation template for a specific model and template name
     *
     * @param string $apiId
     * @param string $name
     * @return string
     */
    public function getOperationTemplate($apiId, $name)
    {
        $this->get(rawurlencode($apiId) . '/templates/' . $name . '?type=method', 'text/html');
        return $this->responseText;
    }

    /**
     * Saves a type of template for a specific model
     *
     * @param string $apiId
     * @param string $type
     * @param string $name
     * @param string $html
     * @return string
     */
    public function saveTemplate($apiId, $type, $name, $html)
    {
        $this->post(rawurlencode($apiId) . '/templates?type=' . $type . '&name=' . $name, $html, 'text/html', 'text/html');
        return $this->responseText;
    }

    /**
     * Updates a type of template for a specific model
     *
     * @param string $apiId
     * @param string $type
     * @param string $name
     * @param string $html
     * @return string
     */
    public function updateTemplate($apiId, $type, $name, $html)
    {
        $uri = rawurlencode($apiId) . '/templates/' . $name . '?type=' . $type;
        $this->put($uri, $html, 'text/html', 'text/html');
        return $this->responseText;
    }

}