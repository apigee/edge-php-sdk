<?php
namespace Apigee\Mint\DataStructures;

class DeveloperBalance {

  /**
   *
   * @var SupportedCurrency
   */
  public $supportedCurrency;

  /**
   * @var double
   */
  public $approxTaxRate;

  /**
   * This is balance (prepaid) or credit limit (postpaid)
   * @var double
   */
  public $currentBalance;

  /**
   * @var string
   */
  public $id;

  /**
   * @var string
   */
  public $month;

  /**
   * @var double
   */
  public $previousBalance;

  /**
   * @var double
   */
  public $tax;

  /**
   * @var double
   */
  public $topups;

  /**
   * @var double
   */
  public $usage;

  /**
   * @var int
   */
  public $year;

  /**
   * Constructor
   * @param array $data
   */
  public function __construct($data = NULL) {
    if (is_array($data)) {
      foreach (array_keys(get_object_vars($this)) as $var) {
        if (isset($data[$var])) {
          if ($var == 'supportedCurrency') {
            $this->supportedCurrency = new SupportedCurrency($data[$var]);
          }
          else {
            $this->$var = $data[$var];
          }
        }
      }
    }
  }

  public function getSupportedCurrency() {
    return $this->supportedCurrency;
  }
  public function setSupportedCurrency($supported_currency) {
    $this->supportedCurrency = $supported_currency;
  }

  public function getApproxTaxRate() {
    return $this->approxTaxRate;
  }
  public function setApproxTaxRate($approx_tax_rate) {
    $this->approxTaxRate = $approx_tax_rate;
  }

  public function getCurrentBalance() {
    return $this->currentBalance;
  }
  public function setCurrentBalance($current_balance) {
    $this->currentBalance = $current_balance;
  }

  public function getId() {
    return $this->id;
  }
  private function setId($id) {
    $this->id = $id;
  }

  public function getMonth() {
    return $this->month;
  }
  public function setMonth($month) {
    $this->month = $month;
  }
}