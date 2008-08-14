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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Type_Abstract
 */
require_once 'Xyster/Orm/Type/Abstract.php';
/**
 * Type for Components
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Component extends Xyster_Orm_Type_Abstract
{
    protected $_isKey = false;
    
    protected $_propertyNames = array();
    
    /**
     * @var Xyster_Orm_Type_Interface[]
     */
    protected $_propertyTypes = array();
    
    protected $_propertyNullability = array();
    
    protected $_propertySpan = 0;
    
    /**
     * @var Xyster_Orm_Tuplizer_Component_Interface
     */
    protected $_tuplizer;
    
    /**
     * Creates a new component type
     *
     * @param Xyster_Orm_Runtime_ComponentMeta $cm
     */
    public function __construct( Xyster_Orm_Runtime_ComponentMeta $cm )
    {
        $this->_isKey = $cm->isKey();
        $this->_propertySpan = $cm->getPropertySpan();
        foreach( $cm->getProperties() as $k=>$prop ) {
            /* @var $prop Xyster_Orm_Runtime_Property_Standard */
            $this->_propertyNames[$k] = $prop->getName();
            $this->_propertyTypes[$k] = $prop->getType();
            $this->_propertyNullability[$k] = $prop->isNullable();
        }
        $this->_tuplizer = $cm->getTuplizer();
    }
    
    /**
     * Return a cacheable copy of the object
     *
     * @param mixed $value
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $owner
     * @return mixed Disassembled, deep copy
     */
    public function cachePack( $value, Xyster_Orm_Session_Interface $sess, $owner )
    {
        $values = null;
        if ( $value !== null ) {
            $values = $this->getPropertyValues($value);
            foreach( $this->_propertyTypes as $k=>$type ) {
                /* @var $type Xyster_Orm_Type_Interface */
                $values[$k] = $type->cachePack($values[$k], $sess, $owner);
            }
        }
        return $values;
    }
    
    /**
     * Reconstruct the object from its cached copy
     *
     * @param mixed $cached
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $owner
     * @return mixed The reconstructed value
     */
    public function cacheUnpack( $cached, Xyster_Orm_Session_Interface $sess, $owner )
    {
        $result = null;
        if ( $cached !== null && is_array($cached) ) {
            $assembled = array();
            foreach( $this->_propertyTypes as $k=>$type ) {
                /* @var $type Xyster_Orm_Type_Interface */
                $assembled[$k] = $type->cacheUnpack($cached[$i], $sess, $owner);
            }
            $result = $this->instantiate($owner, $sess);
            $this->setPropertyValues($result, $assembled);
        }
        return $result;
    }
    
    /**
     * Compare two instances of this type
     *
     * One probably wants to overwrite this method
     * 
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare( $a, $b )
    {
        if ( $a === $b ) {
            return 0;
        }
        $avals = $this->getPropertyValues($a);
        $bvals = $this->getPropertyValues($b);
        foreach( $this->_propertyTypes as $k=>$type ) {
            /* @var $type Xyster_Orm_Type_Interface */
            $cmp = $type->compare($avals[$k], $bvals[$k]);
            if ( $cmp != 0 ) {
                return $cmp;
            }
        }
        return 0;
    }
    
    /**
     * Gets a deep copy of the persistent state; stop on entity and collection
     *
     * @param mixed $value
     * @return mixed A copy
     */
    public function deepCopy($value)
    {
        if ( $value === null ) {
            return null;
        }
        
        $values = $this->getPropertyValues($value);
        foreach( $this->_propertyTypes as $k=>$type ) {
            /* @var $type Xyster_Orm_Type_Interface */
            $values[$k] = $type->deepCopy($values[$k]);
        }
        
        $result = $this->instantiateNoParent();
        $this->setPropertyValues($result, $values);
        
        $tuplizer = $this->_tuplizer;
        if ( $tuplizer->hasParentProperty() ) {
            $tuplizer->setParent($result, $tuplizer->getParent($value));
        }
        
        return $result;
    }
    
    /**
     * Gets the type out of a result set statement
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @return object
     */
    public function get(array $values, $owner, Xyster_Orm_Session_Interface $sess )
    {
        $result = $this->instantiate($owner, $sess);
        
        $loc = 0;
        $vals = array();
        foreach( $this->_propertyTypes as $k=>$type ) {
            /* @var $type Xyster_Orm_Type_Interface */
            $len = $type->getColumnSpan();
            $range = array();
            for( $i=0; $i<$len; $i++ ) {
                $range[$i] = $values[$loc+$i];
            }
            $val = $type->hasResolve() ?
                $type->get($range, $owner, $sess) : $range;
            if ( $len == 1 ) { 
                $val = $val[0];
            }
            $vals[$k] = $val;
            $loc += $len; 
        }
        
        $this->setPropertyValues($result, $vals);
        
        return $result;
    }
    
    /**
     * Gets how many columns are used to persist this type
     *
     * @return int
     */
    public function getColumnSpan()
    {
        $cols = 0;
        foreach( $this->_propertyTypes as $type ) {
            $cols += $type->getColumnSpan();
        }
        return $cols;
    }

    /**
     * Gets an array of Xyster_Db_DataType objects for the columns in this type 
     *
     * @return array of {@link Xyster_Db_DataType} objects
     */
    public function getDataTypes()
    {
        $types = array();
        foreach( $this->_propertyTypes as $type ) {
            foreach( $type->getDataTypes() as $dt ) {
                $types[] = $dt;
            }
        }
        return $types;
    }

    /**
     * Gets the fetch type for binding
     * 
     * See the Zend_Db::PARAM_* constants.
     *
     * @return int
     */
    public function getFetchTypes()
    {
        $types = array();
        foreach( $this->_propertyTypes as $type ) {
            foreach( $type->getFetchTypes() as $ft ) {
                $types[] = $ft;
            }
        }
        return $types;
    }
        
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'component[' . implode(',', $this->_propertyNames) . ']';
    }
    
    /**
     * Gets the property names
     *
     * @return array
     */
    public function getPropertyNames()
    {
        return $this->_propertyNames;
    }
    
    /**
     * Gets the property nullability
     *
     * @return array
     */
    public function getPropertyNullability()
    {
        return $this->_propertyNullability;
    }
    
    /**
     * Gets the value of the specified property
     *
     * @param mixed $component
     * @param int $i
     * @return mixed
     */
    public function getPropertyValue( $component, $i )
    {
        return $this->_tuplizer->getPropertyValue($component, $i);
    }
    
    /**
     * Gets the values of all properties 
     *
     * @param mixed $component
     * @return array
     */
    public function getPropertyValues( $component )
    {
        return $this->_tuplizer->getPropertyValues($component);
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        return $this->_tuplizer->getMappedType();
    }
    
    /**
     * Gets the types of the underlying properties
     *
     * @return array
     */
    public function getTypes()
    {
        return array() + $this->_propertyTypes;
    }
    
    /**
     * Whether this type needs to have {@link get}() called
     *
     * @return boolean
     */
    public function hasResolve()
    {
        return true;
    }
    
    /**
     * Creates a new component instance
     *
     * @param mixed $parent
     * @param Xyster_Orm_Session_Interface $sess
     */
    public function instantiate( $parent, Xyster_Orm_Session_Interface $sess )
    {
        $tuplizer = $this->_tuplizer;
        $result = $tuplizer->instantiate();
        if ( $tuplizer->hasParentProperty() ) {
            /* 
             * @todo once we get proxies and the session/session factory
             * $tuplizer->setParent($result,
                $sess->getPersistenceContext()->proxyFor($parent));
             */
        }
        return $result;
    }
    
    /**
     * Instantiates a new, empty component with no parent
     *
     * @return object
     */
    public function instantiateNoParent()
    {
        return $this->_tuplizer->instantiate();
    }
    
    /**
     * Whether this type is a component
     *
     * @return boolean
     */
    public function isComponentType()
    {
        return true;
    }
    
    /**
     * Tests whether an object is dirty
     *
     * @param mixed $old The old value
     * @param mixed $current The current value
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $checkable Boolean for each column's updatability
     */
    public function isDirty( $old, $current, Xyster_Orm_Session_Interface $sess, array $checkable = array() )
    {
        if ( $old === $current ) {
            return false;
        }
        if ( $old === null || $current === null ) {
            return true;
        }
        $ovalues = $this->getPropertyValues($old);
        $cvalues = $this->getPropertyValues($current);
        $loc = 0;
        foreach( $this->_propertyTypes as $k => $type ) {
            $len = $type->getColumnSpan();
            $dirty = false;
            if ( $len < 2 ) {
                $dirty = ($len == 0 || $checkable[$loc]) &&
                    $type->isDirty($ovalues[$k], $cvalues[$k], $sess);
            } else {
                $subcheck = array();
                for( $i=0; $i<$len; $i++ ) {
                    $subcheck[$i] = $checkable[$loc+$i];
                }
                $dirty = $type->isDirty($ovalues[$k], $cvalues[$k], $sess, $subcheck);
            }
            if ( $dirty ) {
                return true; 
            }
            $loc += $len;
        }
        return false;
    }
    
    /**
     * Compares the values supplied for persistence equality
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function isEqual($a, $b)
    {
        if ( $a === $b ) {
            return true;
        }
        if ( $a === null || $b === null ) {
            return false;
        }
        $avals = $this->getPropertyValues($a);
        $bvals = $this->getPropertyValues($b);
        foreach( $this->_propertyTypes as $k => $type ) {
            if ( !$type->isEqual($avals[$k], $bvals[$k]) ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Whether this type can be altered 
     *
     * @return boolean
     */
    public function isMutable()
    {
        return true;
    }

    /**
     * Compares the values supplied for persistence equality
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function isSame($a, $b)
    {
        if ( $a === $b ) {
            return true;
        }
        if ( $a === null || $b === null ) {
            return false;
        }
        $avals = $this->getPropertyValues($a);
        $bvals = $this->getPropertyValues($b);
        foreach( $this->_propertyTypes as $k => $type ) {
            if ( !$type->isSame($avals[$k], $bvals[$k]) ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Sets the value to the prepared statement
     *
     * @param Zend_Db_Statement_Interface $stmt The statment to set
     * @param mixed $value The value to bind into the statement
     * @param int $index The starting index to bind
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $settable Boolean for each column's settability
     */
    public function set(Zend_Db_Statement_Interface $stmt, $value, $index, Xyster_Orm_Session_Interface $sess, array $settable = array() )
    {
        $subvalues = array_fill(0, $this->_propertySpan, null);
        if ( $value !== null ) {
            $subvalues = $this->getPropertyValues($value);
        }
        
        $loc = 0;
        foreach( $this->_propertyTypes as $k => $type ) {
            $len = $type->getColumnSpan();
            if ( $len == 1 ) {
                if ( $settable[$loc] ) {
                    $type->set($stmt, $subvalues[$k], $index, $sess);
                    ++$index;
                }
            } else if ( $len > 1 ) {
                $subsettable = array();
                for( $i=0; $i<$len; $i++ ) {
                    $subsettable[$i] = $settable[$loc+$i];
                }
                $type->set($stmt, $subvalues[$k], $index, $sess, $subsettable);
                $index += array_sum($subsettable); // yay, PHP!
            }
            $loc += $len;
        }
    }
    
    /**
     * Sets the property values 
     *
     * @param mixed $component
     * @param array $values
     */
    public function setPropertyValues( $component, array $values )
    {
        return $this->_tuplizer->setPropertyValues($component, $values);
    }
    
    /**
     * Given an instance, return which columns would be null
     * 
     * If a value should be null, the array will contain false.
     *
     * @param mixed $value
     * @return array
     */
    public function toColumnNullness( $value )
    {
        $nulls = array_fill(0, $this->getColumnSpan(), false);
        if ( $value !== null ) {
            $values = $this->getPropertyValues($value);
            foreach( $this->_propertyTypes as $k=>$type ) {
                $nulls = array_merge($nulls, $type->toColumnNullness($values[$k]));
            }
        }
        return $nulls;
    }
}