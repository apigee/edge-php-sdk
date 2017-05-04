<?php

namespace Apigee\Mint\DataStructures;

use ReflectionClass;
use Apigee\Util\APIObject;

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
                $notice = 'No setter method was found for property “%s” on class “%s”.';
                APIObject::$logger->notice(sprintf($notice, $property_name, $class_name));
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
     * @param string $name Name of the private property to set
     * @param mixed $value Value to be set
     */
    public function __set($name, $value)
    {
        $class_name = get_class($this);
        if (!array_key_exists($class_name, self::$childSetters)) {
            self::$childSetters[$class_name] = self::getSetterMethods($class_name);
        }
        $property_setter_name = 'set' . ucfirst($name);
        if (array_key_exists($property_setter_name, self::$childSetters[$class_name])) {
            $method = self::$childSetters[$class_name][$property_setter_name];
            $method->invoke($this, $value);
        } else {
            $notice = 'No setter method was found for property “%s” on class “%s”.';
            APIObject::$logger->notice(sprintf($notice, $name, $class_name));
        }
        $warning = 'Attempt to access property “%s” without its accessor in class “%s”.';
        APIObject::$logger->warning(sprintf($warning, $name, $class_name));
    }

    /**
     * Encapsulation enforcement for old consumers of this class and its children.
     *
     * This magic method is meant to never be used. It is only for backwards
     * compatibility to provide old consumers of the classes that inherit this class
     * which at the beginning did not provide accessors methods. These old references
     * must be upgraded to accessor methods since properties are now marked as private
     *
     * @param string $name Name of the property
     * @return mixed
     */
    public function __get($name)
    {
        $class_name = get_class($this);
        $notice = 'Attempt to access property “%s” without its accessor on class “%s” from %s';
        $noticeArgs = array($name, $class_name, json_encode(debug_backtrace()));
        APIObject::$logger->notice(vsprintf($notice, $noticeArgs));
        $property_accessor_name = 'get' . ucfirst($name);
        if (method_exists($this, $name)) {
            return $this->$property_accessor_name();
        } else {
            $warning = 'No accessor method was found for property “%s” on class “%s”.';
            APIObject::$logger->warning(sprintf($warning, $name, $class_name));
            return null;
        }
    }

    public function __toString()
    {
        $data = array();
        $reflect = new \ReflectionClass($this);
        /** @var \ReflectionProperty $property */
        foreach ($reflect->getProperties() as $property) {
            $method_name = 'get' . ucfirst($property->getName());
            $value = null;
            if (method_exists($this, $method_name)) {
                $value = $this->{$method_name}();
            }
            // Additional check for getters of boolean properties.
            // Ex.: $this->isBoolean; $this->isBoolean();
            elseif (method_exists($this, $property->getName())) {
                $value = $this->{$property->getName()}();
            }

            if (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    $value = json_decode((string)$value, true);
                } else {
                    $value = null;
                }
            }

            $data[$property->getName()] = $value;
        }
        return json_encode($data);
    }
}
