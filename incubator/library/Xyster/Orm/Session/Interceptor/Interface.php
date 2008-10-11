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
 * Allows the viewing and changing of property values
 *
 * It's probably a better idea to extend
 * {@link Xyster_Orm_Session_Interceptor_Empty} and only override the desired
 * methods than to implement this interface directly.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Session_Interceptor_Interface
{
    /**
     * Called during the flush sequence.
     * 
     * If the method returns an array of property indexes, the entity is dirty.
     * If the method returns an empty array, the entity is clean.  If the method
     * returns null, default dirty checking will be used.
     * 
     * @param object $entity The entity
     * @param mixed $id The entity identifier
     * @param array $values The current entity values
     * @param array $previous The previous entity values
     * @param array $propertyNames The names of the entity properties
     * @param array $types An array of {@link Xyster_Orm_Type_Interface} objects
     * @return array
     */
    function findDirty($entity, $id, array $state, array $previous, array $propertyNames, array $types);
    
    /**
     * Gets a fully loaded entity instance that is cached elsewhere
     * 
     * @param string $entityName
     * @param mixed $id
     * @return object
     */
    function getEntity($entityName, $id);
    
    /**
     * Gets the entity name for a persistent or transient instance
     * 
     * @param object $object
     * @return string The entity name
     */
    function getEntityName($object);
    
    /**
     * Instantiates the entity class
     * 
     * Return null to indicate that default instantiation should be used. The
     * identifier property of the returned instance should be initialized with
     * the given identifier.
     * 
     * @param string $entityName
     * @param mixed $id
     * @return object An instance of the class or null
     */
    function instantiate($entityName, $id);
    
    /**
     * Whether an entity is transient as opposed to detached
     * 
     * If the method returns null, default behavior will occur to determine if 
     * the entity is unsaved.
     * 
     * @param object $entity
     * @return boolean if the entity is transient
     */
    function isTransient($entity);
    
    /**
     * Called before a collection is (re)created
     * 
     * @param object $collection
     * @param mixed $key
     */
    function onCollectionRecreate($collection, $key);
    
    /**
     * Called before a collection is deleted
     * 
     * @param object $collection
     * @param mixed $key
     */
    function onCollectionRemove($collection, $key);
    
    /**
     * Called before a collection is updated
     * 
     * @param object $collection
     * @param mixed $key
     */
    function onCollectionUpdate($collection, $key);
    
    /**
     * Called before an object is deleted
     * 
     * @param object $entity The entity
     * @param mixed $id The entity's identifier
     * @param array $values The new entity values
     * @param array $propertyNames The names of the entity properties
     * @param array $types An array of {@link Xyster_Orm_Type_Interface} objects
     */
    function onDelete($entity, $id, array $values, array $propertyNames, array $types);
    
    /**
     * Called when an object is dirty during the flush sequence
     * 
     * The values may be changed by the interceptor so they will be passed to 
     * both the object and the database.  Note that not all flushes end in
     * synchronization with the database.
     * 
     * @param object $entity The entity
     * @param mixed $id The entity's identifier
     * @param array $values The current entity values
     * @param array $previous The previous entity values
     * @param array $propertyNames The names of the entity properties
     * @param array $types An array of {@link Xyster_Orm_Type_Interface} objects
     * @return boolean Return true if the values were modified at all
     */
    function onFlushDirty($entity, $id, array &$values, array $previous, array $propertyNames, array $types);
    
    /**
     * Called before an object is initialized
     * 
     * The values may be changed by the interceptor so they will be passed to
     * the object. When this method is called, entity will be an empty instance
     * of the class.
     * 
     * @param object $entity The entity
     * @param mixed $id The entity's identifier
     * @param array $values The new entity values
     * @param array $propertyNames The names of the entity properties
     * @param array $types An array of {@link Xyster_Orm_Type_Interface} objects
     * @return boolean Return true if the values were modified at all
     */
    function onLoad($entity, $id, array &$values, array $propertyNames, array $types);
    
    /**
     * Called when a SQL statement is being prepared
     * 
     * @param string $sql
     * @return string The original or modified sql
     */
    function onPrepareStatement($sql);
    
    /**
     * Called before an object is saved
     * 
     * The values may be changed by the interceptor so they will be passed to
     * the database and the object.
     *   
     * @param object $entity The entity
     * @param mixed $id The entity's identifier
     * @param array $values The new entity values
     * @param array $propertyNames The names of the entity properties
     * @param array $types An array of {@link Xyster_Orm_Type_Interface} objects
     * @return boolean Return true if the values were modified at all
     */
    function onSave($entity, $id, array &$values, array $propertyNames, array $types);
    
    /**
     * Called after a flush that ends in database synchronization
     * 
     * @param Iterator $entities
     */
    function postFlush(Iterator $entities);
    
    /**
     * Called before a flush
     * @param Iterator $entities
     */
    function preFlush(Iterator $entities);
}