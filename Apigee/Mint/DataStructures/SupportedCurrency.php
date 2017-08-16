<?php

namespace Apigee\Mint\DataStructures;

use Apigee\Mint\Organization;
use Apigee\Util\OrgConfig;

class SupportedCurrency extends DataStructure
{

    /**
     * Organization
     * @var \Apigee\Mint\Organization
     */
    private $organization;

    /**
     * Currency id
     * @var string
     */
    private $id;

    /**
     * Supported Currency Name
     * @var string
     */
    private $name;

    /**
     * Display Name
     * @var string
     */
    private $displayName;

    /**
     * Description
     * @var string
     */
    private $description;

    /**
     * Virtual currency
     * @var boolean
     */
    private $virtualCurrency;

    /**
     * Status.
     * @var string Allowed values [CREATED|INACTIVE|ACTIVE]
     */
    private $status;

    /**
     * Credit Limit for Postpaid developers. This can be overridden for each developer in developer balance.
     * @var double
     */
    private $creditLimit;

    /**
     * @var float Minimum amount a developer can set a recurring amount
     */
    private $minimumRecurringAmount = 0;

    /**
     * @param float $creditLimit
     */
    public function setCreditLimit($creditLimit)
    {
        $this->creditLimit = $creditLimit;
    }

    /**
     * @return float
     */
    public function getCreditLimit()
    {
        return $this->creditLimit;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
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
     * @param float $minimumRecurringAmount
     */
    public function setMinimumRecurringAmount($minimumRecurringAmount)
    {
        $this->minimumRecurringAmount = $minimumRecurringAmount;
    }

    /**
     * @return float
     */
    public function getMinimumRecurringAmount()
    {
        return $this->minimumRecurringAmount;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Apigee\Mint\Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return \Apigee\Mint\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $var
     */
    public function setVar($var)
    {
        $this->var = $var;
    }

    /**
     * @return mixed
     */
    public function getVar()
    {
        return $this->var;
    }

    /**
     * @param boolean $virtualCurrency
     */
    public function setVirtualCurrency($virtualCurrency)
    {
        $this->virtualCurrency = $virtualCurrency;
    }

    /**
     * @return boolean
     */
    public function isVirtualCurrency()
    {
        return $this->virtualCurrency;
    }


    /**
     * Constructor
     * @param array $data
     * @param OrgConfig $config
     *   Required to load data which only contains organization as an array.
     */
    public function __construct($data = null, OrgConfig $config = NULL)
    {
        if (is_array($data)) {
            foreach (array_keys(get_object_vars($this)) as $var) {
                if (isset($data[$var])) {
                    if ($var == 'organization' && !is_object($data[$var])) {
                        if ($config == NULL) {
                            throw new \Exception('OrgConfig can not be null.');
                        }
                        $org = new Organization($config);
                        $org->loadFromRawData($data[$var]);
                        $this->{$var} = $org;
                    }
                    else {
                        $this->{$var} = $data[$var];
                    }
                }
            }
        }
    }

    public function __toString()
    {
        $json = parent::__toString();
        $obj = json_decode($json, true);
        $obj['organization'] = array('id' => $this->organization->getId());
        return json_encode($obj);
    }
}
