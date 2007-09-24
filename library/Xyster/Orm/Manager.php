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
 * @see Xyster_Orm_Repository
 */
require_once 'Xyster/Orm/Repository.php';
/**
 * The main backend of the orm package
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Manager
{
    /**
     * The mapper factory
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mapFactory;
    
    /**
     * The repository for storing entities
     *
     * @var Xyster_Orm_Repository
     */
    protected $_repository;
    
    /**
     * The secondary cache
     *
     * @var Zend_Cache_Core
     */
    protected $_secondaryCache;
    
    /**
     * Clears out the repository
     *
     */
    public function clear()
    {
        $this->_repository = null;
    }
    
    /**
     * Executes a query or report query
     *
     * @param Xyster_Orm_Query $query
     * @return Xyster_Data_Set 
     */
    public function executeQuery( Xyster_Orm_Query $query )
    {
        $set = $this->_mapFactory->get($query->getFrom())->query($query);
        
        if ( $set instanceof Xyster_Orm_Set ) {
        	// add returned entities to the cache
        	$this->getRepository()->addAll($set);
        	$this->putAllInSecondaryCache($set);
        }
        
        return $set;
    }
    
    /**
     * Gets the first entity found matching a set of criteria
     *
     * @param string $className
     * @param array $criteria
     * @return Xyster_Orm_Entity The entity found or null if none
     */
    public function find( $className, array $criteria )
    {
        if ( $entity = $this->getRepository()->find($className, $criteria) ) {
            
            return $entity;
            
        } else {
            
            $map = $this->getMapperFactory()->get($className);
            $entity = $map->find($criteria);
            if ( $entity ) {
                $this->getRepository()->add($entity);
                $this->putInSecondaryCache($entity);
            }
            return $entity;
        }
    }
    
    /**
     * Finds all entities matching a given criteria
     *
     * @param string $className
     * @param mixed $criteria {@link Xyster_Data_Criterion} or associative array 
     * @param mixed $sorts Array of {@link Xyster_Data_Sort} objects
     */
    public function findAll( $className, $criteria, $sorts = null )
    {
        $map = $this->getMapperFactory()->get($className);
        $all = $map->findAll($criteria, $sorts);
        $this->getRepository()->addAll($all);
        $this->putAllInSecondaryCache($all);
        return $all;
    }
    
    /**
     * Gets an entity by class and primary key
     * 
     * @param string $className
     * @param mixed $id
     * @return Xyster_Orm_Entity
     */
    public function get( $className, $id )
    {
        $map = $this->getMapperFactory()->get($className);
        
        if ( is_scalar($id) || is_null($id) ) {
            $keyNames = $map->getEntityMeta()->getPrimary();
            $id = array( $keyNames[0] => $id );
        }
        
        $entity = $this->getRepository()->get($className, $id);
        if ( $entity instanceof Xyster_Orm_Entity ) {
            return $entity;
        }

        $entity = $this->_getFromSecondaryCache($className, $id);
        if ( $entity instanceof Xyster_Orm_Entity ) {
            $this->getRepository()->add($entity);
            return $entity;
        }

        $entity = $map->get($id); 
        if ( $entity instanceof Xyster_Orm_Entity ) {
            $this->getRepository()->add($entity);
            $this->putInSecondaryCache($entity);
            return $entity;
        }
        return null;
    }
    
    /**
     * Gets all entities from the data source or a subset if given the keys
     *
     * @param string $className
     * @param array $ids
     * @return Xyster_Orm_Set
     */
    public function getAll( $className, array $ids = null )
    {
        $all = null;
        $map = $this->getMapperFactory()->get($className);
        
        if ( is_array($ids) && count($ids) ) {
            // we're getting a few entities by primary key

            if ( $this->getRepository()->hasAll($className) ) {
                $keyNames = $map->getEntityMeta()->getPrimary();
                $all = $map->getSet();
                foreach( $ids as $id ) {
                    if ( is_scalar($id) ) {
                        $id = array( $keyNames[0] => $id );
                    }
                    $entity = $this->getRepository()->get($className, $id);
                    if ( $entity ) {
                        $all->add( $entity );
                    }
                }
            } else {
                $all = $map->getAll($ids);
                $this->getRepository()->addAll($all);
                $this->putAllInSecondaryCache($all);
            }
            
        } else {
            // we're getting ALL entities from the source

            if ( $this->getRepository()->hasAll($className) ) {
                $all = $this->getRepository()->getAll($className);
                if (! $all instanceof Xyster_Orm_Set ) {
                    $all = $map->getSet($all);
                }
            } else {
                $all = $map->getAll();
                $this->getRepository()->addAll($all);
                $this->putAllInSecondaryCache($all);
                $this->getRepository()->setHasAll($className);
            }
            
        }
        
        return $all;
    }
    
    /**
     * Gets entities via a many-to-many table
     *
     * @param string $className
     * @param Xyster_Orm_Entity $entity
     * @param Xyster_Orm_Relation $relation
     * @return Xyster_Orm_Set
     */
    public function getJoined( Xyster_Orm_Entity $entity, Xyster_Orm_Relation $relation )
    {
        $className = get_class($entity);
        $joined = $this->_mapFactory->get($className)->getJoined($entity, $relation);
        $this->getRepository()->addAll($joined);
        return $joined;
    }
    
    /**
     * Gets the factory for entity mappers
     *
     * @return Xyster_Orm_Mapper_Factory_Interface
     */
    public function getMapperFactory()
    {
        if ( !$this->_mapFactory ) {
            require_once 'Xyster/Orm/Mapper/Factory.php';
            $this->setMapperFactory(new Xyster_Orm_Mapper_Factory());
        }
        return $this->_mapFactory;
    }
    
    /**
     * Gets the entity repository
     *
     * @return Xyster_Orm_Repository
     */
    public function getRepository()
    {
        if ( !$this->_repository ) {
            $this->_repository = new Xyster_Orm_Repository($this->getMapperFactory());
        }
        return $this->_repository;
    }
    
    /**
     * Gets the secondary repository for storing entities
     *
     * @return Zend_Cache_Core
     */
    public function getSecondaryCache()
    {
        return $this->_secondaryCache;
    }
    
    /**
     * Refreshes the values of an entity 
     */
    public function refresh( Xyster_Orm_Entity $entity )
    {
        $this->getMapperFactory()->get(get_class($entity))->refresh($entity);
    }
    
    /**
     * Sets the factory for entity mappers
     * 
     * This method also calls the 
     * {@link Xyster_Orm_Mapper_Factory_Interface::setManager} method.
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $mapFactory
     */
    public function setMapperFactory( Xyster_Orm_Mapper_Factory_Interface $mapFactory )
    {
        $this->_mapFactory = $mapFactory;
        $mapFactory->setManager($this);
    }
    
    /**
     * Sets the secondary repository for storing entities
     *
     * If $repository is null, then no secondary repository is used.
     *
     * @param mixed $repository Either a Cache object, or a string naming a Registry key
     */
    public function setSecondaryCache($repository = null)
    {
        $this->_secondaryCache = $this->_setupSecondaryCache($repository);
    }
    
    /**
     * Gets an entity from the secondary repository
     *
     * @param string $className
     * @param array $id
     * @return Xyster_Orm_Entity the entity found or null if none
     */
    protected function _getFromSecondaryCache( $className, $id )
    {
        $repo = $this->getSecondaryCache();
        $entity = null;
        if ( $repo ) {
            $repoId = array( 'Xyster_Orm',
                $this->getMapperFactory()->get($className)->getDomain(),
                $className );
            foreach( $id as $key => $value ) {
                $repoId[] = $key . '=' . $value;
            }
            $repoId = md5(implode("/",$repoId));
            
            $entity = $repo->load($repoId);
        }
        return $entity;
    }
   
    /**
     * Puts the entity in the secondary repository
     * 
     * @param Xyster_Orm_Entity $entity
     */
    public function putInSecondaryCache( Xyster_Orm_Entity $entity )
    {
        $repo = $this->getSecondaryCache();
        $className = get_class($entity);
        $map = $this->getMapperFactory()->get($className);
        $cacheLifetime = $map->getLifetime();

        // only store the entity if it should be cached longer than the request
        // that's why we have the primary repository
        if ( $repo && $cacheLifetime > -1 ) {
            
            $repoId = array('Xyster_Orm', $map->getDomain(), $className);
            foreach( $entity->getPrimaryKey() as $key => $value ) {
                $repoId[] = $key . '=' . $value;
            }
            $repoId = md5(implode("/", $repoId));
            $repo->save($entity, $repoId, array(), $cacheLifetime);
        }
    }
    
    /**
     * Convenience method to put all entities in a set in the cache
     *
     * @param Xyster_Orm_Set $set
     */
    public function putAllInSecondaryCache( Xyster_Orm_Set $set )
    {
        foreach( $set as $entity ) {
            $this->putInSecondaryCache($entity);
        }
    }
    
    /**
     * @param mixed $repository Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Exception
     */
    final protected function _setupSecondaryCache($repository)
    {
        if ($repository === null) {
            return null;
        }
        if (is_string($repository)) {
            require_once 'Zend/Registry.php';
            $repository = Zend_Registry::get($repository);
        }
        if (!$repository instanceof Zend_Cache_Core) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $repository;
    }   
}