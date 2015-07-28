<?php

namespace Apigee\Mint\DataStructures;

class MintCriteria extends DataStructure
{

    /**
     * @var bool
     */
    private $asXorg;

    /**
     * @var string
     */
    private $currencyOption;

    /**
     * @var string
     *   Date value formatted as YYYY-mm-dd
     */
    private $fromDate;

    /**
     * @var array
     */
    private $groupBy;

    /**
     * @var bool
     */
    private $showRevSharePct;

    /**
     * @var bool
     */
    private $showSummary;

    /**
     * @var bool
     */
    private $showTxDetail;

    /**
     * @var bool
     */
    private $showTxType;

    /**
     * @var string
     *   Date value formatted as YYYY-mm-dd
     */
    private $toDate;

    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->loadFromRawData($data);
        }
    }

    // accessors/setters
    public function asXorg()
    {
        return $this->asXorg;
    }

    public function setAsXorg($as_xorg)
    {
        $this->asXorg = $as_xorg;
    }

    public function getCurrencyOption()
    {
        return $this->currencyOption;
    }

    public function setCurrencyOption($currency_option)
    {
        $this->currencyOption = $currency_option;
    }

    public function getFromDate()
    {
        return $this->fromDate;
    }

    public function setFromDate($from_date)
    {
        $this->fromDate = $from_date;
    }

    public function getGroupBy()
    {
        return $this->groupBy;
    }

    public function setGroupBy($group_by)
    {
        $this->groupBy = $group_by;
    }

    public function showRevSharePct()
    {
        return $this->showRevSharePct;
    }

    public function setShowRevSharePct($show_rev_share_pct)
    {
        $this->showRevSharePct = $show_rev_share_pct;
    }

    public function showSummary()
    {
        return $this->showSummary;
    }

    public function setShowSummary($show_summary)
    {
        $this->showSummary = $show_summary;
    }

    public function showTxDetail()
    {
        return $this->showTxDetail;
    }

    public function setShowTxDetail($show_tx_detail)
    {
        $this->showTxDetail = $show_tx_detail;
    }

    public function showTxType()
    {
        return $this->showTxType;
    }

    public function setSowTxType($show_tx_type)
    {
        $this->showTxType = $show_tx_type;
    }

    public function getToDate()
    {
        return $this->toDate;
    }

    public function setToDate($to_date)
    {
        $this->toDate = $to_date;
    }
}
