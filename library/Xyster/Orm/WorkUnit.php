<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Collection_Set
 */
require_once 'Xyster/Collection/Set.php';
/**
 * A transactional unit of work
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_WorkUnit
{
    /**
     * Entities that are in queue for update
     *
     * @var Xyster_Collection_Set
     */
    protected $_dirty;
    
    /**
     * Entities that are in queue for insertion
     *
     * @var Xyster_Collection_Set
     */
    protected $_new;
    
    /**
     * Entities that are in queue for removal
     *
     * @var Xyster_Collection_Set
     */
    protected $_removed;
    
    /**
     * Creates a new Xyster_Orm_WorkUnit object
     *
     */
    public function __construct()
    {
        $this->_new = new Xyster_Collection_Set();
        $this->_dirty = new Xyster_Collection_Set();
        $this->_removed = new Xyster_Collection_Set();
    }

    /**
     * Execute pending transactions
     *
     * @param Xyster_Orm_Repository $repo The repository in use by the manager
     * @param Xyster_Orm_Mapper_Factory_Interface $mapFactory The mapper factory 
     */
    public function commit( Xyster_Orm_Repository $repo, Xyster_Orm_Mapper_Factory_Interface $mapFactory )
    {
        /*
	     * Save all new entities
	     */
		foreach( $this->_new as $v ) {
			$mapFactory->get(get_class($v))->save($v);
			$repo->add($v);
		}
		$this->_new->clear();
		
		/*
		 * Update any existing entities
		 */
		foreach( $this->_dirty as $v ) {
			$mapFactory->get(get_class($v))->save($v);
		}
		$this->_dirty->clear();
		
		/*
		 * Delete entities to be removed
		 */
		foreach( $this->_removed as $v ) {
			$mapFactory->get(get_class($v))->delete($v);
			$repo->remove($v);
		}
		$this->_removed->clear();
    }

    /**
     * Register an entity as in queue for insertion
     *
     * @param Xyster_Orm_Entity $entity The entity to persist
     * @throws Xyster_Orm_Exception if the entity is already registered
     */
    public function registerNew( Xyster_Orm_Entity $entity )
    {
        if ( $this->_dirty->contains($entity) ||
            $this->_removed->contains($entity) ||
            $this->_new->contains($entity) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('This entity is already registered');
        }
        $this->_new->add($entity);
    }
    
    /**
     * Register an entity as dirty
     *
     * @param Xyster_Orm_Entity $entity The entity to update
     * @throws Xyster_Orm_Exception if the entity can't be set as dirty
     */
    public function registerDirty( Xyster_Orm_Entity $entity )
    {
        $key = $entity->getPrimaryKey();
        if ( array_keys($key, null, true) == array_keys($key) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('This entity cannot be saved, it must first be persisted');
        }
        if ( $this->_removed->contains($entity) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('This entity cannot be updated because it is in queue for removal');
        }
        $this->_dirty->add($entity);
    }
    
    /**
     * Register an entity as in queue for removal
     *
     * @param Xyster_Orm_Entity $entity The entity to remove
     */
    public function registerRemoved( Xyster_Orm_Entity $entity )
    {
        if ( $this->_new->remove($entity) )  {
            return;
        }
        $this->_dirty->remove($entity);
        $this->_removed->add($entity);
    }
    
    /**
     * Cancel any pending changes
     *
     * Any entity objects still in memory will remain in a modified state, but
     * will not be committed to the data store. 
     */
    public function rollback()
    {
        $this->_new->clear();
        $this->_removed->clear();
        $this->_dirty->clear();
    }
}