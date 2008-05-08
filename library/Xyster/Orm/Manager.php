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
 * @see Xyster_Orm_Repository
 */
require_once 'Xyster/Orm/Repository.php';
/**
 * @see Xyster_Orm_Plugin_Broker
 */
require_once 'Xyster/Orm/Plugin/Broker.php';
/**
 * The main backend of the orm package
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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
     * The plugin broker
     *
     * @var Xyster_Orm_Plugin_Broker
     */
    protected $_plugins;
    
    /**
     * Creates a new Orm_Manager 
     *
     */
    public function __construct()
    {
        $this->_plugins = new Xyster_Orm_Plugin_Broker();
    }
    
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
            $keyNames = $map->getEntityType()->getPrimary();
            $id = array( $keyNames[0] => $id );
        }
        
        $entity = $this->getFromCache($className, $id, true);
        if ( $entity instanceof Xyster_Orm_Entity ) {
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
                $keyNames = $map->getEntityType()->getPrimary();
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
     * Tries to load an entity from the cache
     * 
     * This method will return null if the entity isn't in either cache
     *
     * @param string $className
     * @param mixed $id
     * @param boolean $checkSecondary Whether to also check the secondary cache
     * @return Xyster_Orm_Entity
     */
    public function getFromCache( $className, $id, $checkSecondary = false )
    {
        $map = $this->getMapperFactory()->get($className);
        
        if ( is_scalar($id) || is_null($id) ) {
            $keyNames = $map->getEntityType()->getPrimary();
            $id = array( $keyNames[0] => $id );
        }
        
        $entity = $this->getRepository()->get($className, $id);
        if ( $entity instanceof Xyster_Orm_Entity ) {
            return $entity;
        }

        if ( $checkSecondary ) {
            $entity = $this->_getFromSecondaryCache($className, $id);
            if ( $entity instanceof Xyster_Orm_Entity ) {
                $this->_plugins->postLoad($entity);
                $this->getRepository()->add($entity);
                return $entity;
            }
        }
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
     * Gets the plugin broker
     *
     * @return Xyster_Orm_Plugin_Broker
     */
    public function getPluginBroker()
    {
        return $this->_plugins;
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
     * Sets the secondary cache for storing entities
     *
     * If $cache is null, then no secondary cache is used.
     *
     * @param mixed $cache Either a Cache object, or a string naming a Registry key
     */
    public function setSecondaryCache($cache = null)
    {
        $this->_secondaryCache = $this->_setupSecondaryCache($cache);
    }
    
    /**
     * Gets an entity from the secondary cache
     *
     * @param string $className
     * @param array $id
     * @return Xyster_Orm_Entity the entity found or null if none
     */
    protected function _getFromSecondaryCache( $className, $id )
    {
        $cache = $this->getSecondaryCache();
        $entity = null;
        if ( $cache ) {
            $map = $this->getMapperFactory()->get($className);
            $cacheId = array('Xyster_Orm', $map->getDomain(), $className);
            foreach( $id as $key => $value ) {
                $cacheId[] = $key . '=' . $value;
            }
            $cacheId = md5(implode("/",$cacheId));
            
            $loaded = $cache->load($cacheId);
            if ( is_array($loaded) ) {
                $entity = $this->_shellToEntity($loaded, $map->getEntityType());
            }
        }
        return $entity;
    }
   
    /**
     * Puts the entity in the secondary cache
     * 
     * @param Xyster_Orm_Entity $entity
     */
    public function putInSecondaryCache( Xyster_Orm_Entity $entity )
    {
        $cache = $this->getSecondaryCache();
        $className = get_class($entity);
        $map = $this->getMapperFactory()->get($className);
        $cacheLifetime = $map->getLifetime();

        // only store the entity if it should be cached longer than the request
        // that's why we have the primary repository
        if ( $cache && $cacheLifetime > -1 ) {
            
            $cacheId = array('Xyster_Orm', $map->getDomain(), $className);
            foreach( $entity->getPrimaryKey() as $key => $value ) {
                $cacheId[] = $key . '=' . $value;
            }
            $cacheId = md5(implode("/", $cacheId));
            $shell = $this->_entityToShell($entity, $map->getEntityType());
            $cache->save($shell, $cacheId, array(), $cacheLifetime);
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
     * Turns an entity into a shell for storage
     *
     * @param Xyster_Orm_Entity $entity
     * @param Xyster_Orm_Entity_Type $meta
     * @return array
     */
    final protected function _entityToShell( Xyster_Orm_Entity $entity, Xyster_Orm_Entity_Type $meta )
    {
        $related = array();
        foreach( $meta->getRelations() as $relation ) {
            /* @var $relation Xyster_Orm_Relation */
            $name = $relation->getName();
            if ( $entity->isLoaded($name) ) {
                $linked = $entity->$name;
                if ( $linked instanceof Xyster_Orm_Set ) {
                    /* @var $linked Xyster_Orm_Set */
                    $related[$name] = $linked->getPrimaryKeys();
                } else if ( $linked instanceof Xyster_Orm_Entity ) {
                    /* @var $linked Xyster_Orm_Entity */
                    $related[$name] = $linked->getPrimaryKey();
                }
            }
        }
        return array('values' => $entity->toArray(), 'related' => $related);
    }
    
    /**
     * Take the serialized array and turn it back into an entity
     *
     * @param array $shell
     * @return Xyster_Orm_Entity
     */
    final protected function _shellToEntity( array $shell, Xyster_Orm_Entity_Type $meta )
    {
        if ( !isset($shell['values']) ) {
            return;
        }
        
        $className = $meta->getEntityName();
        $entity = new $className($shell['values']);
        if ( isset($shell['related']) && is_array($shell['related']) ) {
            foreach( $shell['related'] as $name => $related ) {
                $relation = $meta->getRelation($name);
                if ( !$relation->isCollection() ) {
                    $entity->$name = $this->get($relation->getTo(), $related); 
                } else {
                    $entity->$name = $this->getAll($relation->getTo(), $related);
                }
            }
        }
        $entity->setDirty(false);
        
        return $entity;
    }
    
    /**
     * @param mixed $cache Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Exception
     */
    final protected function _setupSecondaryCache($cache)
    {
        if ($cache === null) {
            return null;
        }
        if (is_string($cache)) {
            require_once 'Zend/Registry.php';
            $cache = Zend_Registry::get($cache);
        }
        if (!$cache instanceof Zend_Cache_Core) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $cache;
    }   
}