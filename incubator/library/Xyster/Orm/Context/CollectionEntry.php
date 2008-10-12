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
 * @todo This thing really needs to be cleaned up and improved.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Context_CollectionEntry
{
    private $_snapshot;
    
    private $_role;
    
    /**
     * The persister that is consistent with the current database state
     * @var Xyster_Orm_Persister_Collection_Interface
     */
    private $_loadedPersister;
    /**
     * The key that is consistent with the current database state
     * @var mixed
     */
    private $_loadedKey;
    
    private $_reached;
    private $_processed;
    private $_update;
    private $_remove;
    private $_recreate;
    private $_ignore;
    
    /**
     * The persister that was assigned during the flush process
     *  
     * @var Xyster_Orm_Persister_Collection_Interface
     */
    private $_persister;
    
    /**
     * The key that was assigned during the flush process
     * @var mixed
     */
    private $_key;
    
    /**
     * Hidden constructor
     */
    private function __construct()
    {  
    }

    /**
     * Creates an entry for a newly wrapped collection or a dereferenced wrapper
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Context_CollectionEntry
     */
    public static function createForWrapped(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection)
    {
        $entry = new self;
        $entry->_ignore = false;
        $collection->setDirty(false);
        $entry->_snapshot = $persister->isMutable() ? 
            $collection->getSnapshot($persister) : null;
        $collection->setSnapshot($entry->_loadedKey, $entry->_role, $entry->_snapshot);
        return $entry;
    }
    
    /**
     * Creates an entry for a collection just loaded from the database
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param Xyster_Orm_Collection_Interface $collection
     * @param mixed $key
     * @param boolean $ignore
     * @return Xyster_Orm_Context_CollectionEntry
     */
    public static function createForLoaded(Xyster_Orm_Persister_Collection_Interface $persister, Xyster_Orm_Collection_Interface $collection, $key, $ignore)
    {
        $entry = new self;
        $entry->_ignore = $ignore;
        $entry->_loadedKey = $key;
        $entry->_setLoadedPersister($persister);
        $collection->setSnapshot($entry->_loadedKey, $entry->_role, null);
        return $entry;
    }
    
    /**
     * Creates an entry for an uninitialized detached collection
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param mixed $key
     * @return Xyster_Orm_Context_CollectionEntry
     */
    public static function createForUninitializedDetached(Xyster_Orm_Persister_Collection_Interface $persister, $key)
    {
        $entry = new self;
        $entry->_ignore = false;
        $entry->_loadedKey = $key;
        $entry->_setLoadedPersister($persister);
        return $entry;
    }
    
    /**
     * Whether the collection can be ignored
     * 
     * A collection can be ignored if it was created during the flush process
     * 
     * @return boolean
     */
    public function canIgnore()
    {
        return $this->_ignore;
    }
    
    /**
     * Gets the persister
     * 
     * @return Xyster_Orm_Persister_Collection_Interface
     */
    public function getPersister()
    {
        return $this->_persister;
    }
    
    /**
     * Gets the key
     * 
     * @return mixed
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Gets the loaded key
     * 
     * @return mixed
     */
    public function getLoadedKey()
    {
        $this->_loadedKey;
    }
    
    /**
     * 
     * @return Xyster_Orm_Persister_Collection_Interface
     */
    public function getLoadedPersister()
    {
        return $this->_loadedPersister;
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
        if ( $this->_snapshot === null ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('No collection snapshot to determine orphans');
        }
        return $collection->getOrphans($this->_snapshot, $entityName);
    }
    
    /**
	 * Gets the collection role
	 *
	 * @return string
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Gets the collection snapshot
     * 
     * @return mixed
     */
    public function getSnapshot()
    {
        return $this->_snapshot;
    }
    
    /**
     * Whether the collection is dereferenced
     * 
     * @return boolean
     */
    public function isDereferenced()
    {
        return $this->getLoadedKey() === null;
    }
    
    /**
     * Whether the collection is processed
     * 
     * @return boolean
     */
    public function isProcessed()
    {
        return $this->_processed;
    }
    
    /**
     * Whether the collection has been reached
     * 
     * @return boolean
     */
    public function isReached()
    {
        return $this->_reached;
    }
    
    /**
     * Whether the snapshot is empty
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return boolean
     */
    public function isSnapshotEmpty(Xyster_Orm_Collection_Interface $collection)
    {
        return $collection->isInitialized() &&
            ($this->getLoadedPersister() === null ||
                $this->getLoadedPersister()->isMutable()) &&
            $collection->isSnapshotEmpty($this->getSnapshot());
    }
    
    /**
     * Called after an action is executed
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     */
    public function postAction(Xyster_Orm_Collection_Interface $collection)
    {
        $lkey = $this->getKey();
    }
    
    /**
     * Called after the flush
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function postFlush(Xyster_Orm_Collection_Interface $collection)
    {
        if ( $this->_ignore ) {
            $this->_ignore = false;
        } else if ( !$this->_processed ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Collection not processed during flush');
        }
        $collection->setSnapshot($this->_loadedKey, $this->_role,
            $this->_snapshot);
    }
    
    /**
     * Called after initialization
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function postInit(Xyster_Orm_Collection_Interface $collection)
    {
        $persister = $this->_loadedPersister;
        $snap = $persister->isMutable() ?
            $collection->getSnapshot($persister) : null;
        $collection->setSnapshot($this->_loadedKey, $this->_role,
            $this->_snapshot); 
    }
    
    /**
     * Called before flush
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function preFlush(Xyster_Orm_Collection_Interface $collection)
    {
        $persister = $this->_loadedPersister;
        $nonMutable = $collection->isDirty() && $persister != null &&
            !$persister->isMutable();
        if ( $nonMutable ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Changed an immutable collection');
        }
        $this->_setDirty($collection);
        $this->setUpdate(false)
            ->setRemove(false)
            ->setRecreate(false)
            ->setReached(false)
            ->setProcessed(false);
    }
    
    /**
     * Sets the key
     * 
     * @param mixed $key
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setKey( $key )
    {
        $this->_key = $key;
        return $this;
    }
    
    /**
     * Sets the persister 
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setPersister( Xyster_Orm_Persister_Collection_Interface $persister )
    {
        $this->_persister = $persister;
        return $this;
    }
    
    /**
     * Sets that the collection should recreate itself
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setRecreate( $flag = true )
    {
        $this->_recreate = $flag;
        return $this;
    }
    
    /**
     * Sets that the collection should remove
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setRemove( $flag = true )
    {
        $this->_remove = $flag;
        return $this;
    }
    
    /**
     * Sets that the collection should update
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setUpdate( $flag = true )
    {
        $this->_update = $flag;
        return $this;
    }
    
    /**
     * Sets whether the collection has been processed
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setProcessed( $flag = true )
    {
        $this->_processed = $flag;
        return $this;
    }
    
    /**
     * Sets whether the collection has been reached
     * 
     * @param boolean $flag
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setReached( $flag = true )
    {
        $this->_reached = $flag;
        return $this;
    }
    
    /**
     * Sets the collection role 
     * 
     * @param string $role
     * @return Xyster_Orm_Context_CollectionEntry provides a fluent interface
     */
    public function setRole( $role )
    {
        $this->_role = $role;
        return $this;
    }
    
    /**
     * Whether the collection should 
     * 
     * @return boolean
     */
    public function shouldRecreate()
    {
        return $this->_recreate;
    }
    
    /**
     * Whether the collection should remove
     * 
     * @return boolean
     */
    public function shouldRemove()
    {
        return $this->_remove;
    }
    
    /**
     * Whether the collection should update
     * 
     * @return boolean
     */
    public function shouldUpdate()
    {
        return $this->_update;
    }
    
    /**
     * Determines if a collection is dirty
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     */
    protected function _setDirty(Xyster_Orm_Collection_Interface $collection)
    {
        $persister = $this->_loadedPersister;
        $force = $collection->isInitialized() && !$collection->isDirty() &&
            $persister != null && $persister->isMutable() &&
            ($collection->isDirectAccess() ||
                $persister->getElementType()->isMutable()) &&
            !$collection->equalsSnapshot($persister);
        if ( $force ) {
            $collection->setDirty(true);
        }
    }
    
    /**
     * Sets the loaded persister
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     */
    protected function _setLoadedPersister(Xyster_Orm_Persister_Collection_Interface $persister = null)
    {
        $this->_loadedPersister = $persister;
        $this->setRole($persister === null ? null : $persister->getRole());
    }
}