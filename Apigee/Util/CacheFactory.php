<?php

namespace Apigee\Util;

use Apigee\Exceptions\ParameterException;

/**
 * Factory class used to set the default cache manager and to create
 * an instance of a cache manager.
 */
class CacheFactory
{

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
    public static function setDefault(CacheManager $cache_manager)
    {
        self::$default_cache_manager = $cache_manager;
    }

    /**
     * Returns the default cache manager if there is one,
     * otherwise it returns null
     *
     * @return CacheManager
     */
    public static function getDefault()
    {
        return self::$default_cache_manager;
    }

    private static function setup()
    {
        self::$cache_managers = array();
        self::$base_cache_class = new \ReflectionClass('Apigee\Util\CacheManager');
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
    public static function getCacheManager($cache_manager_class_name = 'Apigee\Util\CacheManager', $logger = null)
    {
        if (!self::$is_setup) {
            self::setup();
        }
        if ($cache_manager_class_name == null) {
            if (self::$default_cache_manager == null) {
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
                } else {
                    if ($logger instanceof \Psr\Log\LoggerInterface) {
                        $logger->error('Class ' . $cache_manager_class_name . ' does not extend Apigee\Util\Cache, an instance of  Apigee\Util\Cache will be used instead');
                    }
                    throw new ParameterException('Class ' . $cache_manager_class_name . ' does extend Apigee\Util\CacheManager');
                }
            } catch (\ReflectionException $re) {
                if ($logger instanceof \Psr\Log\LoggerInterface) {
                    $logger->error('Could not load cache manager class ' . $cache_manager_class_name);
                }
                throw new ParameterException('Could not load cache manager class ' . $cache_manager_class_name . '.');
            }
        }
        return $cache_managers[$cache_manager_class_name];
    }
}
