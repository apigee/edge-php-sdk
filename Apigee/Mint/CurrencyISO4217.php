<?php
/**
 * Author: Isaias Arellano
 * User: isaias@apigee.com
 * Date: 11/7/13
 * Time: 4:53 PM
 */

/**
 * Consumes ISO 4217 for currencies properties
 */
namespace Apigee\Mint;

use Apigee\Util\CacheFactory;
use Guzzle\Http\Client;

class CurrencyISO4217 {

  public $currencyName;

  public $currencyCode;

  public $numericCode;

  public $minorUnit;

  public $smallestUnit;

  public $sourceURL;

  private static $currencies;

  public function __construct($sourceURL = 'http://www.currency-iso.org/dam/downloads/table_a1.xml') {
    $this->sourceURL = $sourceURL;
  }

  public static function getList() {
    if (!isset(self::$currencies)) {
      $data = file_get_contents(__DIR__ . '/currency-iso-4217.xml');
      $xml = new \SimpleXMLElement($data);
      self::$currencies = array();
      foreach ($xml->CcyTbl->CcyNtry as $currency_entry) {
        $currency = self::instantiateNew();
        $currency->loadFromRawData($currency_entry);
        self::$currencies[$currency->currencyCode] = $currency;
      }
    }
    return self::$currencies;
  }

  /**
   * Creates a blank instance of __CLASS__ with the same constructor parameters
   * as the class that is doing the instantiation.
   *
   * @return \Apigee\Mint\CurrencyISO4217
   */
  public static function instantiateNew() {
    return new CurrencyISO4217();
  }

  /**
   * Given an associative array from the raw JSON response, populates the
   * object with that data.
   *
   * @param array $data
   * @param bool  $reset
   *
   * @return void
   */
  public function loadFromRawData($data, $reset = FALSE) {
    if ($reset) {
      $this->initValues();
    }
    $this->currencyName = (string) $data->CcyNm;
    $this->currencyCode = (string) $data->Ccy;
    $this->numericCode = (int) $data->CcyNbr;
    $this->minorUnit = (int) $data->CcyMnrUnts;
    $this->smallestUnit = 1 / pow(10, $this->minorUnit);
  }

  /**
   * Returns all member variables to their default values.
   *
   * @return mixed
   */
  protected function initValues() {
    $this->currencyName = NULL;
    $this->currencyCode = NULL;
    $this->numericCode = NULL;
    $this->minorUnit = NULL;
  }

  /**
   * Returns a JSON representation of the object.
   *
   * @return mixed
   */
  public function __toString() {
    return json_encode($this);
  }

}