<?php

namespace Apigee\Util;

/**
 * Interface KeyValueStoreInterface
 * @package Apigee\Util
 * Defines functions necessary to read/write variables to a persistent store.
 *
 * @deprecated
 */
interface KeyValueStoreInterface
{
    /**
     * Reads a value from the persistent store. If value is missing,
     * the value specified by $default is returned instead.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Sets a value to be saved by the persistent store.
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * Saves values to the persistent store.
     */
    public function save();
}
