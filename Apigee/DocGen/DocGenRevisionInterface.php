<?php

namespace Apigee\DocGen;

interface DocGenRevisionInterface
{
    public function loadVerbose($apiId, $revId);
    public function getAllRevisions($apiId);
    public function getRevision($apiId, $revId);
    public function addAuth($apiId, $rev, $auth);
    public function updateAuth($apiId, $rev, $auth);
    public function getOAuthCredentials($apiId, $rev);
    public function getTokenCredentials($apiId, $rev);
    public function newRevision($apiId, $payload);
    public function updateRevision($apiId, $revId, $payload);
}