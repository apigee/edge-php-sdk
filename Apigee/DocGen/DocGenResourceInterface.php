<?php

namespace Apigee\DocGen;

interface DocGenResourceInterface
{
    public function loadResources($apiId, $revId);
    public function loadResource($apiId, $revId, $resId);
    public function createResource($apiId, $revId, $payload);
    public function updateResource($apiId, $revId, $resId, $payload);
}