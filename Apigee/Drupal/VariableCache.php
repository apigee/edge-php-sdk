<?php

namespace Apigee\Drupal;

/**
 * A wrapper class around Drupal's variable_get/variable_set.
 *
 * This class will be removed in a future release.
 *
 * @deprecated
 */
class VariableCache implements \Apigee\Util\KeyValueStoreInterface
{
    public function get($key, $default = null)
    {
        variable_get($key, $default);
    }

    public function set($key, $value)
    {
        variable_set($key, $value);
    }

    public function save()
    {
        // Do nothing, because $this->set saves to DB.
    }
}