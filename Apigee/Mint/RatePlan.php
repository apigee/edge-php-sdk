<?php

namespace Apigee\Mint;

use \DateTime;
use \DateTimeZone;

use Apigee\Util\CacheFactory;

use Apigee\Exceptions\ParameterException;

class RatePlan extends Base\BaseObject
{

    /**
     * Advance
     * @var boolean
     */
    private $advance;

    /**
     * com.apigee.mint.model.Organization
     * @var \Apigee\Mint\Organization
     */
    private $organization;

    /**
     * MonetizationPackage
     * @var \Apigee\Mint\MonetizationPackage
     */
    private $monetizationPackage;

    /**
     * Rate Plan currency
     * @var \Apigee\Mint\DataStructures\SupportedCurrency
     */
    private $currency;

    /**
     * Reference rate plan id RatePlan
     * @var \Apigee\Mint\RatePlan
     */
    private $parentRatePlan;

    private $paymentDueDays;

    /**
     * References a rate plan that this plan belongs to if any. This is for
     * devconnect internal logic.
     * @var \Apigee\Mint\RatePlan
     */
    private $childRatePlan;

    /**
     * com.apigee.mint.model.Developer
     * @var \Apigee\Mint\Developer
     */
    private $developerId;

    /**
     * com.apigee.mint.model.DeveloperCategory
     * @var \Apigee\Mint\DeveloperCategory
     */
    private $developerCategory;

    /**
     * Array of DeveloperRatePlan
     * @var \Apigee\Mint\DeveloperRatePlan
     */
    private $developers = array();

    /**
     * com.apigee.mint.model.ApplicationCategory
     * @var \Apigee\Mint\ApplicationCategory
     */
    private $applicationCategory;

    /**
     * Exchange Organization
     * @var \Apigee\Mint\Organization
     */
    private $exchangeOrganization; //@TODO Verify if required

    /**
     * Rate plan type.
     * @var string
     */
    private $type;

    /**
     * Name
     * @var string
     */
    private $name;

    /**
     * Display Name
     * @var string
     */
    private $displayName;

    /**
     *  Description
     * @var string
     */
    private $description;

    /**
     * One time set up fee
     * @var double
     */
    private $setUpFee;

    /**
     * Recurring time set up fee
     * @var double
     */
    private $recurringFee;

    /**
     * Duration
     * @var int
     */
    private $frequencyDuration;

    /**
     * Duration Type.
     * @var string
     */
    private $frequencyDurationType;

    /**
     * Recurring Type Used to define if scheduler needs to be run based on Calendar or Custom (plan start date)
     * Possible values:
     * @var string
     */
    private $recurringType;

    /**
     * Recurring start unit this is only used if the recurringType is CALENDAR
     * @var int
     */
    private $recurringStartUnit;

    /**
     * Should this package be prorated?
     * @var boolean
     */
    private $prorate;

    /**
     * Early termination fee
     * @var double
     */
    private $earlyTerminationFee;

    /**
     * Effective Start date
     * @var string
     */
    private $startDate;

    /**
     * Effective End date
     * @var string
     */
    private $endDate;

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
     * Freemium Duration Type
     * @var string
     */
    private $freemiumDurationType;

    /**
     * Is published?
     * @var boolean
     */
    private $published;

    /**
     * Contract duration
     * @var int
     */
    private $contractDuration;

    /**
     * Contract Duration Type .
     * @var string
     */
    private $contractDurationType;

    /**
     * Keep developer original start date (used for rate plan revisions)
     * @var boolean
     */
    private $keepOriginalStartDate;

    /**
     * Rate plan details
     * @var array Array must elements must be instances of Apigee\Mint\DataStructures\RatePlanRate
     */
    private $ratePlanDetails = array();

    /**
     * Monetization Package id
     * @var string
     */
    private $mPackageId;

    /**
     * @var string
     */
    public $id;

