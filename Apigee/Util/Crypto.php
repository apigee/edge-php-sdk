<?php
/**
 * @file
 * Utility library for cryptological functionality.
 *
 * All methods are declared static.
 *
 * @author djohnson
 */

namespace Apigee\Util;

class Crypto {

  private static $crypto_key;

  public static function setKey($key) {
    self::$crypto_key = $key;
  }

  /**
   * Encrypts a string. Caches the result so that expensive encryption does
   * not need to happen more often than necessary.
   *
   * @static
   * @param string $string
   * @return string
   */
  public static function encrypt($string) {
    static $encrypted_strings = array();

    if (isset($encrypted_strings[$string])) {
      // Already encrypted this one once; use cached version.
      return $encrypted_strings[$string];
    }

    srand();
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
    $iv_base64 = rtrim(base64_encode($iv), '='); // Guaranteed to be 22 char long
    // Store password length so we can accurately trim in case of NULL-padding
    $encrypt = strlen($string) . "\n" . $string;
    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, self::$crypto_key, $encrypt, MCRYPT_MODE_CBC, $iv);
    $encrypted_strings[$string] = $iv_base64 . base64_encode($encrypted);
    return $encrypted_strings[$string];
  }

  /**
   * Decrypts a string which was encrypted with Crypto::encrypt().
   *
   * @static
   * @param string $scrambled
   * @return string
   */
  public static function decrypt($scrambled) {
    $iv_base64 = substr($scrambled, 0, 22) . '==';
    $string_encrypted = substr($scrambled, 22);

    $iv = base64_decode($iv_base64);
    if ($iv === FALSE) {
      throw new \Apigee\Exceptions\ParameterException('Unable to parse encrypted string.');
    }
    $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::$crypto_key, base64_decode($string_encrypted), MCRYPT_MODE_CBC, $iv);
    list ($length, $password) = explode("\n", $decrypted, 2);
    return substr($password, 0, intval($length));
  }

}