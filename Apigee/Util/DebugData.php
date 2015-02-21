<?php

namespace Apigee\Util;

/**
 * Contains raw debug data from the Management API
 * passed to the calling client.
 * Access this data through the $debugData property of APIObject.
 *
 * @see APIObject::$debugData
 */
class DebugData
{
    public static $raw;
    public static $opts;
    public static $data;
    public static $code;
    public static $code_status;
    public static $code_class;
    public static $exception;
    public static $time_elapsed;

    public static function toArray()
    {
        return array(
            'raw' => self::$raw,
            'opts' => self::$opts,
            'data' => self::$data,
            'code' => self::$code,
            'code_status' => self::$code_status,
            'code_class' => self::$code_class,
            'exception' => self::$exception,
            'time_elapsed' => self::$time_elapsed
        );
    }
    public static function fromArray($array) {
        foreach (array('raw', 'opts', 'data', 'code', 'code_status', 'code_class', 'exception', 'time_elapsed') as $key) {
            if (array_key_exists($key, $array)) {
                self::$$key = $array[$key];
            }
        }
    }
}
