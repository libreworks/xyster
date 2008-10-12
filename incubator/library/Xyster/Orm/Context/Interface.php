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
 * The persistence context (identity map, entries, snapshots, proxies, etc.) 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Context_Interface
{
    /**
     * Maps an entity key to an instance
     *  
     * @param Xyster_Orm_Context_EntityKey $key
     * @param object $entity
     */
    function addEntity(Xyster_Orm_Context_EntityKey $key, $entity);
    
    /**
     * Maps an entity unique key to an instance
     * 
     * @param Xyster_Orm_Context_EntityUniqueKey $key
     * @param object $entity
     */
    function addEntityUnique(Xyster_Orm_Context_EntityUniqueKey $key, $entity);

    /**
     * Adds an entity to the internal cache
     * 
     * @param object $entity
     * @param Xyster_Orm_Engine_Status $status
     * @param array $state
     * @param mixed $key
     * @param mixed $version
     * @param boolean $inDatabase
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @param boolean $disableVersion
     * @param boolean $lazyUnfetched
     * @return Xyster_Orm_Context_EntityEntry
     */
    function addEntityEntry($entity, Xyster_Orm_Engine_Status $status, array $state, Xyster_Orm_Context_EntityKey $key, $version, $inDatabase, Xyster_Orm_Persister_Entity_Interface $persister, $disableVersion, $lazyUnfetched);

    /**
     * Creates an EntityEntry and adds it to the internal cache
     * 
     * @param object $entity
     * @param Xyster_Orm_Engine_Status $status
     * @param array $state
     * @param mixed $rowId
     * @param mixed $id
     * @param mixed $version
     * @param boolean $inDatabase
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @param boolean $disableVersion
     * @param boolean $lazyUnfetched
     * @return Xyster_Orm_Context_EntityEntry
     */
    function addEntry($entity, Xyster_Orm_Engine_Status $status, array $state, $rowId, $id, $version, $inDatabase, Xyster_Orm_Persister_Entity_Interface $persister, $disableVersion, $lazyUnfetched);
    
    /**
     * Adds an initialized collection
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $id
     * @return Xyster_Orm_Context_CollectionEntry
     */
    function addInitializedCollection(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection, $id);

    /**
     * Adds a detached collection (created by another session)
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     */
    function addDetachedCollection(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Adds a new collection
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     */
    function addNewCollection(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Adds a collection for non-lazy loading
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     */
    function addNonLazyCollection(Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Registers a null property association
     * @param Xyster_Orm_Context_EntityKey $key
     * @param string $propertyName
     */
    function addNullProperty(Xyster_Orm_Context_EntityKey $key, $propertyName);
    
    /**
     * Adds a proxy to the cache
     * @param Xyster_Orm_Context_EntityKey $key
     * @param object $proxy
     */
    function addProxy(Xyster_Orm_Context_EntityKey $key, $proxy);
    
    /**
     * Adds an uninitialized collection
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $id
     */
    function addUninitializedCollection(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection, $id);
    
    /**
     * Adds a detached uninitialized collection
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     */
    function addUninitializedDetachedCollection(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Adds a collection with no loaded owner
     * 
     * @param Xyster_Orm_Context_CollectionKey $key
     * @param Xyster_Orm_Collection_Interface $collection
     */
    function addUnownedCollection(Xyster_Orm_Context_CollectionKey $key, Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Checks if the given key represents an entity loaded in the session
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @param object $object
     */
    function checkUnique(Xyster_Orm_Context_EntityKey $key, $object);
    
    /**
     * Clears out the context
     */
    function clear();
    
    /**
     * Whether the context contains the collection provided
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return boolean
     */
    function containsCollection(Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Whether the context contains an entity with the key provided
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @return boolean
     */
    function containsEntity(Xyster_Orm_Context_EntityKey $key);
    
    /**
     * Whether the context contains the proxy provided
     * 
     * @param $proxy
     * @return boolean
     */
    function containsProxy($proxy);
    
    /**
     * Gets the batch fetch queue
     * 
     * @return Xyster_Orm_Engine_BatchFetchQueue
     */
    function getBatchFetchQueue();
    
    /**
     * Gets the cached database snapshot for the entity key provided
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @return array
     */
    function getCachedSnapshot(Xyster_Orm_Context_EntityKey $key);
    
    /**
     * Gets the collection associated with the provided key
     * 
     * @param Xyster_Orm_Context_CollectionKey $key
     * @return Xyster_Orm_Collection_Interface
     */
    function getCollection(Xyster_Orm_Context_CollectionKey $key);
    
    /**
     * Gets the mapping from instances to entries
     * 
     * @return Xyster_Collection_Map_Interface
     */
    function getCollectionEntries();
    
    /**
     * Gets the entry for the collection given
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Context_CollectionEntry
     */
    function getCollectionEntry(Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Gets the entry for the given collection (could be wrapped or unwrapped)
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Context_CollectionEntry
     */
    function getCollectionEntryOrNull($collection);
    
    /**
     * Gets the load context for collections
     * 
     * @return Xyster_Orm_Engine_CollectionLoadContext
     */
    function getCollectionLoadContext();
    
    /**
     * Gets the owner for the collection
     * 
     * @param mixed $key
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return object
     */
    function getCollectionOwner($key, Xyster_Orm_Persister_Collection_Interface $persister);
    
    /**
     * Gets the mapping from key to instance
     * 
     * @return Xyster_Collection_Map_Interface
     */
    function getCollectionsByKey();

    /**
     * Gets the current state of the entity in the database or null if no row
     * 
     * @param mixed $id
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @return array
     */
    function getDatabaseSnapshot($id, Xyster_Orm_Persister_Entity_Interface $persister);
    
    /**
     * Gets the mapping from key to instance
     * 
     * @return Xyster_Collection_Map_Interface
     */
    function getEntitiesByKey();
    
    /**
     * Gets the entity instance associated with the key
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @return object
     */
    function getEntity(Xyster_Orm_Context_EntityKey $key);
    
    /**
     * Gets the entity cached by unique key
     * 
     * @param Xyster_Orm_Context_EntityUniqueKey $key
     * @return object
     */
    function getEntityUnique(Xyster_Orm_Context_EntityUniqueKey $key);
    
    /**
     * Gets the mapping from instance to entry
     * 
     * @return Xyster_Collection_Map_Interface
     */
    function getEntityEntries();
    
    /**
     * Gets the entityentry associated with the given entity
     * 
     * @param object $entity
     * @return Xyster_Orm_Context_EntityEntry
     */
    function getEntry($entity);
    
    /**
     * Search the context for an index of the child object
     * 
     * @param object $entity
     * @param string $property
     * @param object $childObject
     * @param Xyster_Collection_Map_Interface $mergeMap
     * @return mixed
     */
    function getIndexInOwner($entity, $property, $childObject, Xyster_Collection_Map_Interface $mergeMap);

    /**
     * Gets the context's load context
     * 
     * @return Xyster_Orm_Engine_Load_Contexts
     */
    function getLoadContexts();
    
    /**
     * Gets a snapshot of the natural id
     * 
     * @param mixed $id
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @return array
     */
    function getNaturalIdSnapshot($id, Xyster_Orm_Persister_Entity_Interface $persister);
    
    /**
     * Gets the entityKeys for nullifiable references
     * 
     * @return Xyster_Collection_Set_Interface
     */
    function getNullifiableEntityKeys();
    
    /**
     * Search the context for an owner of the child object
     * 
     * @param object $entity
     * @param string $property
     * @param object $childObject
     * @param Xyster_Collection_Map_Interface $map
     * @return mixed
     */
    function getOwnerId($entity, $property, $childObject, Xyster_Collection_Map_Interface $map);
    
    /**
     * Gets a proxy by key
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @return object
     */
    function getProxy(Xyster_Orm_Context_EntityKey $key);
    
    /**
     * Gets the session
     * 
     * @return Xyster_Orm_Session_Interface
     */
    function getSession();
    
    /**
     * Gets the snapshot of the pre-flush state
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return mixed
     */
    function getSnapshot(Xyster_Orm_Collection_Interface $collection);
    
    /**
     * Whether the context has non-read-only entities
     * 
     * @return boolean
     */
    function hasWritableEntities();
    
    /**
     * Force initialization of all non-lazy collections during the 2-phase load
     */
    function initializeNonLazyCollections();
    
    /**
     * Whether there's an entityentry for the given instance
     * 
     * @param object $entity
     * @return boolean
     */
    function isEntryFor($entity);
    
    /**
     * Whether there's a flush cycle currently in progress
     * 
     * @return boolean
     */
    function isFlushing();
    
    /**
     * Whether the association property is null
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @param string $propertyName
     * @return boolean
     */
    function isPropertyNull(Xyster_Orm_Context_EntityKey $key, $propertyName);
    
    /**
     * Whether the context is stateless
     * 
     * @return boolean
     */
    function isStateless();

    /**
     * Instantiate a new proxy and overwrite the old if the existing proxy is too derived
     *  
     * @todo what does this do??
     *  
     * @param object $proxy
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @param Xyster_Orm_Context_EntityKey $key
     * @param object $object
     * @return object
     */
    function narrowProxy($proxy, Xyster_Orm_Persister_Entity_Interface $persister, Xyster_Orm_Context_EntityKey $key, $object);
    
    /**
     * Called after a two-phase load
     */
    function postLoad();
    
    /**
     * Called after transaction
     */
    function postTransaction();
    
    /**
     * Called before a two-phase load
     */
    function preLoad();
    
    /**
     * Gets the proxy tied to the entity or the third argument if none exists
     * 
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @param Xyster_Orm_Context_EntityKey $key
     * @param object $impl
     * @return unknown_type
     */
    function proxyFor(Xyster_Orm_Persister_Entity_Interface $persister, Xyster_Orm_Context_EntityKey $key, $impl);
    
    /**
     * Gets the proxy tied to the entity or the same object if none exists
     * 
     * @param object $impl
     * @return object
     */
    function proxyForEntity($impl);
    
    /**
     * Reset the id of the proxy if the deleted entity is re-saved
     * 
     * @param object $value
     * @param mixed $id
     */
    function reassociateProxy($value, $id);
    
    /**
     * Reassociates it with the even source if the object is represents proxy
     * 
     * @param object $value
     * @return boolean
     */
    function reassociateProxyIfUninitialized($value);
    
    /**
     * Removes an entity from the cache
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @return object
     */
    function removeEntity(Xyster_Orm_Context_EntityKey $key);
    
    /**
     * Removes an entity entry from the session
     * 
     * @param object $entity
     * @return Xyster_Orm_Context_EntityEntry
     */
    function removeEntry($entity);
    
    /**
     * Removes a proxy from the cache
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @return object
     */
    function removeProxy(Xyster_Orm_Context_EntityKey $key);
    
    /**
     * Replaces an old key with a new one if it's delayed
     * 
     * @param Xyster_Orm_Context_EntityKey $key
     * @param mixed $generatedId
     */
    function replaceDelayedId(Xyster_Orm_Context_EntityKey $key, $generatedId);
    
    /**
     * Sets an entry status
     * 
     * @param Xyster_Orm_Context_EntityEntry $entry
     * @param Xyster_Orm_Engine_Status $status
     */
    function setEntryStatus(Xyster_Orm_Context_EntityEntry $entry, Xyster_Orm_Engine_Status $status);
    
    /**
     * Called before and after the flush process
     * 
     * @param boolean $flag
     */
    function setFlushing( $flag );
    
    /**
     * Sets an object to read-only and remove its snapshot
     * 
     * @param object $entity
     * @param boolean $flag
     */
    function setReadOnly($entity, $flag);
    
    /**
     * Gets the entity instance underlying the proxy
     * 
     * @param object $proxy
     * @return object
     * @throws Xyster_Orm_Exception if the proxy is uninitialized
     */
    function unproxy($proxy);
    
    /**
     * Unproxy the reference and reassociate it with the session
     * 
     * @param object $proxy
     * @return object
     */
    function unproxyAndReassociate($proxy);
    
    /**
     * Get/remove a collection whose owner is not yet loaded or is loading
     * 
     * @param Xyster_Orm_Context_CollectionKey $key
     * @return Xyster_Orm_Collection_Interface
     */
    function useUnownedCollection(Xyster_Orm_Context_CollectionKey $key);
}