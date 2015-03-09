<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;

/**
 * Fetches a unit of HTML documentation from SmartDocs.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Doc extends APIObject
{
    /**
     * Constructs the proper values for the Apigee DocGen API.
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param string $modelId
     * @param string $revisionId
     * @param string $resourceUuid
     * @param string $methodUuid
     */
    public function __construct(OrgConfig $config, $modelId, $revisionId, $resourceUuid, $methodUuid)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . $modelId . '/revisions/' . $revisionId . '/resources/' . $resourceUuid . '/methods/' . $methodUuid);
    }

    /**
     * Grabs the HTML blob of a given operation.
     *
     * @param string $templateName
     *
     * @return string
     */
    public function getHtml($templateName)
    {
        $this->get('doc?template=' . $templateName, 'text/html');
        return $this->responseText;
    }

}