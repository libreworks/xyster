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
 * @see Xyster_Orm_WorkUnit
 */
require_once 'Xyster/Orm/WorkUnit.php';
/**
 * @see Xyster_Orm_Repository
 */
require_once 'Xyster/Orm/Repository.php';
/**
 * The main front-end for the ORM package
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm
{
    /**
     * The singleton instance of this class
     * 
     * @var Xyster_Orm
     */
    static protected $_instance;

    /**
     * Secondary cache for entities by primary key
     *
     * @var Zend_Cache_Core
     */
    static protected $_secondaryCache;
    
    /**
     * Mapper factory
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    static protected $_mapFactory;
    
    /**
     * The entity repository (identity map)
     *
     * @var Xyster_Orm_Repository
     */
    protected $_repository;

    /**
     * The "unit of work" to hold our pending transactions
     *
     * @var Xyster_Orm_WorkUnit
     */
    protected $_work;

    /**
     * Creates a new Xyster_Orm object, hide from userland
     */
    protected function __construct()
    {
    }

    /**
     * Called if the object is cloned - singletons cannot be cloned
     * 
     * @magic
     */
    private function __clone()
    {
    }
    
    /**
     * Gets an instance of Xyster_Orm
     * 
     * @return Xyster_Orm
     */    
    static public function getInstance()
    {
        if (! self::$_instance instanceof self ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Gets the secondary repository for storing entities
     *
     * @return Zend_Cache_Core
     */
    public static function getSecondaryCache()
    {
        return self::$_secondaryCache;
    }
    /**
     * Sets the secondary repository for storing entities
     *
     * If $repository is null, then no secondary repository is used.
     *
     * @param mixed $repository Either a Cache object, or a string naming a Registry key
     */
    public static function setSecondaryCache($repository = null)
    {
        self::$_secondaryCache = self::_setupSecondaryCache($repository);
    }
    
    /**
     * Sets the factory for entity mappers
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $mapFactory
     */
    public static function setMapperFactory( Xyster_Orm_Mapper_Factory_Interface $mapFactory )
    {
        self::$_mapFactory = $mapFactory;
    }
    
    /**
     * Gets the factory for entity mappers
     *
     * @return Xyster_Orm_Mapper_Factory_Interface
     */
    public static function getMapperFactory()
    {
        if ( !self::$_mapFactory ) {
            require_once 'Xyster/Orm/Mapper/Factory.php';
            self::$_mapFactory = new Xyster_Orm_Mapper_Factory();
        }
        return self::$_mapFactory;
    }
    
    /**
     * Abandons the current session
     * 
     * This will unload the repository and unset the singleton instance.  The
     * next call to {@link getInstance} will return a new session. 
     */
    public function clear()
    {
        $this->_repository = null;
        $this->_getWorkUnit()->rollback();
        self::$_instance = null;
    }
    
    /**
     * Commits pending operations to the data store
     * 
     */
    public function commit()
    {
        foreach( $this->_getRepository()->getClasses() as $class ) {
            foreach( $this->_getRepository()->getAll($class) as $entity ) {
                if ( $entity->isDirty() ) {
                    try {
                        $this->_getWorkUnit()->registerDirty($entity);
                    } catch ( Xyster_Orm_Exception $thrown ) {
                        // do nothing - the entity was probably pending delete 
                    }
                }
            }
        }

        $this->_getWorkUnit()->commit();
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
        $map = self::getMapperFactory()->get($className);
        
        if ( is_scalar($id) ) {
            $keyNames = (array) $map->getPrimary();
            $id = array( $map->translateField($keyNames[0]) => $id );
        }
        
        $entity = $this->_getRepository()->get($className,$id);
        if ( $entity ) {
            return $entity;
        }

        $entity = $this->_getFromSecondaryCache($className,$id);
        if ( $entity ) {
            $this->_getRepository()->add($entity);
            return $entity;
        }

        $entity = $map->get($id); 
        if ( $entity ) {
            $this->_getRepository()->add($entity);
            $this->_putInSecondaryCache($entity);
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
        $map = self::getMapperFactory()->get($className);
        
        if ( is_array($ids) && count($ids) ) {
            // we're getting a few entities by primary key

            if ( $this->_getRepository()->hasAll($className) ) {
                $keyNames = (array) $map->getPrimary();
                $all = $map->getSet();
                foreach( $ids as $id ) {
                    if ( is_scalar($id) ) {
                        $id = array( $map->translateField($keyNames[0]) => $id );
                    }
                    $entity = $this->_getRepository()->get($className,$id);
                    if ( $entity ) {
                        $all->add( $entity );
                    } else {
                        $entity = $this->_getFromSecondaryCache($className,$id);
                        if ( $entity ) {
                            $all->add($entity);
                        }
                    }
                }
            } else {
                $all = $map->getAll($ids);
                $this->_getRepository()->addAll($all);
                foreach( $all as $entity ) {
                    $this->_putInSecondaryCache($entity);
                }
            }
            
        } else {
            // we're getting ALL entities from the source

            if ( $this->_getRepository()->hasAll($className) ) {
                $all = $this->_getRepository()->getAll($className);
                if ( is_array($all) ) {
                    $all = $map->getSet( Xyster_Collection::using($all) );
                }
            } else {
                $all = $map->getAll();
                $this->_getRepository()->addAll($all);
                foreach( $all as $entity ) {
                    $this->_putInSecondaryCache($entity);
                }
                $this->_getRepository()->setHasAll($className,true);
            }
            
        }
        
        return $all;
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
        if ( $entity = $this->_getRepository()->find($className,$criteria) ) {
            
            return $entity;
            
        } else {
            
            $map = self::getMapperFactory()->get($className);
            $entity = $map->find($criteria);
            if ( $entity ) {
                $this->_getRepository()->add($entity);
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
        $map = self::getMapperFactory()->get($className);
        $all = $map->findAll($criteria,$sorts);
        if ( count($all) ) {
            $this->_getRepository()->addAll($all);
        }
        return $all;
    }

    /**
     * Sets an entity to be added to the data store
     *
     * @param Xyster_Orm_Entity $entity
     * @throws Xyster_Orm_Exception if the entity is already persisted
     */
    public function persist( Xyster_Orm_Entity $entity )
    {
        if ( $entity->getPrimaryKey(true) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('This entity is already persisted');
        }
        $this->_getWorkUnit()->registerNew($entity);
    }

    /**
     * Creates a query object to return entities
     *
     * @param string $className The entity class name
     * @param string $xsql The XSQL expression to use
     * @return Xyster_Orm_Query The query object
     */
    public function query( $className, $xsql = null )
    {
        require_once 'Xyster/Orm/Query.php';
        require_once 'Xyster/Orm/Query/Parser.php';
        
        $query = null;
        
        if ( $xsql ) {
            $parser = new Xyster_Orm_Query_Parser(self::getMapperFactory());
            $query = $parser->parseQuery($className, $xsql);
        } else { 
            $query = new Xyster_Orm_Query($className, self::getMapperFactory());
        }
        
        return $query;
    }
    
    /**
     * Refreshes the values of an entity 
     */
    public function refresh( Xyster_Orm_Entity $entity )
    {
        self::getMapperFactory()->get($className)->refresh($entity);
    }

    /**
     * Sets an entity to be removed
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function remove( Xyster_Orm_Entity $entity )
    {
        $this->_getWorkUnit()->registerRemoved($entity);
    }
    
    /**
     * Creates a report query object to return a data set
     *
     * @param string $className The entity class name
     * @param string $xsql The XSQL expression to use
     * @return Xyster_Orm_Query_Report The report query object
     */
    public function reportQuery( $className, $xsql = null )
    {
        require_once 'Xyster/Orm/Query/Report.php';
        require_once 'Xyster/Orm/Query/Parser.php';
        
        $query = null;
        
        if ( $xsql ) {
            $parser = new Xyster_Orm_Query_Parser(self::getMapperFactory());
            $query = $parser->parseReportQuery($className, $xsql);
        } else { 
            $query = new Xyster_Orm_Query_Report($className, self::getMapperFactory());
        }
        
        return $query;
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
        $repo = self::getSecondaryCache();
        if ( $repo ) {
            $repoId = array( 'Xyster_Orm',
                self::getMapperFactory()->get($className)->getDomain(),
                $className );
            foreach( $id as $key => $value ) {
                $repoId[] = $key . '=' . $value;
            }
            $repoId = md5(implode("/",$repoId));
            
            return $repo->load($repoId);
        }

        return null;
    }
    
    /**
     * Gets the entity repository
     *
     * @return Xyster_Orm_Repository
     */
    protected function _getRepository()
    {
        if ( !$this->_repository ) {
            $this->_repository = new Xyster_Orm_Repository(self::getMapperFactory());
        }
        return $this->_repository;
    }
    
    /**
     * Gets the work unit
     *
     * @return Xyster_Orm_WorkUnit
     */
    protected function _getWorkUnit()
    {
        if ( !$this->_work ) {
            $this->_work = new Xyster_Orm_WorkUnit($this->_getRepository(),
                self::getMapperFactory());
        }
        return $this->_work;
    }
    
    /**
     * Puts the entity in the secondary repository
     * 
     * @param Xyster_Orm_Entity $entity
     */
    protected function _putInSecondaryCache( Xyster_Orm_Entity $entity )
    {
        $repo = self::getSecondaryCache();
        $className = get_class($entity);
        $map = self::getMapperFactory()->get($className);
        $cacheLifetime = $map->getLifetime();

        // only store the entity if it should be cached longer than the request
        // that's why we have the primary repository
        if ( $repo && $cacheLifetime > -1 ) {
            
            $repoId = array( 'Xyster_Orm', $map->getDomain(), $className );
            foreach( $entity->getPrimaryKey() as $key => $value ) {
                $repoId[] = $key . '=' . $value;
            }
            $repoId = md5(implode("/",$repoId));
            $repo->save( $repoId, $entity, null, $cacheLifetime );

        }
    }
    
    /**
     * @param mixed $repository Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Exception
     */
    protected static final function _setupSecondaryCache($repository)
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