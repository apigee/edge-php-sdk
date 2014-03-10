<?php

namespace Apigee\DocGen;

interface DocGenResourceInterface
{
    public function loadResources($apiId, $revId);
}