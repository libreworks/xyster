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
 * A collection value object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Collection_Interface
{
    /**
     * Gets whether the current state is the same as the snapshot
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return boolean
     */
    function equalsSnapshot(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Forces immediate initialization (called by the session)
     */
    function forceInit();
    
    /**
     * Gets all the elements that need to be deleted
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return Iterator
     */
    function getDeletes(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Gets the value of the given entry
     * 
     * @param mixed $entry
     * @return mixed
     */
    function getElement($entry);
    
    /**
     * Gets an iterator for entries during update
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return Iterator
     */
    function getEntries(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Gets the identifier of the given entry
     * 
     * @param mixed $entry
     * @param int $i
     * @return mixed
     */
    function getId($entry, $i);

    /**
     * Gets the index of the given entry
     * 
     * @param mixed $entry
     * @param int $i
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return mixed
     */
    function getIndex($entry, $i, Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Gets the current key value
     * 
     * @return mixed
     */
    function getKey();
    
    /**
     * Gets all orphaned elements
     * 
     * @param mixed $snapshot
     * @param string $entityName
     * @return Xyster_Collection_Interface
     */
    function getOrphans($snapshot, $entityName);
    
    /**
     * Gets the owning entity
     * 
     * @return object
     */
    function getOwner();
    
    /**
     * Gets the queued additions
     * 
     * @return Iterator
     */
    function getQueuedAdditions();
    
    /**
     * Gets the queued orphans
     * 
     * @param string $entityName
     * @return Xyster_Collection_Interface
     */
    function getQueuedOrphans($entityName);
    
    /**
     * Gets the role name
     * 
     * @return string
     */
    function getRole();
    
    /**
     * Gets a new snapshot
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return mixed
     */
    function getSnapshot(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Gets the snapshot value of the given entry
     * @param mixed $entry
     * @param int $i
     * @return mixed
     */
    function getSnapshotElement($entry, $i);
    
    /**
     * Gets the cached snapshot
     * 
     * @return mixed
     */
    function getStoredSnapshot();
    
    /**
     * Gets the user-visible collection
     * 
     * @return Xyster_Collection_Interface
     */
    function getValue();
    
    /**
     * Gets whether this instance has any queued actions
     * 
     * @return boolean
     */
    function hasQueuedActions();
    
    /**
     * Reads the state of the packed cached value
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param mixed $packed
     * @param object $owner
     */
    function initFromCache(Xyster_Orm_Persister_Collection_Interface $persister, $packed, $owner);
    
    /**
     * Gets whether userland could have a reference to the internal collection
     * 
     * @return boolean
     */
    function isDirectAccess();
    
    /**
     * Gets whether the collection is dirty
     * 
     * @return boolean
     */
    function isDirty();
    
    /**
     * Gets whether the collection is empty
     * 
     * @return boolean
     */
    function isEmpty();
    
    /**
     * Gets whether the instances has been initialized
     * 
     * @return boolean
     */
    function isInitialized();
    
    /**
     * Gets whether updating the row is possible
     * 
     * @return boolean
     */
    function isRowUpdatePossible();
    
    /**
     * Gets whether the snapshot is empty
     * 
     * @param mixed $snapshot
     * @return boolean
     */
    function isSnapshotEmpty($snapshot);
    
    /**
     * Gets whether the collection is unreferenced
     * 
     * @return boolean
     */
    function isUnreferenced();
    
    /**
     * Gets whether this is the wrapper for the internal collection
     * 
     * @param mixed $collection
     * @return boolean
     */
    function isWrapper($collection);
    
    /**
     * Packs the collection for caching
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return mixed
     */
    function pack(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Clears any queued actions after flush
     */
    function postFlush();
    
    /**
     * Called after initializing from cache
     */
    function postInit();
    
    /**
     * Called after inserting a row to get the generated id
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param object $entry
     * @param int $i
     */
    function postInsert(Xyster_Orm_Persister_Collection_Interface $persister, $entry, $i);

    /**
     * Called after reading all rows
     */
    function postRead();
    
    /**
     * Called before initializing with elements
     *  
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param int $size
     */
    function preInit(Xyster_Orm_Persister_Collection_Interface $persister, $size);

    /**
     * Called before inserting any rows
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     */
    function preInsert(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Called before reading rows
     */
    function preRead();

    /**
     * Read a row from the result set
     *  
     * @param Zend_Db_Statement_Interface $stmt
     * @param Xyster_Orm_Persister_Collection_Interface $role
     * @param Xyster_Orm_Collection_Aliases $descriptor
     * @param object $owner
     * @return object
     */
    function read(Zend_Db_Statement_Interface $stmt, Xyster_Orm_Persister_Collection_Interface $role, Xyster_Orm_Collection_Aliases $descriptor, $owner);
    
    /**
     * Sets the collection with the given session
     * 
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    function setSession(Xyster_Orm_Session_Interface $session);
    
    /**
     * Sets the collection to dirty or clean
     * 
     * @param boolean $flag
     */
    function setDirty( $flag = true );
    
    /**
     * Sets the reference to the owner
     * 
     * @param object $entity
     */
    function setOwner($entity);
    
    /**
     * Re-init snapshot state
     * 
     * @param mixed $key
     * @param string $role
     * @param mixed $snapshot
     */
    function setSnapshot($key, $role, $snapshot);
    
    /**
     * Gets whether the element needs to be inserted
     * 
     * @param mixed $entry
     * @param int $i
     * @param Xyster_Orm_Type_Interface $type
     * @return boolean
     */
    function shouldInsert($entry, $i, Xyster_Orm_Type_Interface $type);
    
    /**
     * Gets whether the collection needs to be recreated on change
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return boolean
     */
    function shouldRecreate(Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Whether the element needs to be updated
     * 
     * @param mixed $entry
     * @param int $i
     * @param Xyster_Orm_Type_Interface $type
     * @return boolean
     */
    function shouldUpdate($entry, $i, Xyster_Orm_Type_Interface $type);

    /**
     * Detach this collection from the session
     * 
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    function unsetSession(Xyster_Orm_Session_Interface $session);
}