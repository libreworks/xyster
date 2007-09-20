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
 * @see Xyster_Orm_Manager
 */
require_once 'Xyster/Orm/Manager.php';
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
     * The orm manager
     *
     * @var Xyster_Orm_Manager
     */
    protected $_manager;

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
        $this->_manager = new Xyster_Orm_Manager();
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
     * Abandons the current session
     * 
     * This will unload the repository and unset the singleton instance.  The
     * next call to {@link getInstance} will return a new session. 
     */
    public function clear()
    {
        $this->_manager->clear();
        $this->_getWorkUnit()->rollback();
        self::$_instance = null;
    }
    
    /**
     * Commits pending operations to the data store
     * 
     */
    public function commit()
    {
        $wu = $this->_getWorkUnit();
        $repo = $this->_getRepository();
        
        foreach( $repo->getClasses() as $class ) {
            foreach( $repo->getAll($class) as $entity ) {
                if ( $entity->isDirty() ) {
                    try {
                        $wu->registerDirty($entity);
                    } catch ( Xyster_Orm_Exception $thrown ) {
                        // do nothing - the entity was pending delete 
                    }
                }
            }
        }

        $wu->commit($this->_manager);
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
        return $this->_manager->find($className, $criteria);
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
        return $this->_manager->findAll($className, $criteria, $sorts);
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
        return $this->_manager->get($className, $id);
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
        return $this->_manager->getAll($className, $ids);
    }

    /**
     * Gets the factory for entity mappers
     *
     * @return Xyster_Orm_Mapper_Factory_Interface
     */
    public function getMapperFactory()
    {
        return $this->_manager->getMapperFactory();
    }
    
    /**
     * Gets the secondary repository for storing entities
     *
     * @return Zend_Cache_Core
     */
    public function getSecondaryCache()
    {
        return $this->_manager->getSecondaryCache();
    }
    
    /**
     * Sets an entity to be added to the data store
     *
     * @param Xyster_Orm_Entity $entity
     * @throws Xyster_Orm_Exception if the entity is already persisted
     */
    public function persist( Xyster_Orm_Entity $entity )
    {
        $key = $entity->getPrimaryKey(true);
        if ( count($key) && current($key) ) {
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
        
        $query = new Xyster_Orm_Query($className, $this->_manager);
        
        if ( $xsql ) {
            $parser = new Xyster_Orm_Query_Parser($this->getMapperFactory());
            $parser->parseQuery($query, $xsql);
        }
        
        return $query;
    }
    
    /**
     * Refreshes the values of an entity 
     */
    public function refresh( Xyster_Orm_Entity $entity )
    {
        $this->_manager->refresh($entity);
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
        
        $query = new Xyster_Orm_Query_Report($className, $this->_manager);
                
        if ( $xsql ) {
            $parser = new Xyster_Orm_Query_Parser($this->getMapperFactory());
            $parser->parseReportQuery($query, $xsql);
        }
        
        return $query;
    }
    
    /**
     * Sets the factory for entity mappers
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $mapFactory
     * @return Xyster_Orm provides a fluent interface
     */
    public function setMapperFactory( Xyster_Orm_Mapper_Factory_Interface $mapFactory )
    {
        $this->_manager->setMapperFactory($mapFactory);
        return $this;
    }

    /**
     * Sets the secondary repository for storing entities
     *
     * If $repository is null, then no secondary repository is used.
     *
     * @param mixed $repository Either a Cache object, or a string naming a Registry key
     * @return Xyster_Orm provides a fluent interface
     */
    public function setSecondaryCache($repository = null)
    {
        $this->_manager->setSecondaryCache($repository);
        return $this;
    }
    
    /**
     * Makes sure all classes and metadata are defined for a type of entity
     *
     * This method should be called if you want to instantiate a new, blank
     * type of entity and you haven't retrieved any from the data store.  
     * 
     * For instance, if you've used {@link findAll} to pull out a set of
     * entities, the classes should be defined and the metadata should be
     * loaded.  If you haven't done any interaction with the backend yet, it's
     * necessary to call this method. 
     * 
     * @param string $className
     */
    public function setup( $className )
    {
        // this will load the 'entity' and 'mapper' classes, as well as look up
        // the metadata
        $map = $this->getMapperFactory()->get($className);
        // this will load the 'set' class
        $map->getSet();
    }
    
    /**
     * Gets the entity repository
     *
     * @return Xyster_Orm_Repository
     */
    protected function _getRepository()
    {
        return $this->_manager->getRepository();
    }
    
    /**
     * Gets the work unit
     *
     * @return Xyster_Orm_WorkUnit
     */
    protected function _getWorkUnit()
    {
        if ( !$this->_work ) {
            $this->_work = new Xyster_Orm_WorkUnit();
        }
        return $this->_work;
    }
}