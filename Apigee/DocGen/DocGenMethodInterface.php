<?php

namespace Apigee\DocGen;

interface DocGenMethodInterface
{

    /**
     * Updates an Operation.
     *
     * /{apiId}/revisions/{revisionId}/resources/{resourceId}/methods/{methodId}
     *
     * @param $apiId
     * @param $update
     * @return array
     */
    public function updateMethod($apiId, $revisionId, $resourceId, $methodId, $payload);
    public function createMethod($apiId, $revisionId, $resourceId, $payload);

}