    /**
     * Class constructor
     * @param string $m_package_id Monetization Package id
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct($m_package_id, \Apigee\Util\OrgConfig $config)
    {
        $base_url = '/mint/organizations/' . rawurlencode($config->orgName) . '/monetization-packages/' . rawurlencode($m_package_id) . '/rate-plans';
        $this->init($config, $base_url);
        $this->mPackageId = $m_package_id;

        $this->wrapperTag = 'ratePlan';
        $this->idField = 'id';

        $this->initValues();
    }

    // Override of BaseObject methods

    public function getList($page_num = null, $page_size = 20, $current = true, $all_available = true)
    {
        if (!isset($this->developerId)) {
            return parent::getList();
        }

        $options = array(
            'query' => array(
                'current' => $current ? 'true' : 'false',
                'allAvailable' => $all_available ? 'true' : 'false',
            ),
        );
        $url = '/mint/organizations/' . rawurlencode($this->config->orgName) . '/monetization-packages/' . rawurlencode($this->mPackageId) . '/developers/' . rawurlencode($this->developerId) . '/rate-plans';
        $this->setBaseUrl($url);
        $this->get(null, 'application/json; charset=utf-8', array(), $options);
        $this->restoreBaseUrl();
        $response = $this->responseObj;

        $return_objects = array();

        foreach ($response[$this->wrapperTag] as $response_data) {
            $obj = $this->instantiateNew();
            $obj->loadFromRawData($response_data);
            $return_objects[] = $obj;
        }
        return $return_objects;
    }

    // Implementation of BaseObject abstract methods

    public function instantiateNew()
    {
        return new RatePlan($this->mPackageId, $this->config);
    }

    public function loadFromRawData($data, $reset = false)
    {

        if ($reset) {
            $this->initValues();
        }

        $excluded_properties = array(
            'org',
            'mPackageId',
            'organization',
            'monetizationPackage',
            'currency',
            'parentRatePlan',
            'developer',
            'developerCategory',
            'developers',
            'applicationCategory',
            'exchangeOrganization',
            'ratePlanDetails'
        );

        foreach (array_keys($data) as $property) {
            if (in_array($property, $excluded_properties)) {
                continue;
            }

            // form the setter method name to invoke setXxxx
            $setter_method = 'set' . ucfirst($property);
            if (method_exists($this, $setter_method)) {
                $this->$setter_method($data[$property]);
            }
            else {
                self::$logger->notice('No setter method was found for property "' . $property . '"');
            }
        }

        // Set objects

        if (isset($data['organization'])) {
            $organization = new Organization($this->config);
            $organization->loadFromRawData($data['organization']);
            $this->organization = $organization;
        }

        if (isset($data['monetizationPackage'])) {
            $monetizationPackage = new MonetizationPackage($this->config);
            $monetizationPackage->loadFromRawData($data['monetizationPackage']);
            $this->monetizationPackage = $monetizationPackage;
        }

        if (isset($data['currency'])) {
            $this->currency = new DataStructures\SupportedCurrency($data['currency']);
        }

        if (isset($data['parentRatePlan'])) {
            $rate_plan = new RatePlan($this->mPackageId, $this->config);
            $rate_plan->loadFromRawData($data['parentRatePlan']);
            $this->setParentRatePlan($rate_plan);
        }

        if (isset($data['developer'])) {
            $dev = new Developer($this->config);
            $dev->loadFromRawData($data['developer']);
            $this->developerId = $dev->getEmail();
        }

        //@TODO Implement load of developerCategory

        //@TODO Implement load of developers

        //@TODO Implement load of applicationCategory

        if (isset($data['exchangeOrganization'])) {
            $organization = new Organization($this->config);
            $organization->loadFromRawData($data['exchangeOrganization']);
            $this->exchangeOrganization = $organization;
        }

        if (isset($data['ratePlanDetails'])) {
            foreach ($data['ratePlanDetails'] as $ratePlanDetail) {
                $this->ratePlanDetails[] = new DataStructures\RatePlanDetail($ratePlanDetail, $this->config);
            }
        }
    }

    protected function initValues()
    {
        $this->advance = false;
        $this->organization = null;
        $this->monetizationPackage = null;
        $this->currency = null;
        $this->childRatePlan = null;
        $this->parentRatePlan = null;
        $this->developerId = null;
      $this->companyId = null;
      $this->isCompanyPlan = FALSE;
        $this->developerCategory = null;
        $this->developers = array();
        $this->applicationCategory = null;
        $this->exchangeOrganization = null;
        $this->type = '';
        $this->name = '';
        $this->displayName = '';
        $this->description = '';
        $this->setUpFee = 0;
        $this->recurringFee = 0;
        $this->frequencyDuration = 0;
        $this->frequencyDurationType = '';
        $this->recurringType = '';
        $this->recurringStartUnit = 0;
        $this->prorate = false;
        $this->earlyTerminationFee = 0;
        $this->startDate = '';
        $this->endDate = '';
        $this->freemiumDuration = 0;
        $this->freemiumUnit = 0;
        $this->freemiumDurationType = '';
        $this->published = false;
        $this->contractDuration = 0;
        $this->contractDurationType = '';
        $this->keepOriginalStartDate = false;
        $this->ratePlanDetails = array();
    }

    public function __toString()
    {
        // @TODO Make right implementation
        return json_encode($this);
    }

    // getters

    /**
     * Is Advance?
     * @return boolean
     */
    public function isAdvance()
    {
        return $this->advance;
    }

