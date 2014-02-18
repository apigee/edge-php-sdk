<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ResponseException;

/**
 * The KeyValueMap class implements a general-purpose datastore.
 * The class can contain multiple maps, where each map has a name and can
 * have multiple key-value pairs stored in it.
 */
class KeyValueMap extends Base implements KeyValueMapInterface
{

    /**
     * Initializes default values of all member variables.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $base_url = '/o/' . rawurlencode($config->orgName) . '/keyvaluemaps';
        $this->init($config, $base_url);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntryValue($map_name, $key_name)
    {
        $url = rawurlencode($map_name) . '/entries/' . rawurlencode($key_name);
        $value = NULL;
        try {
            $this->get($url);
            $response_obj = $this->responseObj;
            $value = $response_obj['value'];
        } catch (ResponseException $e) {
        }
        return $value;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function deleteEntry($map_name, $key_name)
    {
        $url = rawurlencode($map_name) . '/entries/' . rawurlencode($key_name);
        // If something went wrong, the following line will throw a ResponseException.
        $this->http_delete($url);
    }

    /**
     * {@inheritDoc}
     */
    public function create($map_name, $entries = NULL)
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
        $this->post(NULL, $payload);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($map_name)
    {
        // If something went wrong, the following line will throw a ResponseException.
        $this->http_delete(rawurlencode($map_name));
    }
}