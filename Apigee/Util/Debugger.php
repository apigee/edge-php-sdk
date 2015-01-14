<?php

namespace Apigee\Util;

use \ReflectionClass as ReflectionClass;
use \stdClass as stdClass;
use Apigee\Exceptions\ParameterException;

/**
 * Class Debugger
 * @package Apigee\Util
 * @deprecated
 */
class Debugger
{

    private static function getArrayValues($array)
    {
        $items = array();
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $items[$key] = self::getDeclaredValues($value);
            } else if (is_array($value)) {
                $items[$key] = self::getArrayValues($value);
            } else {
                $items[$key] = $value;
            }
        }
        return $items;
    }

    public static function getDeclaredValues($object)
    {
        if (is_object($object)) {
            $class = new ReflectionClass($object);
            $properties = $class->getProperties();
            $newObject = new stdClass;
            foreach ($properties as $property) {
                if ($property->getDeclaringClass()->getName() == $class->getName()) {
                    $property->setAccessible(true);
                    $name = $property->getName();
                    $value = $property->getValue($object);
                    if (is_object($value)) {
                        $newObject->$name = self::getDeclaredValues($value);
                    } else if (is_array($value)) {
                        $newObject->$name = self::getArrayValues($value);
                    } else {
                        $newObject->$name = $value;
                    }
                }
            }
        } else if (is_array($object)) {
            return self::getArrayValues($object);
        } else if (is_null($object)) {
            return null;
        } else {
            throw new ParameterException('Not an object');
        }
        return $newObject;
    }
}
