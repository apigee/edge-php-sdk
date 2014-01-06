<?php

namespace Apigee\Mint\Types;

class ApplicationStatusType extends Type {
  const ACTIVE = 'ACTIVE';
  const INACTIVE = 'INACTIVE';
  const BLACKLISTED = 'BLACKLISTED';

  private function __construct() {}
}