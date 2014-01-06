<?php

namespace Apigee\Mint\Types;

class BillingDocumentType extends Type {
  const REV_STMT = 'REV_STMT';
  const GPA = 'GPA';
  const NPA = 'NPA';
  const SELF_INVOICE = 'SELF_INVOICE';
  const INVOICE = 'INVOICE';
  const NETTING_STMT = 'NETTING_STMT';

  private function __construct() {}
}