<?php

namespace Apigee\Mint\DataStructures;

use \ReflectionClass;

class DataStructure {

  private static $childSetters = array();

  private static function getSetterMethods($class_name) {
    $class = new ReflectionClass($class_name);
    $setter_methods = array();
    foreach ($class->getMethods() as $method) {
      if ($method->getDeclaringClass() != $class) {
        continue;
      }
      $method_name = $method->getName();
      if (strpos($method_name, 'set') !== 0) {
        continue;
      }
      if ($method->isPrivate()) {
        $method->setAccessible(TRUE);
      }
      $setter_methods[$method_name] = $method;
    }
    return $setter_methods;
  }

  protected function loadFromRawData($data, $exclude_variables = array()) {
    $class_name = get_class($this);
    if (!array_key_exists($class_name, self::$childSetters)) {
      self::$childSetters[$class_name] = self::getSetterMethods($class_name);
    }

    foreach ($data as $property_name => $property_value) {
      if (in_array($property_name, $exclude_variables)) {
        continue;
      }
      $property_setter_name = 'set' . ucfirst($property_name);
      if (array_key_exists($property_setter_name, self::$childSetters[$class_name])) {
        $method = self::$childSetters[$class_name][$property_setter_name];
        $method->invoke($this, $property_value);
      }
      else {
        \Apigee\Util\APIObject::$logger->notice('No setter method was found for property "' . $property_name . '"');
      }
    }
  }
}