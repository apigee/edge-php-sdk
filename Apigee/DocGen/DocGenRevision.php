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
     * Loads a verbose object of a revision.
     *
     * {@inheritDoc}
     */
    public function loadVerbose($apiId, $revId)
    {
        $this->get(rawurlencode($apiId) . '/revisions/' . $revId . '?expand=yes');
        return $this->responseObj;
    }

}