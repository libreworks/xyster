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
 * @see Xyster_Orm_Engine_Status
 */
require_once 'Xyster/Orm/Engine/Status.php';
/**
 * Abstracts the current and persistent states of an entity
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Context_EntityEntry
{
    /**
     * @var array
     */
    private $_deletedState;
        
    private $_id;
    
    private $_inDatabase = false;
    
    private $_loadedLazyUnfetched = false;
    
    /**
     * @var array
     */
    private $_loadedState;
    
    private $_name;
    
    /**
     * @var Xyster_Orm_Persister_Entity_Interface
     */
    private $_persister;
    
    private $_rowId;
    
    /**
     * @var Xyster_Orm_Engine_Status
     */
    private $_status;
    
    private $_version;

    /**
     * Creates a new entity entry object
     * 
     * @param Xyster_Orm_Engine_Status $status
     * @param array $loadedState
     * @param mixed $rowId
     * @param mixed $id
     * @param mixed $version
     * @param boolean $inDatabase
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     * @param boolean $disableVersionIncrement
     * @param boolean $lazyUnfetched
     */
    public function __construct(Xyster_Orm_Engine_Status $status, array $loadedState, $rowId, $id, $version, $inDatabase, Xyster_Orm_Persister_Entity_Interface $persister, $disableVersionIncrement, $lazyUnfetched)
    {
        $this->_status = $status;
        $this->_loadedState = $loadedState;
        $this->_id = $id;
        $this->_rowId = $rowId;
        $this->_inDatabase = $inDatabase;
        $this->_version = $version;
        $this->_loadedLazyUnfetched = $lazyUnfetched;
        $this->_persister = $persister;
        $this->_name = $persister === null ?
            null : $persister->getEntityName();
    }
    
    /**
     * Gets the deleted state
     * 
     * @return array
     */
    public function getDeletedState()
    {
        return $this->_deletedState;
    }
    
    /**
     * Gets the entity name
     * 
     * @return string
     */
    public function getEntityName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the identifier
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Gets the loaded state
     * 
     * @return array
     */
    public function getLoadedState()
    {
        return $this->_loadedState;
    }
    
    /**
     * Loads the value for the property given
     * 
     * @param string $propertyName
     * @return mixed
     */
    public function getLoadedValue($propertyName)
    {
        $index = $this->_persister->getPropertyIndex($propertyName);
        return $this->_loadedState[$index];
    }
    
    /**
     * Gets the entity persister
     * 
     * @return Xyster_Orm_Persister_Entity_Interface
     */
    public function getPersister()
    {
        return $this->_persister;
    }
    
    /**
     * Gets the row identifier
     * 
     * @return mixed
     */
    public function getRowId()
    {
        return $this->_rowId;
    }
    
    /**
     * Gets the status of the entity
     * 
     * @return Xyster_Orm_Engine_Status
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * Gets the entity version
     * 
     * @return mixed
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * Gets whether this entity exists in the database
     * 
     * @return boolean
     */
    public function isInDatabase()
    {
        return $this->_inDatabase;
    }
    
    /**
     * Gets whether the entity has been loaded and its lazy properties unfetched
     * 
     * @return boolean
     */
    public function isLoadedLazyUnfetched()
    {
        return $this->_loadedLazyUnfetched;
    }
    
    /**
     * Whether the entity is nullifiable
     * 
     * @param boolean $earlyInsert
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    public function isNullifiable($earlyInsert, Xyster_Orm_Session_Interface $session)
    {
        if ( $this->_status === Xyster_Orm_Engine_Status::Saving() ) {
            return true;
        } else if ( $earlyInsert ) {
            return !$this->_inDatabase;
        } else {
            $keys = $session->getContext()->getNullifiableEntityKeys();
            $expect = new Xyster_Orm_Context_EntityKey($this->getId(),
                    $this->getPersister());
            foreach( $keys as $key ) {
                 if ( $expect->equals($key) ) {
                     return true;
                 }
            }
            return false;
        }
    }
    
    /**
     * Called after database delete
     */
    public function postDelete()
    {
        $this->_status = Xyster_Orm_Engine_Status::Gone();
        $this->_inDatabase = false;
    }
    
    /**
     * Called after database insert
     */
    public function postInsert()
    {
        $this->_inDatabase = true;
    }
    
    /**
     * Called after database update
     * 
     * @param object $entity
     * @param array $state
     * @param mixed $nextVersion
     */
    public function postUpdate($entity, array $state, $nextVersion)
    {
        $this->_loadedState = $state;
        
        $pers = $this->_persister;
        if ( $pers->isVersioned() ) {
            $this->_version = $nextVersion;
            $pers->setPropertyValue($entity, $pers->getVersionProperty(),
                $nextVersion);
        }
    }
    
    /**
     * Gets whether the entity should be checked for dirtiness
     * 
     * @param object $entity
     * @return boolean
     */
    public function requiresDirtyCheck($entity)
    {
        $pers = $this->_persister;
        return $this->_status !== Xyster_Orm_Engine_Status::ReadOnly() &&
            $pers->isMutable() && $pers->hasMutableProperties();
    }
    
    /**
     * Sets the deleted state
     * 
     * @param array $state
     * @return Xyster_Orm_Context_EntityEntry provides a fluent interface
     */
    public function setDeletedState(array $state)
    {
        $this->_deletedState = $state;
        return $this;
    }
    
    /**
     * Sets whether the entity is read-only
     * 
     * @param boolean $readOnly
     * @param object $entity
     * @return Xyster_Orm_Context_EntityEntry provides a fluent interface
     */
    public function setReadOnly($readOnly, $entity)
    {
        if ( $this->_status !== Xyster_Orm_Engine_Status::Managed() && 
            $this->_status !== Xyster_Orm_Engine_Status::ReadOnly() ) {
            require_once 'Xyster/Orm/Context/Exception.php';
            throw new Xyster_Orm_Context_Exception('Cannot set read-only for this state: ' . $this->_status->getName());
        }
        if ( $readOnly ) {
            $this->setStatus(Xyster_Orm_Engine_Status::ReadOnly());
            $this->_loadedState = null;
        } else {
            $this->setStatus(Xyster_Orm_Engine_Status::Managed());
            $this->_loadedState = $this->_persister->getPropertyValues($entity);
        }
        return $this;
    }
    
    /**
     * Sets the entity status
     * 
     * @param Xyster_Orm_Engine_Status $status
     * @return Xyster_Orm_Context_EntityEntry provides a fluent interface
     */
    public function setStatus( Xyster_Orm_Engine_Status $status )
    {
        if ( $status === Xyster_Orm_Engine_Status::ReadOnly() ) {
            $this->_loadedState = null;
        }
        $this->_status = $status;
        return $this;
    }
}