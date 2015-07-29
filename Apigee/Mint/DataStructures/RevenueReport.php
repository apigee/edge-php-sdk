<?php

namespace Apigee\Mint\DataStructures;

use Apigee\Mint\Developer;
use Apigee\Mint\Organization;

class RevenueReport extends DataStructure
{

    private $mintCriteria;

    private $description;

    private $developer;

    private $id;

    private $name;

    private $organization;

    private $type;

    public function __construct($data, Developer $developer)
    {
        $excluded_properties = array('mintCriteria', 'developer', 'organization');
        if (is_array($data)) {
            $this->loadFromRawData($data, $excluded_properties);
        } elseif (!isset($data)) {
            $data = array();
        }

        if (isset($data['mintCriteria'])) {
            $this->mintCriteria = new MintCriteria($data['mintCriteria']);
        }

        $this->developer = $developer;

        if (isset($data['organization'])) {
            $organization = new Organization($this->developer->getConfig());
            $organization->loadFromRawData($data['organization']);
            $this->organization = $organization;
        }
    }

    public function getMintCriteria()
    {
        return $this->mintCriteria;
    }

    public function setMintCriteria($mint_criteria)
    {
        $this->mintCriteria = $mint_criteria;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDeveloper()
    {
        return $this->developer;
    }

    public function setDeveloper($developer)
    {
        $this->developer = $developer;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
