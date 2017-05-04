<?php

namespace Apigee\Mint\DataStructures;

use Apigee\Mint\Types\Country;
use Apigee\Util\APIObject;

class Address extends DataStructure
{
    private $address1 = null;
    private $address2 = null;
    private $city = null;
    private $country = null;
    private $id = null;
    private $isPrimary = null;
    private $state = null;
    private $zip = null;

    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->loadFromRawData($data);
        }
    }

    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    public function getAddress1()
    {
        return $this->address1;
    }

    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    public function getAddress2()
    {
        return $this->address2;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCountry($country_code)
    {
        // Only set country if it is valid.
        if (Country::validateCountryCode($country_code)) {
            $this->country = $country_code;
        } elseif ($country_code == 'UK') {
            // Change incorrect United Kingdom 'UK' country code to 'GB'.
            $this->country = 'GB';
        } else {
            APIObject::$logger->error('Invalid country code "' . $country_code . '" passed from Edge MGMT API.');
        }
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setIsPrimary($is_primary)
    {
        $this->isPrimary = $is_primary;
    }

    public function isPrimary()
    {
        return $this->isPrimary;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    public function getZip()
    {
        return $this->zip;
    }
}
