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
class Xyster_Orm_Engine_CollectionEntry
{
    //ATTRIBUTES MAINTAINED BETWEEN FLUSH CYCLES
	
    // session-start/post-flush persistent state
    private $_snapshot;
    // allow the CollectionSnapshot to be serialized
    private $_role;
    
    // "loaded" means the reference that is consistent 
    // with the current database state
    private $_loadedPersister;
    private $_loadedKey;
    
    // ATTRIBUTES USED ONLY DURING FLUSH CYCLE
    
    // during flush, we navigate the object graph to
    // collections and decide what to do with them
    private $_reached;
    private $_processed;
    private $_update;
    private $_remove;
    private $_recreate;
    // if we instantiate a collection during the flush() process,
    // we must ignore it for the rest of the flush()
    private $_ignore;
    
    // "current" means the reference that was found during flush() 
    private $_persister;
    private $_key;
    
    /**
     * Gets the persister
     * 
     * @return Xyster_Orm_Persister_Collection_Interface
     */
    public function getPersister()
    {
        
    }
    
    /**
     * Gets the key
     * 
     * @return mixed
     */
    public function getKey()
    {
        
    }
    
    /**
     * Gets the loaded key
     * 
     * @return mixed
     */
    public function getLoadedKey()
    {
        
    }
    
    /**
     * 
     * @return Xyster_Orm_Persister_Collection_Interface
     */
    public function getLoadedPersister()
    {
        
    }
    
    /**
     * Gets the collection orphans
     * 
     * @param string $entityName
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Collection_Interface
     */
    public function getOrphans($entityName, Xyster_Orm_Collection_Interface $collection)
    {
        
    }
    
    /**
	 * Gets the collection role
	 *
	 * @return string
     */
    public function getRole()
    {
        
    }

    /**
     * Gets the collection snapshot
     * 
     * @return mixed
     */
    public function getSnapshot()
    {
        
    }
    
    /**
     * Whether the collection is dereferenced
     * 
     * @return boolean
     */
    public function isDereferenced()
    {
        
    }
    
    /**
     * Whether the collection should 
     * 
     * @return boolean
     */
    public function shouldRecreate()
    {
        
    }
    
    /**
     * Whether the collection should remove
     * 
     * @return boolean
     */
    public function shouldRemove()
    {
        
    }
    
    /**
     * Whether the collection should update
     * 
     * @return boolean
     */
    public function shouldUpdate()
    {
        
    }
    
    /**
     * Whether the collection is in ignore mode(?)
     * 
     * @return boolean
     */
    public function isIgnore()
    {
        
    }
    
    /**
     * Whether the collection is processed
     * 
     * @return boolean
     */
    public function isProcessed()
    {
        
    }
    
    /**
     * Whether the snapshot is empty
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return boolean
     */
    public function isSnapshotEmpty(Xyster_Orm_Collection_Interface $collection)
    {
        
    }
    
    /**
     * Called after the flush
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function postFlush(Xyster_Orm_Collection_Interface $collection)
    {
        
    }
    
    /**
     * Called after initialization
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function postInit(Xyster_Orm_Collection_Interface $collection)
    {
        
    }
    
    /**
     * Called before flush
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function preFlush(Xyster_Orm_Collection_Interface $collection)
    {
        
    }
    
    /**
     * Sets the key
     * 
     * @param mixed $key
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setKey( $key )
    {
        
    }
    
    /**
     * Sets the persister 
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setPersister( Xyster_Orm_Persister_Collection_Interface $persister )
    {
        
    }
    
    /**
     * Sets that the collection should recreate itself
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setRecreate( $flag = true )
    {
        
    }
    
    /**
     * Sets that the collection should remove
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setRemove( $flag = true )
    {
        
    }
    
    /**
     * Sets that the collection should update
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setUpdate( $flag = true )
    {
        
    }
    
    /**
     * Sets whether the collection has been processed
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setProcessed( $flag = true )
    {
        
    }
    
    /**
     * Sets whether the collection has been reached
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setReached( $flag = true )
    {
        
    }
    
    /**
     * Sets the collection role 
     * 
     * @param string $role
     * @return Xyster_Orm_Engine_CollectionEntry provides a fluent interface
     */
    public function setRole( $role )
    {
        
    }
}