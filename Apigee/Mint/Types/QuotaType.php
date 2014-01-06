<?php

namespace Apigee\Mint\Types;

final class QuotaType extends Type {
  const Transactions = 'Transactions';
  const CreditLimit = 'CreditLimit';
  const SpendLimit = 'SpendLimit';
  const FeeExposure = 'FeeExposure';
  const Balance = 'Balance';

  private function __construct() {
  }
}