    /**
     * Get com.apigee.mint.model.Organization
     * @return \Apigee\Mint\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Get MonetizationPackage
     * @return \Apigee\Mint\MonetizationPackage
     */
    public function getMonetizationPackage()
    {
        return $this->monetizationPackage;
    }

    /**
     * Get Rate Plan currency
     * @return \Apigee\Mint\DataStructures\SupportedCurrency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get Parent Rate Plan
     * @return \Apigee\Mint\RatePlan
     */
    public function getParentRatePlan()
    {
        return $this->parentRatePlan;
    }

    public function getPaymentDueDays()
    {
        return $this->paymentDueDays;
    }

    public function getChildRatePlan()
    {
        return $this->childRatePlan;
    }

    /**
     * Get Company or Developer ID.
     * @return integer The company or developer ID.
     */
    public function getDeveloperId()
    {
        return $this->developerId;
    }

    /**
     * Get com.apigee.mint.model.DeveloperCategory
     * @return \Apigee\Mint\DeveloperCategory
     */
    public function getDeveloperCategory()
    {
        return $this->developerCategory;
    }

    /**
     * Get an array of DeveloperRatePlan
     * @return \Apigee\Mint\DeveloperRatePlan
     */
    public function getDeveloperRatePlans()
    {
        return $this->developers;
    }

    /**
     * Get com.apigee.mint.model.ApplicationCategory
     * @return \Apigee\Mint\ApplicationCategory
     */
    public function getApplicationCategory()
    {
        return $this->applicationCategory;
    }

    /**
     * Get Exchange Organization
     * @return \Apigee\Mint\Organization
     */
    public function getExchangeOrganization()
    {
        return $this->exchangeOrganization;
    }

    /**
     * Get Rate plan type.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Display Name
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Get Description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get One time set up fee
     * @return double
     */
    public function getSetUpFee()
    {
        return $this->setUpFee;
    }

    /**
     * Get Recurring time set up fee
     * @return double
     */
    public function getRecurringFee()
    {
        return $this->recurringFee;
    }

    /**
     * Get Frecuency Duration
     * @return int
     */
    public function getFrequencyDuration()
    {
        return $this->frequencyDuration;
    }

    /**
     * Get Frecuency Duration Type.
     * @return string
     */
    public function getFrequencyDurationType()
    {
        return $this->frequencyDurationType;
    }

    /**
     * Get Recurring Type Used to define if scheduler needs to be run based on Calendar or Custom (plan start date)
     * @return string
     */
    public function getRecurringType()
    {
        return $this->recurringType;
    }

    /**
     * Get Recurring start unit this is only used if the recurringType is CALENDAR
     * @return int
     */
    public function getRecurringStartUnit()
    {
        return $this->recurringStartUnit;
    }

    /**
     * Is this package to be prorated
     * @return boolean
     */
    public function isProrate()
    {
        return $this->prorate;
    }

    /**
     * Get Early termination fee
     * @return double
     */
    public function getEarlyTerminationFee()
    {
        return $this->earlyTerminationFee;
    }

    /**
     * Get start date as a string in GMT
     * @deprecated Use getStartDateTime() instead
     * @return string The start date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Get start date as a DateTime object in org's timezone.
     * @return \DateTime The start date
     */
    public function getStartDateTime()
    {
        return $this->convertToDateTime($this->startDate);
    }

    /**
     * Get end date as a string in GMT
     * @deprecated Use getEndDateTime() instead
     * @return string The end date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Get end date as a DateTime object in org's timezone.
     * @return \DateTime The end date or null if not set
     */
    public function getEndDateTime()
    {
        return $this->convertToDateTime($this->endDate);
    }

