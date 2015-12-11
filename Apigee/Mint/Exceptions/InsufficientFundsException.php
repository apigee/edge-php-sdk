<?php

namespace Apigee\Mint\Exceptions;

use Apigee\Exceptions\ResponseException;
use Apigee\Exceptions\ParameterException;

class InsufficientFundsException extends MintApiException
{

    const INSUFFICIENT_FUNDS = 'mint.insufficientFunds';

    const CONTEXT_COST_RATE_PLAN = 'Rate Plan';
    const CONTEXT_COST_TAX = 'Tax';
    const CONTEXT_COST_TOTAL = 'Total Rate Plan';

    private $ratePlan = null;
    private $tax = null;
    private $totalRatePlan = null;
    private $hasCostDetails = null;

    /**
     * Hold the exception codes relative to Mint API
     *
     * @var array
     */
    private static $codes = array(
        self::INSUFFICIENT_FUNDS => 'Developer does not have sufficient funds to proceed',
    );

    /**
     * Determines if this exception is relative to the Mint API REST call
     *
     * @param \Apigee\Exceptions\ResponseException $e
     * @return boolean
     */
    public static function isInsufficientFundsException(ResponseException $e)
    {
        $error_info = json_decode($e->getResponse());
        return isset($error_info->code) && array_key_exists($error_info->code, self::$codes);
    }

    /**
     * Class constructor
     *
     * @param ResponseException $e
     * @throws ParameterException if the exception has no Mint
     *   registered code
     */
    public function __construct(ResponseException $e)
    {
        parent::__construct($e);
        if (!self::isInsufficientFundsException($e)) {
            throw new ParameterException(
                'Improper response exception message passed into InsufficientFundsException class constructor.',
                $e
            );
        }
        $errorInfo = json_decode($e->getResponse());

        if (empty($errorInfo->contexts)) {
            // If contexts are empty, then we do not have the cost breakdown data.
            $this->hasCostDetails = false;
            // Get the total cost
            $amtStartsAt = strpos($this->mintMessage, '[') + 1;
            $substring = substr($this->mintMessage, $amtStartsAt, strlen($this->mintMessage) - $amtStartsAt - 1);
            $required = str_replace(',', '', $substring);
            $this->totalRatePlan = $required;
        } else {
            // We have the cost details, collect them into proper attributes.
            $this->hasCostDetails = true;
            foreach ($errorInfo->contexts as $context) {
                switch ($context->name) {
                    case self::CONTEXT_COST_TOTAL:
                        $this->totalRatePlan = $context->value;
                        break;
                    case self::CONTEXT_COST_TAX:
                        $this->tax = $context->value;
                        break;
                    case self::CONTEXT_COST_RATE_PLAN:
                        $this->ratePlan = $context->value;
                        break;
                }
            }
        }
    }

    /**
     * If there is a proper message then it is returned; otherwise NULL is
     * returned.
     *
     * @param bool $response_message
     * @param bool $no_code
     *
     * @return string|null
     */
    public function getMintMessage($response_message = false, $no_code = false)
    {
        return $response_message
            ? (!$no_code ? $this->mintCode . ': ' : '') . $this->mintMessage
            : self::$codes[$this->mintCode];
    }

    /**
     * Get the Rate Plan cost without the tax.
     * @return string The rate plan cost
     */
    public function getRatePlanCost()
    {
        return $this->ratePlan;
    }

    /**
     * Get the tax only cost of the plan.
     * @return string the tax cost
     */
    public function getTaxCost()
    {
        return $this->tax;
    }

    /**
     * Get the total cost of the plan.
     * @return string the total cost
     */
    public function getTotalCost()
    {
        return $this->totalRatePlan;
    }

    public function hasCostDetails()
    {
        return $this->hasCostDetails;
    }
}
