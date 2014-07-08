<?php

namespace Apigee\DocGen;

interface DocGenRevisionInterface
{
    public function loadVerbose($apiId, $revId);
    public function getAllRevisions($apiId);
    public function newRevision($apiId, $payload);
}