    /**
     * Get Freemium duration
     * @return int
     */
    public function getFreemiumDuration()
    {
        return $this->freemiumDuration;
    }

    /**
     * Get Freemium number of units
     * @return int
     */
    public function getFreemiumUnit()
    {
        return $this->freemiumUnit;
    }

    /**
     * Get Freemium Duration Type
     * @return string
     */
    public function getFreemiumDurationType()
    {
        return $this->freemiumDurationType;
    }

    /**
     * Is published?
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Get Contract duration
     * @return int
     */
    public function getContractDuration()
    {
        return $this->contractDuration;
    }

    /**
     * Get Contract Duration Type .
     * @return string
     */
    public function getContractDurationType()
    {
        return $this->contractDurationType;
    }

    /**
     * Keep developer original start date (used for rate plan revisions)
     * @return boolean
     */
    public function getKeepOriginalStartDate()
    {
        return $this->keepOriginalStartDate;
    }

    /**
     * Get Rate plan details
     * @return array Array must elements must be instances of Apigee\Mint\DataStructures\RatePlanRate
     */
    public function getRatePlanDetails()
    {
        return $this->ratePlanDetails;
    }

    public function getRatePlanDetailsByProduct(Product $product = null)
    {
        if ($product == null) {
            return $this->ratePlanDetails;
        } else {
            $rate_plan_details = array();
            foreach ($this->ratePlanDetails as &$rate_plan_detail) {
                if (isset($rate_plan_detail->product) && $rate_plan_detail->product->getId() == $product->getId()) {
                    $rate_plan_details[] = $rate_plan_detail;
                }
            }
            return $rate_plan_details;
        }
    }
    //setters

    /**
     * Set Advance
     * @param boolean $advance
     */
    public function  setAdvance($advance)
    {
        $this->advance = $advance;
    }

    /**
     * Set com.apigee.mint.model.Organization
     * @param \Apigee\Mint\Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Set MonetizationPackage
     * @param \Apigee\Mint\MonetizationPackage $monetization_package
     */
    public function setMonetizationPackage(MonetizationPackage $monetization_package)
    {
        $this->monetizationPackage = $monetization_package;
    }

