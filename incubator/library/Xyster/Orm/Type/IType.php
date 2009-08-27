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
 * @see Xyster_Collection_Comparator_Interface
 */
require_once 'Xyster/Collection/Comparator/Interface.php';
/**
 * A mapping between a database type and an internal PHP type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Type_IType extends Xyster_Collection_Comparator_Interface
{
    /**
     * Gets a deep copy of the persistent state; stop on entity and collection.
     *
     * @param mixed $value
     * @return mixed A copy
     */
    function copy($value);
    
    /**
     * Gets how many columns are used to persist this type
     *
     * @return int
     */
    function getColumnSpan();

    /**
     * Gets an array of Xyster_Db_DataType objects for the columns in this type.
     *
     * @return array of {@link Xyster_Db_DataType} objects
     */
    function getDataTypes();

    /**
     * Gets the fetch types for binding.
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
     * Whether this type has a translation process.
     * 
     * For instance, turning a string into a Zend_Date object.
     *
     * @return boolean
     */
    function hasTranslate();
    
    /**
     * Whether this type is an association
     * 
     * @return boolean
     */
    function isAssociation();
    
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
    function isComponent();
    
    /**
     * Tests whether an object is dirty
     *
     * @param mixed $old The old value
     * @param mixed $current The current value
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $checkable Boolean for each column's updatability
     */
    function isDirty($old, $current, array $checkable = array());
    
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
     * Compares the values supplied for persistence equality.
     * 
     * Types that compare actual objects should compare identity (===).
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    function isSame($a, $b);
    
    /**
     * Given an instance, return which columns would be null.
     * 
     * If a value should be null, the array will contain false.
     *
     * @param mixed $value
     * @return array
     */
    function toColumnNullness($value);
    
    /**
     * Translates the value pulled from the statement into the proper type.
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @return mixed The translated value
     */
    function translateFrom(array $values, $owner, Xyster_Orm_ISession $sess);
    
    /**
     * Translates the object type back what is stored in the database
     * 
     * @param mixed $value The value available on the entity
     * @param mixed $owner The owning entity
     * @param Xyster_Orm_ISession $sess
     * @return array The translated value or values
     */
    function translateTo($value, $owner, Xyster_Orm_ISession $sess);
}