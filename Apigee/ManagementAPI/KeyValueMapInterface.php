<?php
namespace Apigee\ManagementAPI;

/**
 * The interface that a key/value map class must implement.
 */
interface KeyValueMapInterface
{
    /**
     * Fetches a value from a named map/key. If no such map or key is found,
     * returns null.
     *
     * @param string $map_name
     * @param string $key_name
     * @return null|string
     */
    public function getEntryValue($map_name, $key_name);

    /**
     * Fetches all entries for a named map and returns them as an associative
     * array.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @return array
     */
    public function getAllEntries($map_name);

    /**
     * Sets a value for a named map/key.
     * This method performs both inserts and updates;
     * that is, if the key does not yet exist, it will create it.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @param string $key_name
     * @param $value
     */
    public function setEntryValue($map_name, $key_name, $value);

    /**
     * Deletes a key and value from a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @param string $key_name
     * @param $value
     */
    public function deleteEntry($map_name, $key_name);

    /**
     * Creates a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     * @param array $entries An optional array of key/value pairs for the map.
     */
    public function create($map_name, $entries = null);

    /**
     * Deletes a map.
     *
     * @throws \Apigee\Exceptions\ResponseException
     *
     * @param string $map_name
     */
    public function delete($map_name);
}
