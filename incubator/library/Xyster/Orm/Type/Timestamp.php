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
 * @see Xyster_Orm_Type_IVersion
 */
require_once 'Xyster/Orm/Type/IVersion.php';
/**
 * @see Xyster_Orm_Type_Mutable
 */
require_once 'Xyster/Orm/Type/Mutable.php';
/**
 * Zend_Date
 */
require_once 'Zend/Date.php';
/**
 * Maps a SQL TIMESTAMP to a Zend_Date
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Timestamp extends Xyster_Orm_Type_Mutable implements Xyster_Orm_Type_IVersion 
{
    /**
     * @var Xyster_Type
     */
    static protected $_type;

    /**
     * Compare two instances of this type
     * 
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare( $a, $b )
    {
        return $a->compare($b);
    }
    
    /**
     * Gets a deep copy of the persistent state; stop on entity and collection
     *
     * @param mixed $value
     * @return mixed A copy
     */
    public function copy( $value )
    {
        return new Zend_Date($value);
    }
    
    /**
     * Gets a comparator for version values
     *
     * @return Xyster_Collection_Comparator_Interface
     */
    public function getComparator()
    {
        return $this;
    }
            
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Timestamp();
    }
    
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'timestamp';
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        if ( !self::$_type ) {
            self::$_type = new Xyster_Type('Zend_Date');
        }
        return self::$_type;
    }
    
    /**
     * Whether this type has a translation process
     *
     * @return boolean
     */
    public function hasTranslate()
    {
        return true;
    }

    /**
     * Gets the initial version id
     *
     * @return mixed The initial version
     */
    public function initial()
    {
        return new Zend_Date();
    }
        
    /**
     * Compares the values supplied for persistence equality
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function isEqual( $a, $b )
    {
        if (! $a instanceof Zend_Date ) {
            /* @var $a Zend_Date */
            $a = new Zend_Date($a);
        }
        return $a->equals($b);
    }
    
    /**
     * Gets the next version id
     *
     * @param mixed $current
     * @return mixed The next version
     */
    public function next( $current )
    {
        return $this->initial();
    }
    
    /**
     * Gets the type out of a result set statement
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_ISession $sess The ORM session
     */
    public function translateFrom(array $values, $owner, Xyster_Orm_ISession $sess)
    {
        return new Zend_Date($values[0]);
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
        if (! $value instanceof Zend_Date ) {
            $value = new Zend_Date($value);
        }
        return array($value->get(Zend_Date::ISO_8601));
    }   
}