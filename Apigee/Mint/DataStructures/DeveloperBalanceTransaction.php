<?php
/**
 * DeveloperBalanceTransaction is used with the following API call:
 *
 * {{base_url}}/mint/organizations/{{org_name}}/developers/{{developer_email}}/developer-balances
 *
 * The result is a developerBalance object that contains a transaction object.
 */

namespace Apigee\Mint\DataStructures;


class DeveloperBalanceTransaction extends DataStructure {

  private $currency;
  private $custAtt1;
  private $endTime;
  private $environment;
  private $id;
  private $isVirtualCurrency;
  private $notes;
  private $providerTxId;
  private $rate;
  private $revenueShareAmount;
  private $startTime;
  private $status;
  private $txProviderStatus;
  private $type;
  private $utcEndTime;
  private $utcStartTime;


  /**
   * Class constructor
   *
   * @param array $data
   */
  public function __construct($data = NULL) {
    $this->currency = '';
    $this->custAtt1 = '';
    $this->endTime = '';
    $this->environment = '';
    $this->id = '';
    $this->isVirtualCurrency = '';
    $this->notes = '';
    $this->providerTxId = '';
    $this->rate = '';
    $this->revenueShareAmount = '';
    $this->startTime = '';
    $this->status = '';
    $this->txProviderStatus = '';
    $this->type = '';
    $this->utcEndTime = '';
    $this->utcStartTime = '';

    if (is_array($data)) {
      $this->loadFromRawData($data, array('developer'));
    }
  }

  public function setCurrency($currency) {
    $this->currency = $currency;
  }

  public function getCurrency() {
    return $this->currency;
  }

  public function setCustAtt1($custAtt1) {
    $this->custAtt1 = $custAtt1;
  }

  public function getCustAtt1() {
    return $this->custAtt1;
  }

  public function setEndTime($endTime) {
    $this->endTime = $endTime;
  }

  public function getEndTime() {
    return $this->endTime;
  }

  public function setEnvironment($environment) {
    $this->environment = $environment;
  }

  public function getEnvironment() {
    return $this->environment;
  }

  public function setId($id) {
    $this->id = $id;
  }


  public function getId() {
    return $this->id;
  }

  public function setIsVirtualCurrency($isVirtualCurrency) {
    $this->isVirtualCurrency = $isVirtualCurrency;
  }

  public function getIsVirtualCurrency() {
    return $this->isVirtualCurrency;
  }

  public function setNotes($notes) {
    $this->notes = $notes;
  }

  public function getNotes() {
    return $this->notes;
  }

  public function setProviderTxId($providerTxId) {
    $this->providerTxId = $providerTxId;
  }

  public function getProviderTxId() {
    return $this->providerTxId;
  }

  public function setRate($rate) {
    $this->rate = $rate;
  }

  public function getRate() {
    return $this->rate;
  }

  public function setRevenueShareAmount($revenueShareAmount) {
    $this->revenueShareAmount = $revenueShareAmount;
  }

  public function getRevenueShareAmount() {
    return $this->revenueShareAmount;
  }

  public function setStartTime($startTime) {
    $this->startTime = $startTime;
  }

  public function getStartTime() {
    return $this->startTime;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function setTxProviderStatus($txProviderStatus) {
    $this->txProviderStatus = $txProviderStatus;
  }


  public function getTxProviderStatus() {
    return $this->txProviderStatus;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function setUtcEndTime($utcEndTime) {
    $this->utcEndTime = $utcEndTime;
  }

  public function getUtcEndTime() {
    return $this->utcEndTime;
  }

  public function setUtcStartTime($utcStartTime) {
    $this->utcStartTime = $utcStartTime;
  }

  public function getUtcStartTime() {
    return $this->utcStartTime;
  }



}