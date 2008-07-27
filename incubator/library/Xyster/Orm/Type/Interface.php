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
 * @see Xyster_Orm_Session_Interface
 */
require_once 'Xyster/Orm/Session/Interface.php';
/**
 * Zend_Db_Statement_Interface
 */
require_once 'Zend/Db/Statement/Interface.php';
/**
 * A mapping between a database type and an internal PHP type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Type_Interface
{
    /**
     * Gets the type out of a result set statement
     *
     * @param Zend_Db_Statement_Interface $rs The statement used to fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $names The column names
     */
    function get(Zend_Db_Statement_Interface $rs, array $names, $owner, Xyster_Orm_Session_Interface $sess );
    
    /**
     * Gets how many columns are used to persist this type
     *
     * @return int
     */
    function getColumnSpan();

    /**
     * Gets an array of Xyster_Db_DataType objects for the columns in this type 
     *
     * @return array of {@link Xyster_Db_DataType} objects
     */
    function getDataTypes();
    
    /**
     * Returns the type name
     *
     * @return string
     */
    function getName();
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    function getReturnedType();
    
    /**
     * Whether this type is a collection
     *
     * @return boolean
     */
    function isCollection();
    
    /**
     * Whether this type is a component
     *
     * @return boolean
     */
    function isComponentType();
    
    /**
     * Tests whether an object is dirty
     *
     * @param mixed $old The old value
     * @param mixed $current The current value
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $checkable Boolean for each column's updatability
     */
    function isDirty( $old, $current, Xyster_Orm_Session_Interface $sess, array $checkable = array() );
    
    /**
     * Whether this type is an entity
     *
     * @return boolean
     */
    function isEntityType();
    
    /**
     * Compares the values supplied for persistence equality
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    function isEqual($a, $b);
    
    /**
     * Whether this type can be altered 
     *
     * @return boolean
     */
    function isMutable();

    /**
     * Compares the values supplied for persistence equality
     * 
     * Types that compare actual objects should compare identity (===).
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    function isSame($a, $b);
    
    /**
     * Sets the value to the prepared statement
     * 
     * A multi-column type will write parameters starting from the index.
     *
     * @param Zend_Db_Statement_Interface $stmt The statment to set
     * @param mixed $value The value to bind into the statement
     * @param int $index The starting index to bind
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $settable Boolean for each column's settability
     */
    function set(Zend_Db_Statement_Interface $stmt, $value, $index, Xyster_Orm_Session_Interface $sess, array $settable = array() );
    
    /**
     * Given an instance, return which columns would be null
     * 
     * If a value should be null, the array will contain false.
     *
     * @param mixed $value
     * @return array
     */
    function toColumnNullness( $value );
}