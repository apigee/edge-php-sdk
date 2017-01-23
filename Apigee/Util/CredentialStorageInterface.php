<?php

namespace Apigee\Util;

/**
 * An interface describing how bearer-tokens and other credentials will be locally stored/cached.
 * @package Apigee\Util
 */
interface CredentialStorageInterface {

    /**
     * Writes a token or credential to a persistent datastore.
     *
     * @param string $identifier
     * @param string $credential_data
     */
    public function write($identifier, $credential_data);

    /**
     * Clears the persistent datastore.
     */
    public function clear();

    /**
     * Reads a token from the persistent datastore. If it is not found, returns NULL.
     *
     * @param string $identifier
     * @return string|null
     */
    public function read($identifier);
}
