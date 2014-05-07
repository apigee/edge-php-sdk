<?php
namespace Apigee\Mint\DataStructures;

use \ReflectionClass;
use \Apigee\Util\APIObject;

class DataStructure
{

    private static $childSetters = array();

    private static function getSetterMethods($class_name)
    {
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
                $method->setAccessible(true);
            }
            $setter_methods[$method_name] = $method;
        }
        return $setter_methods;
    }

    protected function loadFromRawData($data, $exclude_variables = array())
    {
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
            } else {
                APIObject::$logger->notice('No setter method was found for property "' . $property_name . '" on class "' . $class_name . '".');
            }
        }
    }

    /**
     * Encapsulation enforcement for old consumers of this class and its children
     *
     * This magic method is meant never to be used, it is only for backwards
     * compatibility for those consumers of classes that inherit from this class that in
     * the beginning did not provide mutators for its properties. Those consumers should
     * also update the way they set its properties to the mutators method instead of the
     * property, since properties are now marked as private.
     *
     * @param $name Name of the private property to set
     * @param $value Value to be set
     */
    public function __set($name, $value) {
        $class_name = get_class($this);
        if (!array_key_exists($class_name, self::$childSetters)) {
            self::$childSetters[$class_name] = self::getSetterMethods($class_name);
        }
        $property_setter_name = 'set' . ucfirst($name);
        if (array_key_exists($property_setter_name, self::$childSetters[$class_name])) {
            $method = self::$childSetters[$class_name][$property_setter_name];
            $method->invoke($this, $value);
        }
        else {
            APIObject::$logger->notice('No setter method was found for property "' . $name . '" on class "' . $class_name . '".');
        }
        APIObject::$logger->warning('Attempt to access property "' . $name . '" without its accessor on class "' . $class_name . '".');
    }

    /**
     * Encapsulation enforcement for old consumers of this class and its children.
     *
     * This magic method is meant to never be used. It is only for backwards
     * compatibility to provide old consumers of the classes that inherit this class
     * which at the beginning did not provide accessors methods. These old references
     * must be upgraded to accessor methods since properties are now marked as private
     *
     * @param $name Name of the property
     * @return mixed
     */
    public function __get($name) {
        $class_name = get_class($this);
        APIObject::$logger->notice('Attempt to access property "' . $name . '" without its accessor on class "' . $class_name . '" from '  . json_encode(debug_backtrace()));
        $property_accessor_name = 'get' . ucfirst($name);
        if (method_exists($this, $name)) {
          return $this->$property_accessor_name();
        }
        else {
            APIObject::$logger->warning('No accessor method was found for property "' . $name . '" on class "' . $class_name . '".');
        }
    }
}