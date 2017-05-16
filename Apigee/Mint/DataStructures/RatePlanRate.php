<?php

namespace Apigee\Mint\DataStructures;

class RatePlanRate extends DataStructure
{

    /**
     * RatePlanRate id
     * @var string
     */
    private $id;

    /**
     * Rate plan rate type.
     * Possible values:
     * @var string Allowed values: [REVSHARE|RATECARD]
     */
    private $type;

    /**
     * Price
     * @var double
     */
    private $rate;

    /**
     * Revshare
     * @var string
     */
    private $revshare;

    /**
     * Unit range start
     * @var int
     */
    private $startUnit;

    /**
     * Unit range end
     * @var int
     */
    private $endUnit;

    /**
     * Class constructor.
     * @param array $data
     */
    public function __construct($data = null)
    {
        if (is_array($data)) {
            foreach (array_keys(get_object_vars($this)) as $var) {
                if (isset($data[$var])) {
                    $this->$var = $data[$var];
                }
            }
        }
    }

    /**
     * @param int $endUnit
     */
    public function setEndUnit($endUnit)
    {
        $this->endUnit = $endUnit;
    }

    /**
     * @return int
     */
    public function getEndUnit()
    {
        return $this->endUnit;
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
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param string $revshare
     */
    public function setRevshare($revshare)
    {
        $this->revshare = $revshare;
    }

    /**
     * @return string
     */
    public function getRevshare()
    {
        return $this->revshare;
    }

    /**
     * @param int $startUnit
     */
    public function setStartUnit($startUnit)
    {
        $this->startUnit = $startUnit;
    }

    /**
     * @return int
     */
    public function getStartUnit()
    {
        return $this->startUnit;
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
