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
     * Called before the unpack to allow batch fetching of uncached entities
     *
     * @param unknown_type $cached
     * @param Xyster_Orm_Session_Interface $sess
     */
    function beforeUnpack( $cached, Xyster_Orm_Session_Interface $sess );
    
    /**
     * Return a cacheable copy of the object
     *
     * @param mixed $value
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $owner
     * @return mixed Disassembled, deep copy
     */
    function cachePack( $value, Xyster_Orm_Session_Interface $sess, $owner );
    
    /**
     * Reconstruct the object from its cached copy
     *
     * @param mixed $cached
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $owner
     * @return mixed The reconstructed value
     */
    function cacheUnpack( $cached, Xyster_Orm_Session_Interface $sess, $owner );
    
    /**
     * Compare two instances of this type
     *
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    function compare( $a, $b );
    
    /**
     * Gets a deep copy of the persistent state; stop on entity and collection
     *
     * @param mixed $value
     * @return mixed A copy
     */
    function deepCopy( $value );
    
    /**
     * Resolves the value pulled from the statement into the proper type
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     */
    function get( array $values, $owner, Xyster_Orm_Session_Interface $sess );
    
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
     * Gets the fetch types for binding
     * 
     * See the Zend_Db::PARAM_* constants.
     *
     * @return array
     */
    function getFetchTypes();
        
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
     * Whether this type needs to have {@link get}() called
     *
     * @return boolean
     */
    function hasResolve();
    
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