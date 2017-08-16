<?php
namespace Apigee\Mint\DataStructures;

use Apigee\Util\OrgConfig;
use Apigee\Mint\Organization;
use Apigee\Mint\Product;

class RatePlanDetail extends DataStructure
{

    /**
     * RatePlanDetail id
     * @var string
     */
    private $id;

    /**
     * Rate plan detail type.
     * @var string Allowed values: [REVSHARE|REVSHARE_RATECARD|RATECARD|NON_CHARGEABLE]
     */
    private $type;

    /**
     * Revenue type.
     * @var string Allowed values: [NET|GROSS]
     */
    private $revenueType;

    /**
     * Metering Type.
     * @var string Allowed values: [UNIT|VOLUME|STAIR_STEP]
     */
    private $meteringType;

    /**
     * Rating Parameter for the rate card. Can be VOLUME or a CUSTOM_ATTRIBUTE_NAME or sum(CUSTOM_ATTRIBUTE_NAME)
     * @var string
     */
    private $ratingParameter;

    /**
     * what is the unit of rating paramter value. e.g. MB, minutes, words etc
     * @var string
     */
    private $ratingParameterUnit;

    /**
     * Duration
     * @var int
     */
    private $duration;

    /**
     * Duration Type.
     * @var string Allowed values: [DAY|WEEK|MONTH|QUARTER|YEAR]
     */
    private $durationType;

    /**
     * Freemium duration
     * @var int
     */
    private $freemiumDuration;

    /**
     * Freemium number of units
     * @var int
     */
    private $freemiumUnit;

    /**
     * Freemium Duration Type.
     * @var string Allowed values: [DAY|WEEK|MONTH|QUARTER|YEAR]
     */
    private $freemiumDurationType;

    /**
     * Rate card details
     * @var array Array must elements must be instances of Apigee\Mint\DataStructure\RatePlanRate
     */
    private $ratePlanRates = array();

    /**
     * Organization
     * @var \Apigee\Mint\Organization
     */
    private $organization;

    /**
     * Product
     * @var \Apigee\Mint\Product
     */
    private $product;

    /**
     * Rate Plan Detail currency
     * @var \Apigee\Mint\DataStructures\SupportedCurrency
     */
    private $currency;

    /**
     * Class constructor.
     * @param array|null $data
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct($data, OrgConfig $config)
    {
        if (is_array($data)) {

            if (isset($data['ratePlanRates'])) {
                foreach ($data['ratePlanRates'] as $ratePlanRate) {
                    $this->ratePlanRates[] = new RatePlanRate($ratePlanRate);
                }
            }

            if (isset($data['currency'])) {
                $this->currency = new SupportedCurrency($data['currency'], $config);
            }

            if (isset($data['organization'])) {
                $organization = new Organization($config);
                $organization->loadFromRawData($data['organization']);
                $this->organization = $organization;
            }

            if (isset($data['product'])) {
                $product = new Product($config);
                $product->loadFromRawData($data['product']);
                $this->product = $product;
            }

            $excluded_properties = array('ratePlanRates', 'organization', 'product', 'currency');
            foreach (array_keys(get_object_vars($this)) as $var) {
                if (isset($data[$var]) && !in_array($var, $excluded_properties)) {
                    $this->$var = $data[$var];
                }
            }
        }
    }

    /**
     * @return \Apigee\Mint\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param \Apigee\Mint\DataStructures\SupportedCurrency $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return \Apigee\Mint\DataStructures\SupportedCurrency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $durationType
     */
    public function setDurationType($durationType)
    {
        $this->durationType = $durationType;
    }

    /**
     * @return string
     */
    public function getDurationType()
    {
        return $this->durationType;
    }

    /**
     * @param int $freemiumDuration
     */
    public function setFreemiumDuration($freemiumDuration)
    {
        $this->freemiumDuration = $freemiumDuration;
    }

    /**
     * @return int
     */
    public function getFreemiumDuration()
    {
        return $this->freemiumDuration;
    }

    /**
     * @param string $freemiumDurationType
     */
    public function setFreemiumDurationType($freemiumDurationType)
    {
        $this->freemiumDurationType = $freemiumDurationType;
    }

    /**
     * @return string
     */
    public function getFreemiumDurationType()
    {
        return $this->freemiumDurationType;
    }

    /**
     * @param int $freemiumUnit
     */
    public function setFreemiumUnit($freemiumUnit)
    {
        $this->freemiumUnit = $freemiumUnit;
    }

    /**
     * @return int
     */
    public function getFreemiumUnit()
    {
        return $this->freemiumUnit;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $meteringType
     */
    public function setMeteringType($meteringType)
    {
        $this->meteringType = $meteringType;
    }

    /**
     * @return string
     */
    public function getMeteringType()
    {
        return $this->meteringType;
    }

    /**
     * @param \Apigee\Mint\Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return \Apigee\Mint\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param array $ratePlanRates
     */
    public function setRatePlanRates($ratePlanRates)
    {
        $this->ratePlanRates = $ratePlanRates;
    }

    /**
     * @return array
     */
    public function getRatePlanRates()
    {
        return $this->ratePlanRates;
    }

    /**
     * @param string $ratingParameter
     */
    public function setRatingParameter($ratingParameter)
    {
        $this->ratingParameter = $ratingParameter;
    }

    /**
     * @return string
     */
    public function getRatingParameter()
    {
        return $this->ratingParameter;
    }

    /**
     * @param string $ratingParameterUnit
     */
    public function setRatingParameterUnit($ratingParameterUnit)
    {
        $this->ratingParameterUnit = $ratingParameterUnit;
    }

    /**
     * @return string
     */
    public function getRatingParameterUnit()
    {
        return $this->ratingParameterUnit;
    }

    /**
     * @param string $revenueType
     */
    public function setRevenueType($revenueType)
    {
        $this->revenueType = $revenueType;
    }

    /**
     * @return string
     */
    public function getRevenueType()
    {
        return $this->revenueType;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
