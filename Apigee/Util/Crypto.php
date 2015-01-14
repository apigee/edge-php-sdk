<?php

/**
 * This file will be removed in a future version of Edge SDK.
 *
 * @author djohnson
 */
namespace Apigee\Util;

/**
 * Utility class for cryptological functionality.
 *
 * All methods are declared static.
 *
 * @package Apigee\Util
 * @deprecated
 */
class Crypto
{

    private static $cryptoKey;

    private static $encryptedStrings;

    const MAX_KEY_LENGTH = 32;

    /**
     * Sets the encryption key.
     *
     * Set the key before you call encrypt(). You must call decrypt() with the
     * same value as used for encrypt(). If the key length exceeds the maximum
     * (defined in the class constant MAX_KEY_LENGTH) it will be truncated.
     *
     * @static
     * @param string $key
     */
    public static function setKey($key)
    {
        if (strlen($key) > self::MAX_KEY_LENGTH) {
            $key = substr($key, 0, self::MAX_KEY_LENGTH);
        }
        self::$cryptoKey = $key;
        self::$encryptedStrings = array();
    }

    /**
     * Encrypts a string using the specified key.
     * Caches the result so that expensive encryption does
     * not need to happen more often than necessary.
     *
     * @static
     * @param string $string
     * @return string
     */
    public static function encrypt($string)
    {
        if (!isset(self::$encryptedStrings)) {
            self::$encryptedStrings = array();
        }
        if (!array_key_exists($string, self::$encryptedStrings)) {
            srand();
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
            $iv_base64 = rtrim(base64_encode($iv), '='); // Guaranteed to be 22 char long
            // Store password length so we can accurately trim in case of null-padding
            $encrypt = strlen($string) . "\n" . $string;
            $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, self::$cryptoKey, $encrypt, MCRYPT_MODE_CBC, $iv);
            self::$encryptedStrings[$string] = $iv_base64 . base64_encode($encrypted);
        }
        return self::$encryptedStrings[$string];
    }

    /**
     * Decrypts a string which was encrypted with Crypto::encrypt().
     *
     * @static
     * @param string $scrambled
     * @return string
     *
     * @throws \Apigee\Exceptions\ParameterException
     */
    public static function decrypt($scrambled)
    {
        // If $scrambled is not valid base64, abort.
        if (!preg_match('!^[A-Za-z0-9+/]+={0,2}$!', $scrambled)) {
            throw new \Apigee\Exceptions\ParameterException('Encrypted string is not properly formed.');
        }
        $iv_base64 = substr($scrambled, 0, 22) . '==';
        $string_encrypted = substr($scrambled, 22);

        $iv = base64_decode($iv_base64);
        if ($iv === false || strlen($iv) < 16) {
            throw new \Apigee\Exceptions\ParameterException('Unable to parse encrypted string.');
        }
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::$cryptoKey, base64_decode($string_encrypted), MCRYPT_MODE_CBC, $iv);
        if (strpos($decrypted, "\n") === false) {
            throw new \Apigee\Exceptions\ParameterException('Unable to decrypt encrypted string.');
        }
        list ($length, $password) = explode("\n", $decrypted, 2);
        return substr($password, 0, intval($length));
    }

}
