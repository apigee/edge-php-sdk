<?php

namespace Apigee\Mint\Exceptions;

use Apigee\Exceptions\ParameterException;
use Apigee\Exceptions\ResponseException;

class MintApiException extends \Exception
{
    protected $mintCode;

    protected $mintMessage;

    const DEVELOPER_LEGAL_COMPANY_NAME_NOT_SPECIFIED = 'mint.developerLegalCompanyNameNotSpecified';
    const DEVELOPER_ADDRESS_NOT_SPECIFIED = 'mint.developerAddressNotSpecified';
    const RATE_PLAN_NOT_PUBLISHED = 'mint.ratePlanNotPublished';
    const START_DATE_EARLIER_THAN_TODAY = 'mint.startDateEarlierThanToday';
    const DEVELOPER_RATE_PLAN_ACCEPT_ERROR = 'mint.developerRatePlanAcceptError';
    const START_DATE_EARLIER_THAN_PLAN_START_DATE = 'mint.startDateEarlierThanPlanStartDate';
    const START_DATE_LATER_THAN_PLAN_END_DATE = 'mint.startDateLaterThanPlanEndDate';
    const END_DATE_EARLIER_THAN_START = 'mint.endDateEarlierThanStart';
    const END_DATE_LATER_THAN_PLAN_END_DATE = 'mint.endDateLaterThanPlanEndDate';
    const DEVELOPER_ALREADY_ACCEPTED_RATE_PLAN_FOR_PRODUCT = 'mint.developerAlreadyAcceptedRatePlanForProduct';
    const DEVELOPER_ALREADY_ACTIVE_ON_RATE_PLAN = 'mint.developerAlreadyActiveOnRatePlan';
    const RESOURCE_DOES_NOT_EXIST = 'mint.resourceDoesNotExist';
    const DEVELOPER_HAS_FOLLOWING_OVERLAP_RATE_PLANS = 'mint.developerHasFollowingOverlapRatePlans';
    const DEVELOPER_ON_RATE_PLAN_WITH_START_DATE = 'mint.developerOnRatePlanWithStartDate';
    const PREPAID_DEVELOPER_HAS_NO_BALANCE = 'mint.prepaidDeveloperHasNoBalance';
    const ONLY_FUTURE_DEVELOPER_RATE_PLAN_CAN_BE_DELETED = 'mint.onlyFutureDeveloperRatePlanCanBeDeleted';
    const PRODUCT_NOT_PART_OF_ANY_MONETIZATION_PACKAGE = 'mint.productNotPartOfAnyMonetizationPackage';
    const NO_CURRENT_PUBLISHABLE_ENTITY = 'mint.noCurrentPublishableEntity';
    /**
     * Hold the exception codes relative to Mint API
     *
     * @var array
     */
    private static $codes = array(
        self::DEVELOPER_LEGAL_COMPANY_NAME_NOT_SPECIFIED => 'Company name not specified, you need to specify company legal name to be able to accept a plan',
        self::DEVELOPER_ADDRESS_NOT_SPECIFIED => 'Address not specified, you need to specify address in your company profile to be able to accept a plan',
        self::RATE_PLAN_NOT_PUBLISHED => 'Plans cannot be added until they are published.',
        self::START_DATE_EARLIER_THAN_TODAY => 'Start date should not be earlier than today.',
        self::START_DATE_EARLIER_THAN_PLAN_START_DATE => 'You cannot add a plan with a start date earlier than a plan start date.',
        self::START_DATE_LATER_THAN_PLAN_END_DATE => 'You cannot add a rate plan with start date going beyond rate plan end date',
        self::END_DATE_LATER_THAN_PLAN_END_DATE => 'You cannot end a rate plan with end date going beyond rate plan end date',
        self::END_DATE_EARLIER_THAN_START => 'You cannot end a plan with end date earlier than start date',
        self::DEVELOPER_ALREADY_ACCEPTED_RATE_PLAN_FOR_PRODUCT => 'You are adding a plan that overlaps with existing plans/products supported by existing plans.',
        self::DEVELOPER_ALREADY_ACTIVE_ON_RATE_PLAN => 'You are adding a plan that is already active',
        self::DEVELOPER_RATE_PLAN_ACCEPT_ERROR => 'Error accepting rate plan',
        self::RESOURCE_DOES_NOT_EXIST => null,
        self::DEVELOPER_HAS_FOLLOWING_OVERLAP_RATE_PLANS => null,
        self::DEVELOPER_ON_RATE_PLAN_WITH_START_DATE => null,
        self::PREPAID_DEVELOPER_HAS_NO_BALANCE => null,
        self::ONLY_FUTURE_DEVELOPER_RATE_PLAN_CAN_BE_DELETED => 'Delete operation allowed only on future dated developer rate plan',
        self::PRODUCT_NOT_PART_OF_ANY_MONETIZATION_PACKAGE => null,
        self::NO_CURRENT_PUBLISHABLE_ENTITY => null,
    );

    /**
     * Factory method to create the proper exception class depending on the error.
     *
     * @param ResponseException $re The response exception from the Edge API
     *
     * @return MintApiException MintApiException or a subclass of MintApiException
     */
    public static function factory(ResponseException $re)
    {
        if (InsufficientFundsException::isInsufficientFundsException($re)) {
            return new InsufficientFundsException($re);
        }
        elseif (MintApiException::isMintExceptionCode($re)) {
            return new MintApiException($re);
        } else {
            throw new ParameterException('Not a registered mint exception.', $re);
        }
    }

    /**
     * Determines if this exception is relative to the Mint API REST call
     *
     * @param \Apigee\Exceptions\ResponseException $e
     * @return boolean
     */
    public static function isMintExceptionCode(ResponseException $e)
    {
        $error_info = json_decode($e->getResponse());
        return isset($error_info->code) && array_key_exists($error_info->code, self::$codes);
    }

    /**
     * Class constructor
     *
     * @param \Apigee\Exceptions\ResponseException $e
     * @return boolean
     * @throws \Apigee\Exceptions\ParameterException if the exception has not a mint
     * registered code
     */
    public function __construct($e)
    {
        parent::__construct($e->getMessage(), $e->getCode(), $e);
        $error_info = json_decode($e->getResponse());
        $this->mintCode = $error_info->code;
        $this->mintMessage = $error_info->message;
    }

    public function getMintCode()
    {
        return $this->mintCode;
    }

    /**
     * @return string|null if there is a proper message then it is returned,
     * otherwise NULL is return
     */
    public function getMintMessage($response_message = false, $no_code = false)
    {
        if ($this->mintCode == self::DEVELOPER_HAS_FOLLOWING_OVERLAP_RATE_PLANS) {
            return $this->mintMessage;
        }
        return $response_message ? (!$no_code ? $this->mintCode . ': ' : '') . $this->mintMessage : self::$codes[$this->mintCode];
    }
}
