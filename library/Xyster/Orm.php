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
 * @see Xyster_Orm_Mapper
 */
require_once 'Xyster/Orm/Mapper.php';
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
     * Creates a new Xyster_Orm object
     */
    protected function __construct()
    {
        $this->_repository = new Xyster_Orm_Repository();
        $this->_work = new Xyster_Orm_WorkUnit( $this->_repository );
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
     * Abandons the current session
     * 
     * This will unload the repository and unset the singleton instance.  The
     * next call to {@link getInstance} will return a new session. 
     */
    public function clear()
    {
        $this->_repository = null;
        $this->_work->rollback();
        self::$_instance = null;
    }
    
    /**
     * Commits pending operations to the data store
     * 
     */
    public function commit()
    {
        foreach( $this->_repository->getClasses() as $class ) {
            foreach( $this->_repository->getAll($class) as $entity ) {
                if ( $entity->isDirty() ) {
                    try {
                        $this->_work->registerDirty($entity);
                    } catch ( Xyster_Orm_Exception $thrown ) {
                        // do nothing - the entity was probably pending delete 
                    }
                }
            }
        }

        $this->_work->commit();
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
        $map = Xyster_Orm_Mapper::factory($className);
        
        if ( is_scalar($id) ) {
            $keyNames = (array) $map->getPrimary();
            $id = array( $map->translateField($keyNames[0]) => $id );
        }
        
        $entity = $this->_repository->get($className,$id);
        if ( $entity ) {
            return $entity;
        }

        $entity = $this->_getFromSecondaryCache($className,$id);
        if ( $entity ) {
            $this->_repository->add($entity);
            return $entity;
        }

        $entity = $map->get($id); 
        if ( $entity ) {
            $this->_repository->add($entity);
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
        $map = Xyster_Orm_Mapper::factory($className);
        
        if ( is_array($ids) && count($ids) ) {
            // we're getting a few entities by primary key

            if ( $this->_repository->hasAll($className) ) {
                $keyNames = (array) $map->getPrimary();
                $all = $map->getSet();
                foreach( $ids as $id ) {
                    if ( is_scalar($id) ) {
                        $id = array( $map->translateField($keyNames[0]) => $id );
                    }
                    $entity = $this->_repository->get($className,$id);
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
                $this->_repository->addAll($all);
                foreach( $all as $entity ) {
                    $this->_putInSecondaryCache($entity);
                }
            }
            
        } else {
            // we're getting ALL entities from the source

            if ( $this->_repository->hasAll($className) ) {
                $all = $this->_repository->getAll($className);
                if ( is_array($all) ) {
                    $all = $map->getSet( Xyster_Collection::using($all) );
                }
            } else {
                $all = $map->getAll();
                $this->_repository->addAll($all);
                foreach( $all as $entity ) {
                    $this->_putInSecondaryCache($entity);
                }
                $this->_repository->setHasAll($className,true);
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
        if ( $entity = $this->_repository->find($className,$criteria) ) {
            
            return $entity;
            
        } else {
            
            $map = Xyster_Orm_Mapper::factory($className);
            $entity = $map->find($criteria);
            if ( $entity ) {
                $this->_repository->add($entity);
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
        $all = Xyster_Orm_Mapper::factory($className)->findAll($criteria,$sorts);
        if ( count($all) ) {
            $this->_repository->addAll($all);
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
        $this->_work->registerNew($entity);
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
        
        return ( $xsql ) ?
            Xyster_Orm_Query_Parser::parseQuery($className, $xsql) :
            new Xyster_Orm_Query($className);
    }
    
    /**
     * Refreshes the values of an entity 
     */
    public function refresh( Xyster_Orm_Entity $entity )
    {
        Xyster_Orm_Mapper::factory(get_class($entity))->refresh($entity);
    }

    /**
     * Sets an entity to be removed
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function remove( Xyster_Orm_Entity $entity )
    {
        $this->_work->registerRemoved($entity);
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
        
        return ( $xsql ) ?
            Xyster_Orm_Query_Parser::parseReportQuery($className, $xsql) :
            new Xyster_Orm_Query_Report($className);
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
                Xyster_Orm_Mapper::factory($className)->getDomain(), $className );
            foreach( $id as $key => $value ) {
                $repoId[] = $key . '=' . $value;
            }
            $repoId = md5(implode("/",$repoId));
            
            return $repo->load($repoId);
        }

        return null;
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
        $map = Xyster_Orm_Mapper::factory($className);
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