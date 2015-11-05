<?php

namespace Apigee\Util;

/**
 * The base class for a cache manager.
 * Cache classes must extend this class and provide a default constructor.
 *
 * A Factory class creates an instance of the class and  then invokes the
 * setup() method. The setup() method is passed an argument that is returned
 * by the getConfig() method, which means setup logic can be performed
 * outside of the constructor.
 *
 * Note that the setup() method should be invoked once and only once by the
 * Factory class.
 *
 * @author isaias
 *
 */
class CacheManager
{

    private $config;

    private static $cache;

    /**
     * @internal
     */
    public function __construct()
    {
        self::$cache = array();
    }

    /**
     * Since static methods are not overridden, the Factory class
     * invokes this method right after the cache manager is created.
     * That means setup logic can be executed here instead of in the constructor.
     *
     * @param array $config
     */
    public function setup($config)
    {
        $this->config = $config;
    }

    /**
     * Returns a config array that is later
     * passed to the setup() method by the Factory class.
     *
     * @return array
     */
    public function getConfig()
    {
        return array();
    }

    /**
     * Cache a value given $data and identifying it by $cid
     *
     * @param string $cid
     * @param mixed $data
     */
    public function set($cid, $data)
    {
        self::$cache[$cid] = $data;
    }

    /**
     * Attempt to get a value from cache given the ID specified by $cid.
     * If no value is found in the cache, then value specified by $data is
     * returned.
     * If no $data is specified, return null.
     *
     * @param string $cid
     * @param mixed $data
     */
    public function get($cid, $data = null)
    {
        if (array_key_exists($cid, self::$cache)) {
            $data = self::$cache[$cid];
        }
        return $data;
    }

    public function clear($cid = null, $wildcard = false)
    {
        if (empty($cid) && $wildcard === true) {
            self::$cache = array();
        } elseif ($wildcard === true) {
            foreach (self::$cache as $cache_id) {
                if (strpos($cache_id, $cid) === 0) {
                    unset(self::$cache[$cache_id]);
                }
            }
        } else {
            unset(self::$cache[$cid]);
        }
    }
}
