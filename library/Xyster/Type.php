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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Type wrapper class 
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Type
{
    private static $_nesting = 10;
    
    private static $_types = array('array', 'scalar', 'boolean', 'integer',
        'double', 'string');
    
    private static $_scalarTypes = array('boolean', 'integer', 'double',
        'string');
    
    private static $_classes = array();
    
    protected $_type;
    
    /**
     * @var ReflectionClass
     */
    protected $_class;
    
    /**
     * Creates a new type representation
     *
     * @param string $type
     */
    public function __construct( $type )
    {
        if ( $type instanceof ReflectionClass ) {
            $type = $type->getName();
        }
        if ( !in_array($type, self::$_types) && !class_exists($type, false) && !interface_exists($type, false) ) {
            require_once 'Zend/Exception.php';
            throw new Zend_Exception('Invalid type: ' . $type);
        }
        $this->_type = $type;
        if ( class_exists($type, false) || interface_exists($type, false) ) {
            $this->_class = self::_getReflectionClass($type);
        }
    }
    
    /**
     * Tests another value for equality
     *
     * @param mixed $object
     * @return boolean
     */
    public function equals( $object )
    {
        return $object === $this ||
            ($object instanceof Xyster_Type && $object->_type == $this->_type);
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
    public function isAssignableFrom( $type )
    {
        $name = null;
        $class = null;
        if ( is_string($type) ) {
            $name = $type;
            if ( class_exists($type, false) ) {
                $class = new ReflectionClass($type);
            }
        } else if ( $type instanceof ReflectionClass ) {
            $name = $type->getName();
            $class = $type;
        } else if ( $type instanceof Xyster_Type ) {
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
    public function isInstance( $value )
    {
    	return ( $this->_class && $this->_class->isInstance($value) ) || 
    	   ( $this->isAssignableFrom(self::of($value)) ); 
    }
    
    /**
     * Gets whether this type is an object type
     *
     * @return boolean
     */
    public function isObject()
    {
        return $this->_class instanceof ReflectionClass;
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
    public static function areEqual( $arg1, $arg2 )
    {
        if ( $arg1 === $arg2 ) {
            return true;
        }
        
        if ( is_numeric($arg1) XOR is_numeric($arg2) ) {
            return false;
        }
        if ( is_array($arg1) XOR is_array($arg2) ) {
            return false;
        }
        if ( is_object($arg1) XOR is_object($arg2) ) {
            return false;
        }
        if ( is_object($arg1) && is_object($arg2) &&
            get_class($arg1) !== get_class($arg2) ) {
            return false;
        }

        if ( is_scalar($arg1) || is_scalar($arg2) ) {
            return $arg1 == $arg2;
        }

        if ( is_object($arg1) && method_exists($arg1, 'equals') ) {
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
    public static function areDeeplyEqual( $arg1, $arg2 )
    {
        return self::_areEqual($arg1, $arg2);
    }
    
    /**
     * Gets the types of the parameters for a function or method
     *
     * @param ReflectionFunctionAbstract $function
     * @return array
     */
    public static function getForParameters( ReflectionFunctionAbstract $function )
    {
    	$types = array();
    	foreach( $function->getParameters() as $parameter ) {
    		/* @var $parameter ReflectionParameter */
    		if ( $parameter->isArray() ) {
    			$types[] = new self('array');
    		} else if ( $parameter->getClass() ) {
    			$types[] = new self($parameter->getClass());
    		} else if ( $parameter->isDefaultValueAvailable() ) {
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
     * @return Xyster_Type
     */
    public static function of( $value )
    {
        return new self(is_object($value) ? get_class($value) : gettype($value));
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
    public static function hash( $value )
    {
        $hash = 0;
        
        if ( is_object($value) ) {
            $hash = self::_objHash($value);
        } else if ( is_string($value) ) {
            $hash = self::_strHash($value);
        } else if ( is_bool($value) ) {
            $hash = $value ? 1231 : 1237;
        } else if ( is_float($value) ) {
            $res = unpack('l', pack('f', $value));
            $hash = $res[1];
        } else if ( is_int($value) ) {
            $hash = $value;
        } else if ( is_array($value) ) {
            $max = (float)PHP_INT_MAX;
            $min = (float)(0 - PHP_INT_MAX);
            $h = 0.0;
            foreach( $value as $v ) {
                $result = 31 * $h + self::hash($v);
                if ( $result > $max ) {
                    $h = $result % $max;
                } else if ( $result < $min ) {
                    $h = 0-(abs($result) % $max);
                } else {
                    $h = $result;
                }
            }
            $hash = $h;
        }
        
        return $hash;
    }
    
    /**
     * Compares two values for equality
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param int $depth  The current search depth
     * @return boolean
     */
    private static function _areEqual( $arg1, $arg2, $depth = 0 )
    {
        if ( $arg1 === $arg2 ) {
            return true;
        }
        
        if ( $depth >= self::$_nesting ) {
            return true;
        }
        
        if ( is_numeric($arg1) XOR is_numeric($arg2) ) {
            return false;
        }
        if ( is_array($arg1) XOR is_array($arg2) ) {
            return false;
        }
        if ( is_object($arg1) XOR is_object($arg2) ) {
            return false;
        }
        if ( is_object($arg1) && is_object($arg2) &&
            get_class($arg1) !== get_class($arg2) ) {
            return false;
        }

        if ( is_scalar($arg1) || is_scalar($arg2) ) {
            return $arg1 == $arg2;
        }

        if ( is_object($arg1) && method_exists($arg1, 'equals') ) {
            return $arg1->equals($arg2);
        }

        if ( is_object($arg1) ) { 
            $arg1 = (array) $arg1;
            $arg2 = (array) $arg2;
        }

        foreach ( $arg1 as $key => $v ) {
            if (!array_key_exists($key, $arg2)) {
                return false;
            }

            if ( !self::_areEqual($arg1[$key], $arg2[$key], $depth + 1) ) {
                return false;
            }

            unset($arg2[$key]);
        }

        if ( count($arg2) ) {
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
    protected static function _getReflectionClass( $name )
    {
        if ( !isset(self::$_classes[$name]) ) {
            self::$_classes[$name] = new ReflectionClass($name); 
        }
        return self::$_classes[$name];
    }
    
    /**
     * Gets the hash code for an object
     *
     * @param object $object
     * @return int
     */
    private static function _objHash( $object )
    {
        if ( method_exists($object, 'hashCode') ) {
            return (int)$object->hashCode();
        }
        
        $hex = str_split(spl_object_hash($object), 2);
        
        return self::hash(array_map('hexdec', $hex));
    }
    
    /**
     * Gets the hash code for a string
     *
     * @param string $string
     * @return int
     */
    private static function _strHash( $string )
    {
        $max = (float)PHP_INT_MAX;
        $min = (float)(0 - PHP_INT_MAX);
        $h = 0.0;
        // mmm... modular arithmetic...
        for( $i=0; $i<strlen($string); $i++ ) {
            $result = 31 * $h + ord($string[$i]);
            if ( $result > $max ) {
                $h = $result % $max;
            } else if ( $result < $min ) {
                $h = 0-(abs($result) % $max);
            } else {
                $h = $result;
            }
        }
        return (int)$h;
    }
}