<?php

namespace Apigee\Util;

/**
 * Reads/writes bearer-tokens and other credentials from the local filesystem.
 * @package Apigee\Util
 */
class FilesystemCredentialStorage implements CredentialStorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function write($identifier, $credential_data)
    {
        $dir = self::getTokenCacheDir();
        file_put_contents("$dir/$identifier", $credential_data);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $cache_dir = self::getTokenCacheDir();
        if ($dh = opendir($cache_dir)) {
            while (($file = readdir($dh)) !== false) {
                if (is_file($file) && substr($file, 0, 1) != '.') {
                    @unlink($file);
                }
            }
            closedir($dh);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($identifier)
    {
        $dir = self::getTokenCacheDir();
        if (!file_exists("$dir/$identifier")) {
            return false;
        }
        return file_get_contents("$dir/$identifier");
    }

    /**
     * Returns the temp dir where access tokens are cached.
     *
     * @return string
     */
    private static function getTokenCacheDir()
    {
        return sys_get_temp_dir() . '/edge-access-tokens';
    }
}
