<?php
/**
 * @author isaias
 * @since 6 November 2013
 */

namespace Apigee\Mint\DataStructures;

class Payment extends DataStructure
{

    /**
     * @var bool
     */
    private $isRecurring;

    /**
     * @var int
     */
    private $orderCode;

    /**
     * @var int
     */
    private $referenceId;

    /**
     * @var string
     */
    private $referenceUrl;

    /**
     * @var bool
     */
    private $success;

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->loadFromRawData($data);
        }
    }

    /**
     * @param boolean $isRecurring
     */
    public function setIsRecurring($isRecurring)
    {
        $this->isRecurring = $isRecurring;
    }

    /**
     * @return boolean
     */
    public function getIsRecurring()
    {
        return $this->isRecurring;
    }

    /**
     * @param int $orderCode
     */
    public function setOrderCode($orderCode)
    {
        $this->orderCode = $orderCode;
    }

    /**
     * @return int
     */
    public function getOrderCode()
    {
        return $this->orderCode;
    }

    /**
     * @param int $referenceId
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return int
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceUrl
     */
    public function setReferenceUrl($referenceUrl)
    {
        $this->referenceUrl = $referenceUrl;
    }

    /**
     * @return string
     */
    public function getReferenceUrl()
    {
        return $this->referenceUrl;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
    }
}
