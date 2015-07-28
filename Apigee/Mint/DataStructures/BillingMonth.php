<?php

namespace Apigee\Mint\DataStructures;

class BillingMonth extends DataStructure
{
    private $month;

    private $monthEnum;

    private $status;

    private $year;

    public function __construct($data)
    {
        $this->loadFromRawData($data);
    }

    public function setMonth($month)
    {
        $this->month = $month;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function setMonthEnum($month_enum)
    {
        $this->monthEnum = $month_enum;
    }

    public function getMonthEnum()
    {
        return $this->monthEnum;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function getYear()
    {
        return $this->year;
    }
}
