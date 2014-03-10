<?php

namespace Apigee\Mint\Types;

final class QuotaType extends Type
{
    const TRANSACTIONS = 'Transactions';
    const CREDIT_LIMIT = 'CreditLimit';
    const SPEND_LIMIT = 'SpendLimit';
    const FEE_EXPOSURE = 'FeeExposure';
    const BALANCE = 'Balance';
}