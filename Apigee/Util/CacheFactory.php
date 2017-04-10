<?php

namespace Apigee\Util;

use Apigee\Exceptions\ParameterException;
use Psr\Log\LoggerInterface;

/**
 * Factory class used to set the default cache manager and to create
 * an instance of a cache manager.
 */
class CacheFactory
{
  
    const CACHE_MGR_CLASS = 'Apigee\Util\CacheManager';

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
    private static $is_setup = false;

    /**
     * @var \Apigee\Util\CacheManager
     */
    private static $default_cache_manager;

    /**
     * Sets the default cache manager.
     * If a default cache manager is set with this method, then when
     * getCacheManager() is called with $cache_manager_class_name as null
     * it returns this cache manager
     *
     * @param \Apigee\Util\CacheManager $cacheManager
     */
    public static function setDefault(CacheManager $cacheManager)
    {
        self::$default_cache_manager = $cacheManager;
    }

    /**
     * Returns the default cache manager.
     *
     * @return CacheManager
     */
    public static function getDefault()
    {
        if (self::$default_cache_manager === null) {
          self::$default_cache_manager = self::CACHE_MGR_CLASS;
        };
        return self::$default_cache_manager;
    }

    private static function setup()
    {
        self::$cache_managers = array();
        self::$base_cache_class = new \ReflectionClass(self::CACHE_MGR_CLASS);
        self::$is_setup = true;
    }

    /**
     * Returns a cache manager of the class specified by $cache_manager_class_name.
     * If $cache_manager_class_name is null, then return the default
     * cache manager as set by setDefault().
     * If no default cache manager has been set, then throw an {@link Apigee\Exceptions\ParameterException}.
     *
     * @param string $cache_manager_class_name
     *   Class name of the cache manager to retrieve. This class name must be of a class
     *   that extends CacheManager, otherwise an {@link Apigee\Exceptions\ParameterException} is thrown.
     * @param \Psr\Log\LoggerInterface|null $logger
     * @return \Apigee\Util\CacheManager
     */
    public static function getCacheManager($cache_manager_class_name = self::CACHE_MGR_CLASS, $logger = null)
    {
        static $cache_managers = array();

        if (!self::$is_setup) {
            self::setup();
        }
        if ($cache_manager_class_name === null) {
            if (self::$default_cache_manager === null) {
                throw new ParameterException('Cache Manager class cannot be null.');
            }
            return self::$default_cache_manager;
        }
        if (!isset($cache_managers[$cache_manager_class_name])) {
            try {
                $class = new \ReflectionClass($cache_manager_class_name);
                if ($class->getName() == self::CACHE_MGR_CLASS || $class->isSubclassOf(self::CACHE_MGR_CLASS)) {
                    $cache_manager = $class->newInstance();
                    $arg = $cache_manager->getConfig();
                    $cache_manager->setUp($arg);
                    $cache_managers[$cache_manager_class_name] = $cache_manager;
                } else {
                    $msgArgs = array($cache_manager_class_name, self::CACHE_MGR_CLASS);
                    $msg = vsprintf('Class %s does not extend %s.', $msgArgs);
                    if ($logger instanceof LoggerInterface) {
                        $logger->error($msg);
                    }
                    throw new ParameterException($msg);
                }
            } catch (\ReflectionException $re) {
                $msg = sprintf('Could not load cache manager class %s', $cache_manager_class_name);
                if ($logger instanceof LoggerInterface) {
                    $logger->error($msg);
                }
                throw new ParameterException($msg);
            }
        }
        return $cache_managers[$cache_manager_class_name];
    }
}
