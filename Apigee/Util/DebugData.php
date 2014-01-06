<?php

namespace Apigee\Util;

class DebugData {
  public static $raw;
  public static $opts;
  public static $data;
  public static $code;
  public static $code_status;
  public static $code_class;
  public static $exception;

  public static function toArray() {
    return array(
      'raw' => self::$raw,
      'opts' => self::$opts,
      'data' => self::$data,
      'code' => self::$code,
      'code_status' => self::$code_status,
      'code_class' => self::$code_class,
      'exception' => self::$exception
    );
  }
}