    /**
     * Set Rate Plan currency
     * @param \Apigee\Mint\DataStructures\SupportedCurrency $currency
     */
    public function setCurrency(DataStructures\SupportedCurrency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Set Parent Rate Plan
     * @param \Apigee\Mint\RatePlan $parent_rate_plan
     */
    public function setParentRatePlan(RatePlan $parent_rate_plan)
    {
        $parent_rate_plan->setChildRatePlan($this);
        $this->parentRatePlan = $parent_rate_plan;
    }

    public function setPaymentDueDays($payment_due_days)
    {
        $this->paymentDueDays = $payment_due_days;
    }

    /**
     * Only for internal logic
     * @param RatePlan $rate_plan
     */
    public function setChildRatePlan(RatePlan $rate_plan)
    {
        $this->childRatePlan = $rate_plan;
    }

  /**
   * Set com.apigee.mint.model.Developer
   * @param \Apigee\Mint\Developer $developer
   */
  public function setCompanyId($company_id)
  {
    $this->companyId = $company_id;
    $this->isCompanyPlan = TRUE;
  }

    /**
     * Set com.apigee.mint.model.Developer
     * @param \Apigee\Mint\Developer $developerId
     */
    public function setDeveloperId($developerId)
    {
        $this->developerId = $developerId;
    }

    /**
     * Set com.apigee.mint.model.DeveloperCategory
     * @param \Apigee\Mint\DeveloperCategory $developer_category
     */
    public function setDeveloperCategory(DeveloperCategory $developer_category)
    {
        $this->developerCategory = $developer_category;
    }

    /**
     * Add a of DeveloperRatePlan
     * @param \Apigee\Mint\DeveloperRatePlan $developer_rate_plan
     */
    public function addDeveloperRatePlan(DeveloperRatePlan $developer_rate_plan)
    {
        $this->developers[] = $developer_rate_plan;
    }

    /**
     * Remove all DeveloperRatePlans from this RatePlan
     */
    public function clearDeveloperRatePlan()
    {
        $this->developers = array();
    }

    /**
     * Set com.apigee.mint.model.ApplicationCategory
     * @param \Apigee\Mint\ApplicationCategory $application_category
     */
    public function setApplicationCategory($application_category)
    {
        $this->applicationCategory = $application_category;
    }

    /**
     * Set Exchange Organization
     * @param \Apigee\Mint\Organization $exchange_organization
     */
    public function setExchangeOrganization($exchange_organization)
    {
        $this->exchangeOrganization = $exchange_organization;
    }

    /**
     * Set Rate plan type.
     * @param string $type Possible values: STANDARD|DEVELOPER_CATEGORY|DEVELOPER|APPLICATION_CATEGORY|EXCHANGE_ORGANIZATION
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function setType($type)
    {
        $type = strtoupper($type);
        if (!in_array($type, array(
            'STANDARD',
            'DEVELOPER_CATEGORY',
            'DEVELOPER',
            'APPLICATION_CATEGORY',
            'EXCHANGE_ORGANIZATION'
        ))
        ) {
            throw new ParameterException('Invalid type of RatePlan: ' . $type);
        }
        $this->type = $type;
    }

    /**
     * Set Name
     * @param string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set Display Name
     * @param string $display_name
     */
    public function setDisplayName($display_name)
    {
        $this->displayName = $display_name;
    }

    /**
     * Set Description
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Set One time set up fee
     * @param double $set_up_fee
     */
    public function setSetUpFee($set_up_fee)
    {
        $this->setUpFee = $set_up_fee;
    }

    /**
     * Set Recurring time set up fee
     * @param double $recurring_fee
     */
    public function setRecurringFee($recurring_fee)
    {
        $this->recurringFee = $recurring_fee;
    }

    /**
     * Set Frequency Duration
     * @param int $frequency_duration
     */
    public function setFrequencyDuration($frequency_duration)
    {
        $this->frequencyDuration = $frequency_duration;
    }

    /**
     * Set Frequency Duration Type.
     * @param string $frequency_duration_type Possible values: DAY|WEEK|MONTH|QUARTER|YEAR
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function setFrequencyDurationType($frequency_duration_type)
    {
        $frequency_duration_type = strtoupper($frequency_duration_type);
        if (!in_array($frequency_duration_type, array('DAY', 'WEEK', 'MONTH', 'QUARTER', 'YEAR'))) {
            throw new ParameterException('Invalid frequency duration type: ' . $frequency_duration_type);
        }
        $this->frequencyDurationType = $frequency_duration_type;
    }

    /**
     * Set Recurring Type Used to define if scheduler needs to be run based on Calendar or Custom (plan start date)
     * @param string $recurring_type . Possible values: CALENDAR|CUSTOM
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function setRecurringType($recurring_type)
    {
        $recurring_type = strtoupper($recurring_type);
        if (!in_array($recurring_type, array('CALENDAR', 'CUSTOM'))) {
            throw new ParameterException('Invalid recurring type: ' . $recurring_type);
        }
        $this->recurringType = $recurring_type;
    }

    /**
     * Set Recurring start unit this is only used if the recurringType is CALENDAR
     * @param int $recurring_start_unit
     */
    public function setRecurringStartUnit($recurring_start_unit)
    {
        $this->recurringStartUnit = $recurring_start_unit;
    }

    /**
     * Set if this package is to be prorated
     * @param boolean $prorate
     */
    public function setProrate($prorate)
    {
        $this->prorate = $prorate;
    }

    /**
     * Set Early termination fee
     * @param double $early_termination_fee
     */
    public function setEarlyTerminationFee($early_termination_fee)
    {
        $this->earlyTerminationFee = $early_termination_fee;
    }

    /**
     * Set Effective Start date
     * @param string $start_date
     */
    public function setStartDate($start_date)
    {
        $this->startDate = $start_date;
    }

    /**
     * Set Effective End date
     * @param string $end_date
     */
    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    /**
     * Set Freemium duration
     * @param int $freemium_duration
     */
    public function setFreemiumDuration($freemium_duration)
    {
        $this->freemiumDuration = $freemium_duration;
    }

    /**
     * Set Freemium number of units
     * @param int $freemium_unit
     */
    public function setFreemiumUnit($freemium_unit)
    {
        $this->freemiumUnit = $freemium_unit;
    }

    /**
     * Set Freemium Duration Type
     * @param string $freemium_duration_type
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function setFreemiumDurationType($freemium_duration_type)
    {
        $freemium_duration_type = strtoupper($freemium_duration_type);
        if (!in_array($freemium_duration_type, array('DAY', 'WEEK', 'MONTH', 'QUARTER', 'YEAR'))) {
            throw new ParameterException('Invalid freemium duration type: ' . $freemium_duration_type);
        }
        $this->freemiumDurationType = $freemium_duration_type;
    }

    /**
     * Set published
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * Set Contract duration
     * @param int $contract_duration
     */
    public function setContractDuration($contract_duration)
    {
        $this->contractDuration = $contract_duration;
    }

    /**
     * Set Contract Duration Type .
     * @param string $contract_duration_type
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function setContractDurationType($contract_duration_type)
    {
        $contract_duration_type = strtoupper($contract_duration_type);
        if (!in_array($contract_duration_type, array('DAY', 'WEEK', 'MONTH', 'QUARTER', 'YEAR'))) {
            throw new ParameterException('Invalid contract duration type: ' . $contract_duration_type);
        }
        $this->contractDurationType = $contract_duration_type;
    }

    /**
     * Keep developer original start date (used for rate plan revisions)
     * @param boolean $keep_original_start_date
     */
    public function setKeepOriginalStartDate($keep_original_start_date)
    {
        $this->keepOriginalStartDate = (bool)$keep_original_start_date;
    }

    /**
     * Add Rate plan details
     * @param \Apigee\Mint\DataStructures\RatePlanRate $rate_plan_detail
     */
    public function addRatePlanDetails(DataStructures\RatePlanRate $rate_plan_detail)
    {
        $this->ratePlanDetails[] = $rate_plan_detail;
    }

    /**
     * Remove all RatePlanDetail from this RatePlan
     */
    public function clearRatePlanDetails()
    {
        $this->ratePlanDetails = array();
    }

    public function getId()
    {
        return $this->id;
    }

    // Used in data load invoked by $this->loadFromRawData()
    private function setId($id)
    {
        $this->id = $id;
    }

    public function isGroupPlan()
    {
        $is_group_plan = true;
        foreach ($this->ratePlanDetails as $ratePlanDetails) {
            if ($ratePlanDetails->getOrganization()->getParent() == null) {
                $is_group_plan = false;
                break;
            } else if ($ratePlanDetails->getOrganization()->getParent()->getId() != $this->organization->getId()) {
                $is_group_plan = false;
                break;
            }
        }
        return $is_group_plan;
    }

    public function load($id = null)
    {
        if (!isset($id)) {
            $id = $this->{$this->idField};
        }
        if (!isset($id)) {
            throw new ParameterException('No object identifier was specified.');
        }
        $cache_manager = CacheFactory::getCacheManager(null);
        $data = $cache_manager->get('rate_plan:' . $id, null);
        if (!isset($data)) {
            $url = rawurlencode($id);
            $this->get($url);
            $data = $this->responseObj;
            $cache_manager->set('rate_plan:' . $id, $data);
        }
        $this->initValues();
        $this->loadFromRawData($data);
    }


    public function hasEnded()
    {
        $plan_end_date = $this->getEndDateTime();

        // If plan end date is not set, return FALSE.
        if(is_null($plan_end_date)) {
            return FALSE;
        }

        $org_timezone = new DateTimeZone($this->getOrganization()->getTimezone());
        $today = new DateTime('today', $org_timezone);

        if($plan_end_date < $today) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Convert date string to DateTime object in proper timezone.
     *
     * To get the proper date, the date needs to be converted from
     * UTC time to the org's timezone.
     *
     * @param $date_string The date in the Edge API format of 'Y-m-d H:i:s'
     * @return \DateTime The date as a DateTime object or NULL if not set.
     */
    private function convertToDateTime($date_string)
    {
        if(empty($date_string)) {
            return NULL;
        }
        $org_timezone = new DateTimeZone($this->getOrganization()->getTimezone());
        $utc_timezone = new DateTimeZone('UTC');

        // Get UTC datetime of date string.
        $date_utc = DateTime::createFromFormat('Y-m-d H:i:s', $date_string, $utc_timezone);

        if($date_utc == FALSE) {
            return NULL;
        }

        // Convert to org's timezone.
        return  $date_utc->setTimezone($org_timezone);
    }
}

