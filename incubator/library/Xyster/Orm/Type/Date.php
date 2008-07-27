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
 * @see Xyster_Orm_Type_Mutable
 */
require_once 'Xyster/Orm/Type/Mutable.php';
/**
 * Zend_Date
 */
require_once 'Zend/Date.php';
/**
 * Maps a SQL DATE to a Zend_Date
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Date extends Xyster_Orm_Type_Mutable
{
    /**
     * @var Xyster_Type
     */
    static protected $_type;
    
    /**
     * Gets the type out of a result set statement
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     */
    public function get(array $values, $owner, Xyster_Orm_Session_Interface $sess )
    {
        return new Zend_Date($values[0]);
    }
        
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Date();
    }
    
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'date';
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
     * Whether this type needs to have {@link get}() called
     *
     * @return boolean
     */
    public function hasResolve()
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
    public function isEqual( $a, $b )
    {
        if (! $a instanceof Zend_Date ) {
            /* @var $a Zend_Date */
            $a = new Zend_Date($a);
        }
        return $a->equals($b, Zend_Date::DATES);
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
        if (! $value instanceof Zend_Date ) {
            $value = new Zend_Date($value);
        }
        if ( !count($settable) || $settable[0] ) {
            $stmt->bindValue($index, $value->get(Zend_Date::DATES));
        }
    }   
}