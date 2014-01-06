<?php

namespace Apigee\DocGen;

interface DocGenRevisionInterface {
  public function loadVerbose($apiId, $revId);
}