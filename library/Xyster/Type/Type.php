<?php
/**
 * Xyster Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Type;
/**
 * Type wrapper class 
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Type
{
    private static $_nesting = 10;
    private static $_types = array('array', 'scalar', 'boolean', 'integer',
        'double', 'string');
    private static $_scalarTypes = array('boolean', 'integer', 'double',
        'string');
    private static $_classes = array();
    private static $_scalarTypeInstances = array();
    protected $_type;
    /**
     * @var ReflectionClass
     */
    protected $_class;

    const INT_MAX_32 = 2147483647;
    const BOOLEAN = 'boolean';
    const DOUBLE = 'double';
    const INTEGER = 'integer';
    const STRING = 'string';

    /**
     * Creates a new type representation
     *
     * @param string $type
     * @throws InvalidTypeException if the type provided doesn't exist
     */
    public function __construct($type)
    {
        if ($type instanceof \ReflectionClass) {
            $type = '\\' . $type->getName();
        }
        if (!in_array($type, self::$_types) && !class_exists($type)
                && !interface_exists($type)) {
            throw new InvalidTypeException('Invalid type: ' . $type);
        }
        $this->_type = ltrim($type, '\\');
        if (class_exists($type, false) || interface_exists($type, false)) {
            $this->_class = self::_getReflectionClass($type);
        }
    }

    /**
     * Tests another value for equality
     *
     * @param mixed $object
     * @return boolean
     */
    public function equals($object)
    {
        return $object === $this ||
        ($object instanceof Type && $object->_type == $this->_type);
    }

    /**
     * If this type is a class this returns the ReflectionClass object for it
     *
     * @return ReflectionClass
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Gets the name of this type
     *
     * @return string
     */
    public function getName()
    {
        return $this->_type;
    }

    /**
     * Gets a hash code for this type
     *
     * @return int
     */
    public function hashCode()
    {
        return self::hash($this->_type);
    }

    /**
     * Determines if the supplied type is a child of the current type
     * 
     * The $type argument can be either a string, a ReflectionClass object or a
     * Xyster_Type object
     *
     * @param mixed $type
     */
    public function isAssignableFrom($type)
    {
        $name = null;
        $class = null;
        if (is_string($type)) {
            $name = ltrim($type, '\\');
            if (class_exists($type, false)) {
                $class = new \ReflectionClass($type);
            }
        } else if ($type instanceof \ReflectionClass) {
            $name = $type->getName();
            $class = $type;
        } else if ($type instanceof Type) {
            $name = $type->getName();
            $class = $type->getClass();
        }

        /* @var $class ReflectionClass */
        return $this->_type == $name ||
        ($this->_type == 'scalar' && in_array($type, self::$_scalarTypes)) ||
        ($class && $this->_class && $class->isSubclassOf($this->_class));
    }

    /**
     * Determines if the value supplied is an instance of this type
     *
     * @param mixed $value
     * @return boolean
     */
    public function isInstance($value)
    {
        return $value !== null && (( $this->_class && is_object($value) &&
            $this->_class->isInstance($value) ) ||
            ( $this->isAssignableFrom(self::of($value)) ));
    }

    /**
     * Gets whether this type is an object type
     *
     * @return boolean
     */
    public function isObject()
    {
        return $this->_class instanceof \ReflectionClass;
    }

    /**
     * Gets the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (( $this->isObject() ) ? 'Class ' : '') . $this->_type;
    }

    /**
     * Checks for shallow equality
     * 
     * If the object you supply for $arg1 has a method named 'equals', that
     * method will be called using $arg2 for the argument.
     * 
     * @param mixed $arg1
     * @param mixed $arg2
     * @return boolean
     */
    public static function areEqual($arg1, $arg2)
    {
        if ($arg1 === $arg2) {
            return true;
        }

        if (is_numeric($arg1) XOR is_numeric($arg2)) {
            return false;
        }
        if (is_array($arg1) XOR is_array($arg2)) {
            return false;
        }
        if (is_object($arg1) XOR is_object($arg2)) {
            return false;
        }
        if (is_object($arg1) && is_object($arg2) &&
                get_class($arg1) !== get_class($arg2)) {
            return false;
        }

        if (is_scalar($arg1) || is_scalar($arg2)) {
            return $arg1 == $arg2;
        }

        if (is_object($arg1) && method_exists($arg1, 'equals')) {
            return $arg1->equals($arg2);
        }

        return false;
    }

    /**
     * Compares two values for equality
     * 
     * If two objects are not identical (that is, are exactly the same instance)
     * they will be compared property-by-property recursively if need be.  This
     * method will not nest any deeper than 10 levels to compare objects.
     * 
     * Arrays will be compared in the same manner.
     * 
     * If the object you supply for $arg1 has a method named 'equals', that
     * method will be called using $arg2 for the argument.
     * 
     * @param mixed $arg1
     * @param mixed $arg2
     * @return boolean
     */
    public static function areDeeplyEqual($arg1, $arg2)
    {
        return self::_areEqual($arg1, $arg2);
    }

    /**
     * Gets the types of the parameters for a function or method
     *
     * @param ReflectionFunctionAbstract $function
     * @return \Xyster\Type\Type[]
     */
    public static function getForParameters(\ReflectionFunctionAbstract $function)
    {
        $types = array();
        foreach ($function->getParameters() as $parameter) {
            /* @var $parameter ReflectionParameter */
            if ($parameter->isArray()) {
                $types[] = new self('array');
            } else if ($parameter->getClass()) {
                $types[] = new self($parameter->getClass());
            } else if ($parameter->isDefaultValueAvailable() && !is_null($parameter->getDefaultValue())) {
                $types[] = self::of($parameter->getDefaultValue());
            } else {
                $types[] = new self('scalar');
            }
        }
        return $types;
    }

    /**
     * Gets the type of the value supplied
     *
     * @param mixed $value
     * @return \Xyster\Type\Type
     */
    public static function of($value)
    {
        $type = is_object($value) ? get_class($value) : gettype($value);
        return in_array($type, self::$_scalarTypes) ?
                self::_staticType($type) : new self($type);
    }

    /**
     * Gets the Java-style hash code of a value
     * 
     * If you supply an object for the argument and it has a method named
     * 'hashCode', that method will be called. 
     *
     * @param mixed $value
     * @return int
     */
    public static function hash($value)
    {
        $hash = 0;

        if (is_string($value)) {

            $max = (float) self::INT_MAX_32;
            $min = (float) (~self::INT_MAX_32 + 1);
            $h = 0.0;
            // mmm... modular arithmetic...
            for ($i = 0; $i < strlen($value); $i++) {
                $result = 31 * $h + ord($value[$i]);
                if ($result > $max) {
                    $h = $result >> 32;
                } else if ($result < $min) {
                    $h = ~(($result * -1) >> 32) + 1;
                } else {
                    $h = $result;
                }
            }
            $hash = (int) $h;
        } else if (is_bool($value)) {
            $hash = $value ? 1231 : 1237;
        } else if (is_float($value)) {
            $res = unpack('l', pack('f', $value));
            $hash = $res[1];
        } else if (is_int($value)) {
            $hash = $value;
        } else if (is_object($value) || is_array($value)) {

            if (is_object($value)) {
                if (method_exists($value, 'hashCode')) {
                    return (int) $value->hashCode();
                }

                $hex = str_split(spl_object_hash($value), 2);
                $value = array_map('hexdec', $hex);
            }

            $max = (float) self::INT_MAX_32;
            $min = (float) (~self::INT_MAX_32 + 1);
            $h = 0.0;
            foreach ($value as $v) {
                $result = 31 * $h + self::hash($v);
                if ($result > $max) {
                    $h = $result >> 32;
                } else if ($result < $min) {
                    $h = ~(($result * -1) >> 32) + 1;
                } else {
                    $h = $result;
                }
            }
            $hash = $h;
        }

        return $hash;
    }

    /**
     * Gets a type for the PHP boolean scalar value
     * 
     * @return Xyster_Type
     */
    public static function boolean()
    {
        return self::_staticType(self::BOOLEAN);
    }

    /**
     * Gets a type for the PHP double scalar value
     * 
     * @return Xyster_Type
     */
    public static function double()
    {
        return self::_staticType(self::DOUBLE);
    }

    /**
     * Gets a type for the PHP integer scalar value
     * 
     * @return Xyster_Type
     */
    public static function integer()
    {
        return self::_staticType(self::INTEGER);
    }

    /**
     * Gets a type for the PHP string scalar value
     * 
     * @return Xyster_Type
     */
    public static function string()
    {
        return self::_staticType(self::STRING);
    }

    private static function _staticType($name)
    {
        if (!array_key_exists($name, self::$_scalarTypeInstances)) {
            self::$_scalarTypeInstances[$name] = new Type($name);
        }
        return self::$_scalarTypeInstances[$name];
    }

    /**
     * Compares two values for equality
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param int $depth  The current search depth
     * @return boolean
     */
    private static function _areEqual($arg1, $arg2, $depth = 0)
    {
        if ($arg1 === $arg2) {
            return true;
        }
        if ($depth >= self::$_nesting) {
            return true;
        }
        if (is_numeric($arg1) XOR is_numeric($arg2)) {
            return false;
        }
        if (is_array($arg1) XOR is_array($arg2)) {
            return false;
        }
        if (is_object($arg1) XOR is_object($arg2)) {
            return false;
        }
        if (is_object($arg1) && is_object($arg2) &&
                get_class($arg1) !== get_class($arg2)) {
            return false;
        }
        if (is_scalar($arg1) || is_scalar($arg2)) {
            return $arg1 == $arg2;
        }
        if (is_object($arg1) && method_exists($arg1, 'equals')) {
            return $arg1->equals($arg2);
        }
        if (is_object($arg1)) {
            $arg1 = (array) $arg1;
            $arg2 = (array) $arg2;
        }
        foreach ($arg1 as $key => $v) {
            if (!array_key_exists($key, $arg2)) {
                return false;
            }
            if (!self::_areEqual($arg1[$key], $arg2[$key], $depth + 1)) {
                return false;
            }
            unset($arg2[$key]);
        }
        if (count($arg2)) {
            return false;
        }
        return true;
    }

    /**
     * Gets the ReflectionClass for a class name and stores it
     *
     * @param string $name
     * @return ReflectionClass
     */
    protected static function _getReflectionClass($name)
    {
        if (!isset(self::$_classes[$name])) {
            self::$_classes[$name] = new \ReflectionClass($name);
        }
        return self::$_classes[$name];
    }
}