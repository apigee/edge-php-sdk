<?php
namespace Apigee\Util;

/**
 * Class to read from cache and write to cache a persistent name/value pair.
 */
class Cache {
  /**
   * Reads a persistent name/value pair from cache. Mostly this is a wrapper
   * around Drupal's variable_get() method to make tests and scripts not dependent
   * on Drupal's environment.
   *
   * @static
   * @param $name
   * @param $default
   * @return mixed|null
   */
  public static function get($name, $default) {
    if (self::isRunningSimpletest()) {
      return self::simpletestVariableGet($name, $default);
    }
    if (function_exists('variable_get')) {
      return variable_get($name, $default);
    }
    if (isset($GLOBALS['_variable_cache']) && isset($GLOBALS['_variable_cache'][$name])) {
      return $GLOBALS['_variable_cache'][$name];
    }
    return $default;
  }

  /**
   * Writes a persistent name/value pair to cache. Mostly this is a wrapper
   * around Drupal's variable_set() method.
   *
   * @static
   * @param $name
   * @param $value
   */
  public static function set($name, $value) {
    if (function_exists('variable_set')) {
      variable_set($name, $value);
    }
    if (!isset($GLOBALS['_variable_cache'])) {
      $GLOBALS['_variable_cache'] = array();
    }
    $GLOBALS['_variable_cache'][$name] = $value;
  }

  /**
   * Make an educated guess as to whether we're running a simpletest
   * case. We return TRUE if a DrupalWebTestCase is being run, otherwise
   * we return FALSE. The result is cached so that the actual logic
   * here only happens once.
   *
   * @static
   * @return bool
   */
  private static function isRunningSimpletest() {
    static $is_running_simpletest = NULL;

    if ($is_running_simpletest === NULL) {
      if (!class_exists('Database') || !method_exists('Database', 'getConnectionInfo')) {
        // If we're running simpletest, we're doing so as a DrupalUnitTest, which has no
        // database, so for our purposes we're running outside of fully-bootstrapped Drupal.
        $is_running_simpletest = FALSE;
      }
      else {
        // Check to see if there is a database connection called simpletest_original_default.
        // This is what simpletest renames the 'default' connection to.
        try {
          $info = \Database::getConnectionInfo('simpletest_original_default');
          $is_running_simpletest = (isset($info) && is_array($info));
        }
        catch (\DatabaseConnectionNotDefinedException $e) {
          // Sorry, no such connection.
          $is_running_simpletest = FALSE;
        }
      }
    }
    return $is_running_simpletest;
  }

  /**
   * When we're in a simpletest environment, variable_get needs to happen against
   * the live database, not simpletest's sandbox.
   *
   * @static
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  private static function simpletestVariableGet($name, $default) {
    // Is the variable stored in memory?
    if (isset($GLOBALS['_variable_cache']) && is_array($GLOBALS['_variable_cache']) && array_key_exists($name, $GLOBALS['_variable_cache'])) {
      return $GLOBALS['_variable_cache'][$name];
    }
    // Nope, so let's fetch it from the live, non-simpletest database.
    \Database::setActiveConnection('simpletest_original_default');
    $value = variable_get($name, $default);
    // Reset connection to the default one.
    \Database::setActiveConnection('default');
    // Store our value in memory so it can be read there.
    $GLOBALS['_variable_cache'][$name] = $value;
    return $value;
  }

  // Note that there's no simpletestVariableSet. While we are in the simpletest
  // sandbox, we should not need to write anything to the db. This means that Cache::set
  // simply writes the value to $GLOBALS['_variable_cache'], where subsequent Cache::get
  // invocations will read it.

}
