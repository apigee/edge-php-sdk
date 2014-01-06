<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ResponseException;

class KeyValueMap extends Base implements KeyValueMapInterface {

  /**
   * Initializes default values of all member variables.
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(\Apigee\Util\OrgConfig $config) {
    $base_url = '/o/' . $this->urlEncode($config->orgName) . '/keyvaluemaps';
    $this->init($config, $base_url);
  }

  /**
   * Fetches a value from a named map/key. If no such map or key is found,
   * returns NULL.
   *
   * @param $map_name
   * @param $key_name
   * @return null|string
   */
  public function getEntryValue($map_name, $key_name) {
    $url = $this->urlEncode($map_name) . '/entries/' . $this->urlEncode($key_name);
    $value = NULL;
    try {
      $this->get($url);
      $response_obj = $this->responseObj;
      $value = $response_obj['value'];
    }
    catch (ResponseException $e) {}
    return $value;
  }

  /**
   * Fetches all entries for a named map and returns them as an associative
   * array.
   *
   * @throws \Apigee\Exceptions\ResponseException
   *
   * @param $map_name
   * @return array
   */
  public function getAllEntries($map_name) {
    // If something went wrong, the following line will throw a ResponseException.
    $this->get($this->urlEncode($map_name));
    $entries = array();
    $response = $this->responseObj;
    foreach ($response['entry'] as $entry) {
      $entries[$entry['name']] = $entry['value'];
    }
    return $entries;
  }

  /**
   * Sets a value for a named map/key. This performs both inserts and updates;
   * that is, if the key does not yet exist, it will create it.
   *
   * @throws \Apigee\Exceptions\ResponseException
   *
   * @param $map_name
   * @param $key_name
   * @param $value
   */
  public function setEntryValue($map_name, $key_name, $value) {
    $url = $this->urlEncode($map_name) . '/entries/' . $this->urlEncode($key_name);
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

  public function deleteEntry($map_name, $key_name) {
    $url = $this->urlEncode($map_name) . '/entries/' . $this->urlEncode($key_name);
    // If something went wrong, the following line will throw a ResponseException.
    $this->http_delete($url);
  }

  public function create($map_name, $entries = NULL) {
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

  public function delete($map_name) {
    // If something went wrong, the following line will throw a ResponseException.
    $this->http_delete($this->urlEncode($map_name));
  }
}