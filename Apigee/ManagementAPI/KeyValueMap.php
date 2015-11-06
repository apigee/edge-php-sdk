<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ResponseException;
use Apigee\Util\OrgConfig;
use Psr\Log\NullLogger;

/**
 * The KeyValueMap class implements a general-purpose datastore.
 * The class can contain multiple maps, where each map has a name and can
 * have multiple key-value pairs stored in it.
 */
class KeyValueMap extends Base
{

    /**
     * Initializes default values of all member variables.
     *
     * @param OrgConfig $config
     * @param string $environment
     */
    public function __construct(OrgConfig $config, $environment = '*')
    {
        if ($environment == '*') {
            $baseUrl = '/o/' . rawurlencode($config->orgName) . '/keyvaluemaps';
        } else {
            $baseUrl = '/o/' . rawurlencode($config->orgName) . '/e/' . rawurlencode($environment) . '/keyvaluemaps';
        }
        $this->init($config, $baseUrl);
    }

    /**
     * Fetches a value from a named map/key. If no such map or key is found,
     * returns null.
     *
     * @param string $mapName
     * @param string $keyName
     * @return null|string
     */
    public function getEntryValue($mapName, $keyName)
    {
        $tempConfig = clone $this->config;
        // Disable logging and all subscribers for this fetch attempt.
        $tempConfig->logger = new NullLogger();
        $tempConfig->subscribers = array();
        $cachedConfig = $this->config;
        $this->config = $tempConfig;

        $url = rawurlencode($mapName) . '/entries/' . rawurlencode($keyName);
        $value = null;
        try {
            $this->get($url);
            $responseObj = $this->responseObj;
            $value = $responseObj['value'];
        } catch (ResponseException $e) {
        }

        $this->config = $cachedConfig;
        return $value;
    }

    /**
     * Fetches all entries for a named map and returns them as an associative
     * array.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $mapName
     * @return array
     */
    public function getAllEntries($mapName)
    {
        // If something went wrong, the following line will throw a ResponseException.
        $this->get(rawurlencode($mapName));
        $entries = array();
        $response = $this->responseObj;
        foreach ($response['entry'] as $entry) {
            $entries[$entry['name']] = $entry['value'];
        }
        return $entries;
    }

    /**
     * Sets a value for a named map/key.
     *
     * This method performs both inserts and updates; that is, if the key does
     * not yet exist, it will create it.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $mapName
     * @param string $keyName
     * @param string $value
     */
    public function setEntryValue($mapName, $keyName, $value)
    {
        $url = rawurlencode($mapName) . '/entries/' . rawurlencode($keyName);
        $payload = array(
            'entry' => array(
                array(
                    'name' => $keyName,
                    'value' => (string)$value
                )
            ),
            'name' => $mapName
        );
        // If something went wrong, the following line will throw a ResponseException.
        $this->put($url, $payload);
    }

    /**
     * Deletes a key-value pair from a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $mapName
     * @param string $keyName
     */
    public function deleteEntry($mapName, $keyName)
    {
        $url = rawurlencode($mapName) . '/entries/' . rawurlencode($keyName);
        // If something went wrong, the following line will throw a ResponseException.
        $this->httpDelete($url);
    }

    /**
     * Creates a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $mapName
     * @param array|null $entries An optional array of key/value pairs for the map.
     */
    public function create($mapName, $entries = null)
    {
        $payload = array(
            'entry' => array(),
            'name' => $mapName
        );
        if (!empty($entries) && is_array($entries)) {
            foreach ($entries as $key => $value) {
                $payload['entry'][] = array('name' => $key, 'value' => $value);
            }
        }
        // If something went wrong, the following line will throw a ResponseException.
        $this->post(null, $payload);
    }

    /**
     * Deletes a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $mapName
     */
    public function delete($mapName)
    {
        // If something went wrong, the following line will throw a ResponseException.
        $this->httpDelete(rawurlencode($mapName));
    }
}
