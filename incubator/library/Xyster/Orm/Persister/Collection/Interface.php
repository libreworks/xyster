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
 * Persists a collection role
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_Collection_Interface
{
    /**
     * Deletes the state of any elements that were removed
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $key
     * @param Xyster_Orm_Session_Interface $session
     */
    function deleteRows(Xyster_Orm_Collection_Interface $collection, $key, Xyster_Orm_Session_Interface $session);
    
    /**
     * Gets whether an element exists
     * @param mixed $key
     * @param object $element
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    function elementExists($key, $element, Xyster_Orm_Session_Interface $session);
    
    /**
     * Gets the meta information
     * 
     * @return Xyster_Orm_Meta_Collection
     */
    function getCollectionMeta();
    
    /**
     * Gets the space that holds the collection
     * 
     * @return array
     */
    function getCollectionSpace();

    /**
     * Gets the element by index
     * @param mixed $key
     * @param object $index
     * @param object $owner
     * @param Xyster_Orm_Session_Interface $session
     * @return object
     */
    function getElementByIndex($key, $index, $owner, Xyster_Orm_Session_Interface $session);
    
    /**
     * Gets the element column aliases based on the provided suffix
     * 
     * @param string $suffix
     * @return array
     */
    function getElementColumnAliases($suffix);
    
    /**
     * Gets the type of the elements in the collection
     * 
     * @return Xyster_Orm_Type_Interface
     */
    function getElementType();
    
    /**
     * Gets the session factory
     * 
     * @return Xyster_Orm_Session_Factory_Interface
     */
    function getFactory();
    
    /**
     * Gets the identifier column alias based on the provided suffix
     * 
     * @param string $suffix
     * @return string
     */
    function getIdColumnAlias($suffix);
    
    /**
     * Gets the key generation strategy
     * 
     * @return Xyster_Orm_Id_Generator_Interface
     */
    function getIdGenerator();
    
    /**
     * Gets the type of key
     * 
     * @return Xyster_Orm_Type_Interface
     */
    function getIdType();
    
    /**
     * Gets the index column aliases based on the provided suffix
     * 
     * @param string $suffix
     * @return array
     */
    function getIndexColumnAliases($suffix);
    
    /**
     * Gets the index type for a list or map
     * 
     * @return Xyster_Orm_Type_Interface
     */
    function getIndexType();
    
    /**
     * Gets the key column aliases based on the provided suffix
     * 
     * @param string $suffix
     * @return array
     */
    function getKeyColumnAliases($suffix);
    
    /**
     * Gets the type of the foreign key
     * 
     * @return Xyster_Orm_Type_Interface
     */
    function getKeyType();
    
    /**
     * Gets the persister of the entity that owns this collection
     * 
     * @return Xyster_Orm_Persister_Entity_Interface
     */
    function getOwnerPersister();
    
    /**
     * Gets the name of this collection role
     * 
     * The role is the class name and a property path.
     * 
     * @return string
     */
    function getRole();
    
    /**
     * Gets the collection size
     * 
     * @param mixed $key
     * @param Xyster_Orm_Session_Interface $session
     * @return int
     */
    function getSize($key, Xyster_Orm_Session_Interface $session);
    
    /**
     * Gets the collection type
     * 
     * @return Xyster_Orm_Type_Collection_Abstract
     */
    function getType();

    /**
     * Gets whether this collection role is cacheable
     * 
     * @return boolean
     */
    function hasCache();
    
    /**
     * Gets whether this collection is indexed (a list or a map)
     * 
     * @return boolean
     */
    function hasIndex();
    
    /**
     * Gets whether this collection has ordering for many-to-many associations
     * 
     * @return boolean
     */
    function hasManyToManyOrdering();
    
    /**
     * Gets whether this collection is ordered
     *  
     * @return boolean
     */
    function hasOrdering();
    
    /**
     * Gets whether this collection deletes orphans
     * 
     * @return boolean
     */
    function hasOrphanDelete();
    
    /**
     * Gets whether the given index exists
     * @param mixed $key
     * @param object $index
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    function indexExists($key, $index, Xyster_Orm_Session_Interface $session);
    
    /**
     * Initializes a collection with the given key
     * 
     * @param mixed $key
     * @param Xyster_Orm_Session_Interface $session
     */
    function initialize($key, Xyster_Orm_Session_Interface $session);
    
    /**
     * Inserts the state of any new collection elements
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $key
     * @param Xyster_Orm_Session_Interface $session
     */
    function insertRows(Xyster_Orm_Collection_Interface $collection, $key, Xyster_Orm_Session_Interface $session);
    
    /**
     * Gets whether cascade delete is handled by the database constraints
     * 
     * @return boolean
     */
    function isCascadeDeleteEnabled();
    
    /**
     * Gets whether this collection needs to move out and get a job
     * 
     * (No idea what this does yet)
     * 
     * @return boolean
     */
    function isExtraLazy();
    
    /**
     * Gets whether the collection should not be persisted back
     * 
     * @return boolean
     */
    function isInverse();
    
    /**
     * Gets whether this collection is lazy
     * 
     * @return boolean
     */
    function isLazy();
    
    /**
     * Gets whether this is a many-to-many association
     * 
     * @return boolean
     */
    function isManyToMany();
    
    /**
     * Gets whether the elements of this collection can change
     * 
     * @return boolean
     */
    function isMutable();
    
    /**
     * Gets whether this collection is a one-to-many association
     * 
     * @return boolean
     */
    function isOneToMany();
    
    /**
     * Gets whether changes to this collection increment the owner's version
     * 
     * @return boolean
     */
    function isVersioned();
    
    /**
     * Called after instantiation
     */
    function postInstantiate();

    /**
     * Reads an element from a row of the result
     * 
     * @param array $values
     * @param object $owner
     * @param array $aliases
     * @param Xyster_Orm_Session_Interface $session
     * @return object
     */
    function readElement(array $values, $owner, array $aliases, Xyster_Orm_Session_Interface $session);
    
    /**
     * Reads an id from a row of the result
     * 
     * @param array $values
     * @param array $aliases
     * @param Xyster_Orm_Session_Interface $session
     * @return object
     */
    function readId(array $values, $alias, Xyster_Orm_Session_Interface $session);
    
    /**
     * Reads an index from a row of the result
     * 
     * @param array $values
     * @param array $aliases
     * @param Xyster_Orm_Session_Interface $session
     * @return object
     */
    function readIndex(array $values, array $aliases, Xyster_Orm_Session_Interface $session);
    
    /**
     * Reads a key from a row of the result
     * 
     * @param array $values
     * @param array $aliases
     * @param Xyster_Orm_Session_Interface $session
     * @return object
     */
    function readKey(array $values, array $aliases, Xyster_Orm_Session_Interface $session);
    
    /**
     * Recreate the collection's state
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $key
     * @param Xyster_Orm_Session_Interface $session
     */
    function recreate(Xyster_Orm_Collection_Interface $collection, $key, Xyster_Orm_Session_Interface $session);
    
    /**
     * Remove this collection in its entirety
     * 
     * @param mixed $id
     * @param Xyster_Orm_Session_Interface $session
     */
    function remove($id, Xyster_Orm_Session_Interface $session);
    
    /**
     * Updates the state of any modified elements
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $key
     * @param Xyster_Orm_Session_Interface $session 
     */
    function updateRows(Xyster_Orm_Collection_Interface $collection, $key, Xyster_Orm_Session_Interface $session);
    
}