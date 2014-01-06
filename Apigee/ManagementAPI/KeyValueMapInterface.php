<?php
namespace Apigee\ManagementAPI;

interface KeyValueMapInterface {
  public function getEntryValue($map_name, $key_name);
  public function getAllEntries($map_name);
  public function setEntryValue($map_name, $key_name, $value);
  public function deleteEntry($map_name, $key_name);
  public function create($map_name, $entries = NULL);
  public function delete($map_name);
}