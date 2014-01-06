<?php

namespace Apigee\Util;

/**
 * Cache classes must extend this class and provide
 * a default constructor, Factory class will create an
 * instance of such class and then invoke setup method,
 * setup method is passed as arguments what is returned
 * by getConfig method, setup logic can then be performed
 * out of the constructor. Notice that setup should only be
 * invoked once and only by the Factory class
 *
 * @author isaias
 *
 */
class CacheManager {

  private $config;

  public function __construct() {
    $GLOBALS['ApigeeMintCache'] = array();
  }

  /**
   * Since static methods are not overriden, then Factory class
   * invokes this method right after cache manager is created, thus
   * setup logic can be executed here instead of on the constructor
   *
   * @param array $config
   */
  public function setup($config) {
    $this->config = $config;
  }

  /**
   * This method returns a config array that is later
   * passed to setup method by Factory class
   *
   * @return array
   */
  public function getConfig() {
    return array();
  }

  /**
   * Cache a value given $data and identifing it by $cid
   *
   * @param string $cid
   * @param mixed $data
   */
  public function set($cid, $data) {
    $GLOBALS['ApigeeMintCache'][$cid] = $data;
  }

  /**
   * Attempt to get a value from cache given the id specified by $cid
   * if no value is found in cache, then value specified by $data is
   * returned. if no $data is specified it will return NULL
   *
   * @param string $cid
   * @param mixed $data
   */
  public function get($cid, $data = NULL) {
    if (array_key_exists($cid, $GLOBALS['ApigeeMintCache'])) {
      $data = $GLOBALS['ApigeeMintCache'][$cid];
    }
    return $data;
  }

  public function clear($cid = NULL, $wildcard = FALSE) {
    if (empty($cid) && $wildcard === TRUE) {
      $GLOBALS['ApigeeMintCache'] = array();
    }
    elseif ($wildcard === TRUE) {
      foreach ($GLOBALS['ApigeeMintCache'] as $cache_id) {
        if (strpos($cache_id, $cid) === 0) {
          unset($GLOBALS['ApigeeMintCache'][$cache_id]);
        }
      }
    }
    else {
      unset($GLOBALS['ApigeeMintCache'][$cid]);
    }
  }
}