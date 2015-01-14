<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ResponseException;

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
     * @param \Apigee\Util\OrgConfig $config
     * @param string $environment
     */
    public function __construct(\Apigee\Util\OrgConfig $config, $environment = '*')
    {
        if ($environment == '*') {
            $base_url = '/o/' . rawurlencode($config->orgName) . '/keyvaluemaps';
        }
        else {
            $base_url = '/o/' . rawurlencode($config->orgName) . '/e/' . rawurlencode($environment) . '/keyvaluemaps';
        }
        $this->init($config, $base_url);
    }

    /**
     * Fetches a value from a named map/key. If no such map or key is found,
     * returns null.
     *
     * @param string $map_name
     * @param string $key_name
     * @return null|string
     */
    public function getEntryValue($map_name, $key_name)
    {
        $url = rawurlencode($map_name) . '/entries/' . rawurlencode($key_name);
        $value = null;
        try {
            $this->get($url);
            $response_obj = $this->responseObj;
            $value = $response_obj['value'];
        } catch (ResponseException $e) {
        }
        return $value;
    }

    /**
     * Fetches all entries for a named map and returns them as an associative
     * array.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @return array
     */
    public function getAllEntries($map_name)
    {
        // If something went wrong, the following line will throw a ResponseException.
        $this->get(rawurlencode($map_name));
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
     * @param string $map_name
     * @param string $key_name
     * @param $value
     */
    public function setEntryValue($map_name, $key_name, $value)
    {
        $url = rawurlencode($map_name) . '/entries/' . rawurlencode($key_name);
        $payload = array(
            'entry' => array(
                array(
                    'name' => $key_name,
                    'value' => $value
                )
            ),
            'name' => $map_name
        );
        // If something went wrong, the following line will throw a ResponseException.
        $this->put($url, $payload);
    }

    /**
     * Deletes a key and value from a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @param string $key_name
     */
    public function deleteEntry($map_name, $key_name)
    {
        $url = rawurlencode($map_name) . '/entries/' . rawurlencode($key_name);
        // If something went wrong, the following line will throw a ResponseException.
        $this->http_delete($url);
    }

    /**
     * Creates a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @param array|null $entries An optional array of key/value pairs for the map.
     */
    public function create($map_name, $entries = null)
    {
        $payload = array(
            'entry' => array(),
            'name' => $map_name
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
     * @param string $map_name
     */
    public function delete($map_name)
    {
        // If something went wrong, the following line will throw a ResponseException.
        $this->http_delete(rawurlencode($map_name));
    }
}
