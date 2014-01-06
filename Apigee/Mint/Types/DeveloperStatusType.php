<?php

namespace Apigee\Mint\Types;

class DeveloperStatusType extends Type {
  const ACTIVE = 'ACTIVE';
  const INACTIVE = 'INACTIVE';
  const BLACKLISTED = 'BLACKLISTED';

  private function __construct() {}
}