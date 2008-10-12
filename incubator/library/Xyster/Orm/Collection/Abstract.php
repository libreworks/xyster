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
 * @see Xyster_Orm_Collection_Interface
 */
require_once 'Xyster/Orm/Collection/Interface.php';
/**
 * @see Xyster_Collection
 */
require_once 'Xyster/Collection.php';
/**
 * Abstract implementation of a stateful collection
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Collection_Abstract implements Xyster_Orm_Collection_Interface
{
    /**
     * @var Xyster_Orm_Session_Interface
     */
    private $_session;
    
    private $_initialized = false;
    
    private $_queue = array();
    
    private $_accessible = false;
    private $_initializing = false;
    private $_owner;
    private $_cachedSize = -1;
    
    private $_role;
    
    private $_key;
    
    private $_dirty;
    /**
     * We might make this an array?
     * @var mixed
     */
    private $_storedSnapshot;
    
    private static $_unknown;
    
    /**
     * Protected constructor
     * 
     * @param Xyster_Orm_Session_Interface $session
     */
    protected function __construct(Xyster_Orm_Session_Interface $session)
    {
        $this->_session = $session;
    }
    
    /**
     * Forces immediate initialization (called by the session)
     */
    final public function forceInit()
    {
        if ( !$this->_initialized ) {
            if ( $this->_initializing ) {
                require_once 'Xyster/Orm/Collection/Exception.php';
                throw new Xyster_Orm_Collection_Exception("Collection is already initializing");
            }
            if ( $this->_session = null ) {
                require_once 'Xyster/Orm/Collection/Exception.php';
                throw new Xyster_Orm_Collection_Exception("Collection not attached to session");
            }
            if ( !$this->_session->isConnected() ) {
                require_once 'Xyster/Orm/Collection/Exception.php';
                throw new Xyster_Orm_Collection_Exception("Session is disconnected");
            }
            $this->_session->initCollection($this, false);
        }
    }
    
    /**
     * Gets the identifier of the given entry
     * 
     * @param mixed $entry
     * @param int $i
     * @return mixed
     */
    public function getId($entry, $i)
    {
        require_once 'Xyster/Orm/Collection/Exception.php';
        throw new Xyster_Orm_Collection_Exception('Cannot get identifier');
    }
    
    /**
     * Gets the current key value
     * 
     * @return mixed
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Gets the owning entity
     * 
     * @return object
     */
    public function getOwner()
    {
        return $this->_owner;
    }
    
    /**
     * Gets the queued additions
     * 
     * @return Iterator
     */
    public function getQueuedAdditions()
    {
        if ( $this->hasQueuedActions() ) {
            $adds = array();
            foreach( $this->_queue as $v ) {
                $adds[] = $v->getAdded();
            }
            return new ArrayIterator($adds);
        } else {
            return new EmptyIterator();
        }
    }
    
    /**
     * Gets the queued orphans
     * 
     * @param string $entityName
     * @return Xyster_Collection_Interface
     */
    public function getQueuedOrphans($entityName)
    {
        if ( $this->hasQueuedActions() ) { 
            $adds = new Xyster_Collection;
            $rems = new Xyster_Collection;
            foreach( $this->_queue as $action ) {
                $adds->add($action->getAdded());
                $rems->add($action->getRemoved());
            }
            return self::_getOrphans($rems, $adds, $entityName, $this->_session);
        } else {
            return Xyster_Collection::emptyList();
        }
    }
    
    /**
     * Gets the role name
     * 
     * @return string
     */
    public function getRole()
    {
        return $this->_role;
    }
    
    /**
     * Gets the assigned session
     * 
     * @return Xyster_Orm_Session_Interface
     */
    final public function getSession()
    {
        return $this->_session;
    }
    
    /**
     * Gets the stored snapshot
     * 
     * @return mixed
     */
    final public function getStoredSnapshot()
    {
        return $this->_storedSnapshot;
    }
    
    /**
     * Gets the user-visible collection
     * 
     * @return Xyster_Collection_Interface
     */
    public function getValue()
    {
        return $this;
    }
    
    /**
     * Gets whether this instance has any queued actions
     * 
     * @return boolean
     */
    final public function hasQueuedActions()
    {
        return !$this->_queue;
    }
    
    /**
     * Gets whether userland could have a reference to the internal collection
     * 
     * @return boolean
     */
    public function isDirectAccess()
    {
        return $this->_accessible;
    }
    
    /**
     * Gets whether the collection is dirty
     * 
     * @return boolean
     */
    final public function isDirty()
    {
        return $this->_dirty;
    }
    
    /**
     * Gets whether the instances has been initialized
     * 
     * @return boolean
     */
    final public function isInitialized()
    {
        return $this->_initialized;
    }
    
    /**
     * Gets whether updating the row is possible
     * 
     * @return boolean
     */
    public function isRowUpdatePossible()
    {
        return true;
    }
    
    /**
     * Gets whether the collection is unreferenced
     * 
     * @return boolean
     */
    final public function isUnreferenced()
    {
        return $this->_role == null;
    }
    
    /**
     * Clears any queued actions after flush
     */
    public function postFlush()
    {
        $this->_queue = array();
        $this->_cachedSize = -1;
        $this->setDirty(false);
    }
    
    /**
     * Called after initializing from cache
     */
    public function postInit()
    {
        $this->_setInitialized();
        if ( $this->_queue ) {
            $this->_performQueuedActions();
            $this->_queue = array();
            $this->_cachedSize = -1;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Called after inserting a row to get the generated id
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param object $entry
     * @param int $i
     */
    public function postInsert(Xyster_Orm_Persister_Collection_Interface $persister, $entry, $i)
    {
    }
    
    /**
     * Called after reading all rows
     * 
     * @return boolean
     */
    public function postRead()
    {
        return $this->postInit();
    }
    
    /**
     * Called before inserting any rows
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     */
    public function preInsert(Xyster_Orm_Persister_Collection_Interface $persister)
    {
    }
    
    /**
     * Called before reading rows
     */
    public function preRead()
    {
        $this->_initializing = true;
    }

    /**
     * Sets the collection to dirty or clean
     * 
     * @param boolean $flag
     */
    final public function setDirty( $flag = true )
    {
        $this->_dirty = $flag;
    }
    
    /**
     * Sets the reference to the owner
     * 
     * @param object $entity
     */
    public function setOwner($entity)
    {
        $this->_owner = $entity;
    }
    
    /**
     * Sets the collection with the given session
     * 
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean False if the collection was already associated with it
     * @throws Xyster_Orm_Exception if the collection was tied to another
     */
    final public function setSession(Xyster_Orm_Session_Interface $session)
    {
        if ( $session === $this->_session ) {
            return false;
        } else if ( $this->_isConnected() ) {
                require_once 'Xyster/Orm/Collection/Exception.php';
                throw new Xyster_Orm_Collection_Exception('Cannot associate a collection with two open sessions');
        } else {
            $this->_session = $session;
            return true;
        }
    }
    
    /**
     * Re-init snapshot state
     * 
     * @param mixed $key
     * @param string $role
     * @param mixed $snapshot
     */
    public function setSnapshot($key, $role, $snapshot)
    {
        $this->_key = $key;
        $this->_role = $role;
        $this->_storedSnapshot = $snapshot;
    }
    
    /**
     * Gets whether the collection needs to be recreated on change
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @return boolean
     */
    public function shouldRecreate(Xyster_Orm_Persister_Collection_Interface $persister)
    {
        return false;
    }
    
    /**
     * Detach this collection from the session
     * 
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean True if this was associated with the given session
     */
    final public function unsetSession(Xyster_Orm_Session_Interface $session)
    {
        if ( $session === $this->_session ) {
            $this->_session = null;
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Gets the cached size
     * 
     * @return int
     */
    protected function _getCachedSize()
    {
        return $this->_cachedSize;
    }
    
    /**
     * Gets the difference between the collections
     * 
     * @param Xyster_Collection_Interface $old
     * @param Xyster_Collection_Interface $current
     * @param string $entityName
     * @param Xyster_Orm_Session_Interface $session
     * @return Xyster_Collection_Interface
     */
    protected static function _getOrphans(Xyster_Collection_Interface $old, Xyster_Collection_Interface $current, $entityName, Xyster_Orm_Session_Interface $session)
    {
        if ( !$current->count() ) {
            return $old;
        }
        if ( !$old->count() ) {
            return $old;
        }
        
        $idType = $session->getFactory()->getEntityPersister($entityName)->getIdType();
        require_once 'Xyster/Collection.php';
        $coll = new Xyster_Collection;
        
        $ids = array();
        foreach( $current as $v ) {
            if ( $v != null &&
                Xyster_Orm_Engine_Transience::isNotTransient($entityName, $v, null, $session) ) {
                $currentId = Xyster_Orm_Engine_Transience::getEntityIdIfSaved($entityName, $v, $session);
                // $ids[] = 
            }
        }
        // @todo finish this 
    }
    
    /**
     * Gets a snapshot from the session context
     * 
     * @return mixed
     */
    final protected function _getSnapshot()
    {
        return $this->_session->getContext()->getSnapshot($this);
    }
    
    /**
     * Removes an id
     * 
     * @param Xyster_Collection_Interface $list
     * @param object $object
     * @param string $entityName
     * @param Xyster_Orm_Session_Interface $session
     */
    protected static function _idRemove(Xyster_Collection_Interface $list, $object, $entityName, Xyster_Orm_Session_Interface $session)
    {
        if ( $object !== null &&
            Xyster_Orm_Engine_Transience::isNotTransient($entityName, $object, null, $session) ) {
            $idType = $session->getFactory()->getEntityPersister($entityName)->getIdType();
            $id = Xyster_Orm_Engine_Transience::getEntityIdIfSaved($entityName, $object, $session);
            foreach( $list as $v ) {
                $oldId = Xyster_Orm_Engine_Transience::getEntityIdIfSaved($entityName, $v, $session);
                if ( $idType->isEqual($id, $oldId) ) {
                    $list->remove($v);
                    break;
                }
            }
        }
    }
    
    /**
     * Initialize the collection
     * 
     * @param boolean $writing
     */
    final protected function _initialize($writing)
    {
        if ( !$this->_initialized ) {
            if ( $this->_initializing ) {
                require_once 'Xyster/Orm/Collection/Exception.php';
                throw new Xyster_Orm_Collection_Exception('Cannot initialize loading collection');
            }
            $this->_throwLazyInitExceptionIfNotConnected();
            $this->_session->initCollection($this, $writing);
        }
    }

    /**
     * Whether the collection is in a state that can have queued actions
     * 
     * @return boolean
     */
    protected function _isActionQueueEnabled()
    {
        return !$this->_initialized && $this->_isConnected() &&
            $this->_isInverse();
    }
    
    /**
     * Whether the collection is in a state that can have queued puts
     * 
     * @return boolean
     */
    protected function _isPutQueueEnabled()
    {
        return !$this->_initialized && $this->_isConnected() &&
            $this->_isInverseOneToManyOrNoOrphanDelete();
    }
    
    /**
     * Whether the collection is in a state that can have queued clears
     * 
     * @return boolean
     */
    protected function _isClearQueueEnabled()
    {
        return !$this->_initialized && $this->_isConnected() &&
            $this->_isInverseNoOrphanDelete();
    }
    
    /**
     * Add queued elements to internal collection after reading all from db
     */
    final protected function _performQueuedActions()
    {
        foreach( $this->_queue as $action ) {
            $action->operate($this);
        }
    }
    
    /**
     * Queue an action
     * 
     * @param Xyster_Orm_Collection_Action $element
     */
    final protected function _queueAction(Xyster_Orm_Collection_Action $element)
    {
        $this->_queue[] = $element;
        $this->_dirty = true;
    }
    
    /**
     * Called by any read-only method
     */
    protected final function _read()
    {
        $this->_initialize(false);
    }
    
    protected function _readIndexExistence($index)
    {
        if (!$this->_initialized) {
            $this->_throwLazyInitExceptionIfNotConnected();
            $entry = $this->_session->getContext()->getCollectionEntry($this);
            $persister = $entry->getLoadedPersister();
            if ( $persister->isExtraLazy() ) {
                if ( $this->hasQueuedActions() ) {
                    $this->_session->flush();
                }
                return $persister->indexExists($entry->getLoadedKey(), $index, $this->_session);
            }
        }
        $this->_read();
        return null;
    }
    
    protected function _readElementExistence($element)
    {
        if ( !$this->_initialized ) {
            $this->_throwLazyInitExceptionIfNotConnected();
            $entry = $this->_session->getContext()->getCollectionEntry($this);
            $persister = $entry->getLoadedPersister();
            if ( $persister->isExtraLazy() ) {
                if ( $this->hasQueuedActions() ) {
                    $this->_session->flush(); 
                }
                return $persister->elementExists($entry->getLoadedKey(), $element, $this->_session);
            }
        }
        $this->_read();
        return null;
    }
    
    protected function _readElementByIndex($index)
    {
        if ( !$this->_initialized ) {
            $this->_throwLazyInitExceptionIfNotConnected();
            $entry = $this->_session->getContext()->getCollectionEntry($this);
            $persister = $entry->getLoadedPersister();
            if ( $persister->isExtraLazy() ) {
                if ( $this->hasQueuedActions() ) {
                    $this->_session->flush(); 
                }
                return $persister->getElementByIndex($entry->getLoadedKey(), $index, $this->_session, $this->_owner);
            }
        }
        $this->_read();
        return self::_getUnknownMarker();
    }
    
    protected function _readSize()
    {
        if (!$this->_initialized) {
            if ( $this->_cachedSize != -1 && $this->hasQueuedActions() ) {
                return true;
            } else {
                $this->_throwLazyInitExceptionIfNotConnected();
                $entry = $this->_session->getContext()->getCollectionEntry($this);
                $persister = $entry->getLoadedPersister();
                if ( $persister->isExtraLazy() ) {
                    if ( $this->hasQueuedActions() ) {
                        $this->_session->flush();
                    }
                    $this->_cachedSize = $persister->getSize($entry->getLoadedKey(),
                        $this->_session);
                    return true;
                }
            }
        }
        $this->_read();
        return false;
    }
    
    /**
     * Sets the initialized info
     */
    final protected function _setInitialized()
    {
        $this->_initializing = false;
        $this->_initialized = true;
    }
    
    /**
     * Sets the direct access
     * 
     * @param boolean $flag
     */
    final protected function _setDirectAccess( $flag )
    {
        $this->_accessible = $flag;
    }
    
    /**
     * Writes
     */
    final protected function _write()
    {
        $this->_initialize(true);
        $this->setDirty(true);
    }
    
    protected static function _getUnknownMarker()
    {
        if ( !self::$_unknown ) { 
            $obj = array('name' => 'UNKNOWN');
            self::$_unknown = (object)$obj;
        }
        return self::$_unknown;
    }
    
    /**
     * Whether the collection is connected to an open session
     * 
     * @return boolean
     */
    private function _isConnected()
    {
        return $this->_session !== null && $this->_session->isOpen() &&
            $this->_session->getContext()->containsCollection($this);
    }
    
    private function _isInverse()
    {
        $entry = $this->_session->getContext()->getCollectionEntry($this);
        return $entry != null && $entry->getLoadedPersister()->isInverse();
    }
    
    private function _isInverseNoOrphanDelete()
    {
        $entry = $this->_session->getContext()->getCollectionEntry($this);
        return $entry !== null && $entry->getLoadedPersister()->isInverse() &&
            $entry->getLoadedPersister()->hasOrphanDelete();
    }
    
    private function _isInverseOneToManyOrNoOrphanDelete()
    {
        $entry = $this->_session->getContext()->getCollectionEntry($this);
        return $entry !== null && $entry->getLoadedPersister()->isInverse() &&
            ($entry->getLoadedPersister()->isOneToMany() || 
            !$entry->getLoadedPersister()->hasOrphanDelete());
    }
    
    private function _throwLazyInitException($message)
    {
        require_once 'Xyster/Orm/Collection/Exception.php';
        throw new Xyster_Orm_Collection_Exception('Cannot initialize collection'
            . (!$this->_role ? '' : ' of role: ' . $this->_role ) . ', ' . 
            $message);
    }
    
    private function _throwLazyInitExceptionIfNotConnected()
    {
        if ( $this->_isConnected() ) {
            $this->_throwLazyInitException('no session or session was closed');
        }
        if ( !$this->_session->isConnected() ) {
            $this->_throwLazyInitException('session is disconnected');
        }
    }
}