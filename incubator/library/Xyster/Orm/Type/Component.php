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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Type_AbstractType
 */
require_once 'Xyster/Orm/Type/AbstractType.php';
/**
 * Type mapping for a component
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Component extends Xyster_Orm_Type_AbstractType
{
    protected $_propertyNames = array();
    
    /**
     * @var Xyster_Orm_Type_IType[]
     */
    protected $_propertyTypes = array();
    
    protected $_propertySpan = 0;
    
    /**
     * @var Xyster_Orm_Tuplizer_IComponent
     */
    protected $_tuplizer;
    
    /**
     * Creates a new component type
     * 
     * @param Xyster_Orm_Meta_Value_Component $component The value
     * @param Xyster_Orm_Tuplizer_IComponent $tuplizer The tuplizer
     */
    public function __construct(Xyster_Orm_Meta_Value_Component $component, Xyster_Orm_Tuplizer_IComponent $tuplizer)
    {
        $this->_propertySpan = $component->getPropertySpan();
        foreach($component->getProperties() as $prop ) {
            $this->_propertyNames[] = $prop->getName();
            $this->_propertyTypes[] = $prop->getType();
        }
        $this->_tuplizer = $tuplizer;
    }
    
    /**
     * Compare two instances of this type.
     * 
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare($a, $b)
    {
        if ( $a === $b ) {
            return 0;
        }
        $avals = $this->_tuplizer->getPropertyValues($a);
        $bvals = $this->_tuplizer->getPropertyValues($b);
        foreach( $this->_propertyTypes as $k=>$type ) {
            /* @var $type Xyster_Orm_Type_IType */
            $cmp = $type->compare($avals[$k], $bvals[$k]);
            if ( $cmp != 0 ) {
                return $cmp;
            }
        }
        return 0;
    }
    
    /**
     * Gets a deep copy of the persistent state; stop on entity and collection.
     *
     * @param mixed $value
     * @return mixed A copy
     */    
    public function copy($value)
    {
        if ( $value === null ) {
            return null;
        }
        
        $values = $this->_tuplizer->getPropertyValues($value);
        foreach( $this->_propertyTypes as $k=>$type ) {
            /* @var $type Xyster_Orm_Type_IType */
            $values[$k] = $type->copy($values[$k]);
        }
        
        $result = $this->_tuplizer->instantiate();
        $this->_tuplizer->setPropertyValues($result, $values);
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
     * Gets an array of fetch types for the columns
     *
     * @return array of int
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
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnType()
    {
        return $this->_tuplizer->getComponentType();
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
     * Whether this type is translated
     *
     * @return boolean
     */
    public function hasTranslate()
    {
        return true;
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
     * @param array $checkable Boolean for each column's updatability
     * @return boolean
     */
    public function isDirty($old, $current, array $checkable = array())
    {
        if ( $old === $current ) {
            return false;
        }
        if ( $old === null || $current === null ) {
            return true;
        }
        $ovalues = $this->_tuplizer->getPropertyValues($old);
        $cvalues = $this->_tuplizer->getPropertyValues($current);
        $loc = 0;
        foreach( $this->_propertyTypes as $k => $type ) {
            /* @var $type Xyster_Orm_Type_IType */
            $len = $type->getColumnSpan();
            $dirty = false;
            if ( $len < 2 ) {
                $dirty = ($len == 0 || $checkable[$loc]) &&
                    $type->isDirty($ovalues[$k], $cvalues[$k]);
            } else {
                $subcheck = array();
                for( $i=0; $i<$len; $i++ ) {
                    $subcheck[$i] = $checkable[$loc + $i];
                }
                $dirty = $type->isDirty($ovalues[$k], $cvalues[$k], $subcheck);
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
        $avals = $this->_tuplizer->getPropertyValues($a);
        $bvals = $this->_tuplizer->getPropertyValues($b);
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
        $avals = $this->_tuplizer->getPropertyValues($a);
        $bvals = $this->_tuplizer->getPropertyValues($b);
        foreach( $this->_propertyTypes as $k => $type ) {
            if ( !$type->isSame($avals[$k], $bvals[$k]) ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Given an instance, return which columns would be null.
     *
     * @param mixed $value
     * @return array
     */
    public function toColumnNullness($value)
    {
        $nulls = array();
        if ( $value !== null ) {
            $values = $this->getPropertyValues($value);
            foreach( $this->_propertyTypes as $k=>$type ) {
                $nulls = array_merge($nulls, $type->toColumnNullness($values[$k]));
            }
        } else {
            $nulls = array_fill(0, $this->getColumnSpan(), false);
        }
        return $nulls;        
    }

    /**
     * Translates the value pulled from the statement into the proper type.
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @return mixed The translated value
     */
    public function translateFrom(array $values, $owner, Xyster_Orm_ISession $sess)
    {
        $result = $this->_tuplizer->instantiate();
        
        $loc = 0;
        $vals = array();
        foreach( $this->_propertyTypes as $k=>$type ) {
            /* @var $type Xyster_Orm_Type_IType */
            $len = $type->getColumnSpan();
            $range = array();
            for( $i=0; $i<$len; $i++ ) {
                $range[$i] = $values[$loc + $i];
            }
            $val = $type->hasTranslate() ?
                $type->get($range, $owner, $sess) : $range;
            if ( is_array($val) && $len == 1 ) {
                $val = $val[0];
            }
            $vals[$k] = $val;
            $loc += $len; 
        }
        
        $this->_tuplizer->setPropertyValues($result, $vals);
        
        return $result;
    }
    
    /**
     * Translates the object type back what is stored in the database
     * 
     * @param mixed $value The value available on the entity
     * @param mixed $owner The owning entity
     * @param Xyster_Orm_ISession $sess
     * @return array The translated value or values
     */
    public function translateTo($value, $owner, Xyster_Orm_ISession $sess)
    {
        $subvalues = array_fill(0, $this->_propertySpan, null);
        if ( $value !== null ) {
            $subvalues = $this->_tuplizer->getPropertyValues($value);
        }
        $translated = array();
        $loc = 0;
        foreach( $this->_propertyTypes as $k => $type ) {
            /* @var $type Xyster_Orm_Type_IType */
            $len = $type->getColumnSpan();
            if ( $len == 1 ) {
                $translated[] = $type->hasTranslate() ?
                        current($type->translateTo($subvalues[$k], $owner, $sess)) :
                        $subvalues[$k];
            } else if ( $len > 1 ) {
                $translated = array_merge($translated,
                    $type->translateTo($subvalues[$k], $owner, $sess));
            }
            $loc += $len;
        }
        return $translated;
    }
}