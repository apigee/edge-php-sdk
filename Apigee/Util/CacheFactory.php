<?php

namespace Apigee\Util;

use Apigee\Exceptions\ParameterException;

class CacheFactory {

  /**
   * Holds the cache managers
   * @var array
   */
  private static $cache_managers;

  /**
   * Base class that cache managers must extend
   * @var \ReflectionClass
   */
  private static $base_cache_class;

  /**
   * Helps this factory to know if is setup
   * @var bool
   */
  private static $is_setup = FALSE;

  private static $default_cache_manager;

  /**
   * If a cache manager is set with this method, then when
   * getCacheManager is invoked with $cache_manager_class_name as NULL
   * it will return this cache manager
   *
   * @param \Apigee\Util\CacheManager $cacheManager
   */
  public static function setDefault(CacheManager $cache_manager) {
    self::$default_cache_manager = $cache_manager;
  }

  private static function setup() {
    self::$cache_managers = array();
    self::$base_cache_class = new \ReflectionClass('Apigee\Util\CacheManager');
    self::$is_setup = TRUE;
  }

  /**
   * It will lookup for a cache manager of class given by $cache_manager_class_name,
   * if $cache_manager_class_name is NULL then it will attempt to return the default
   * cache manager set by setDefault(), it no default manager has been set
   * then an Apigee\Exceptions\ParameterException will be thrown
   *
   * @param string $cache_manager_class_name
   *   Class name of the cache manager to retrieve. This class name must be of a class
   *   that extends CacheManager, otherwise it will thrown a Apigee\Exceptions\ParameterException
   * @param \Psr\Log\LoggerInterface|null $logger
   * @return \Apigee\Util\CacheManager
   */
  public static function getCacheManager($cache_manager_class_name = 'Apigee\Util\CacheManager', $logger = NULL) {
    if (!self::$is_setup) {
      self::setup();
    }
    if ($cache_manager_class_name == NULL) {
      if (self::$default_cache_manager == NULL) {
        throw new ParameterException('$cache_manager_class_name cannot be null if no default cache manager has been specified by invoking CacheFactory::setDefault()');
      }
      return self::$default_cache_manager;
    }
    if (!isset($cache_managers[$cache_manager_class_name])) {
      try {
        $class = new \ReflectionClass($cache_manager_class_name);
        if ($class->getName() == 'Apigee\Util\CacheManager' || $class->isSubclassOf('Apigee\Util\CacheManager')) {
          $cache_manager = $class->newInstance();
          $arg = $cache_manager->getConfig();
          $cache_manager->setUp($arg);
          $cache_managers[$cache_manager_class_name] = $cache_manager;
        }
        else {
          if ($logger instanceof \Psr\Log\LoggerInterface) {
            $logger->error('Class ' . $cache_manager_class_name . ' does not extend Apigee\Util\Cache, an instance of  Apigee\Util\Cache will be used instead');
          }
          throw new ParameterException('Class ' . $cache_manager_class_name . ' does extend Apigee\Util\CacheManager');
        }
      }
      catch (\ReflectionException $re) {
        if ($logger instanceof \Psr\Log\LoggerInterface) {
          $logger->error('Could not load cache manager class ' . $cache_manager_class_name);
        }
        throw new ParameterException('Could not load cache manager class ' . $cache_manager_class_name . '.');
      }
    }
    return $cache_managers[$cache_manager_class_name];
  }
}