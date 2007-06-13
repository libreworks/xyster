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
 * @see Xyster_Collection_Set
 */
require_once 'Xyster/Collection/Set.php';
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
	 * Setting for entity cache lifetime in seconds
	 * 
	 * It is set to 30 seconds by default
	 *
	 * @var int
	 */
	static private $_lifetime = 30;
    /**
     * Secondary cache for entities by primary key
     *
     * @var Zend_Cache_Core
     */
    protected static $_secondaryRepository = null;
    
    /**
     * Enable session persistence of Repository?
     * 
     * @var boolean
     */
    static protected $_session = false;
    
    /**
     * Paths where entities, mappers, and sets are stored
     * 
     * @var array
     */
    static protected $_paths = array();
    	
	/**
	 * The singleton instance of this class
	 * 
	 * @var Xyster_Orm
	 */
	static protected $_instance;
	
	/**
	 * The entity repository (identity map)
	 *
	 * @var Xyster_Orm_Repository
	 */
	protected $_repository;
	/**
	 * Entities that are in queue for insertion
	 *
	 * @var Xyster_Collection_Set
	 */
	protected $_new;
	/**
	 * Entities that are in queue for update
	 *
	 * @var Xyster_Collection_Set
	 */
	protected $_dirty;
	/**
	 * Entities that are in queue for removal
	 *
	 * @var Xyster_Collection_Set
	 */
	protected $_removed;

	/**
	 * Creates a new Xyster_Orm object
	 */
	protected function __construct()
	{
	    // set up the repository
	    if ( self::$_session ) {
	        
	        /*
	         * If session persistence is turned on, try to load from the session
	         */
	        require_once 'Zend/Session/Namespace.php';
	        $session = new Zend_Session_Namespace('Xyster_Orm');
	        if ( $session->repository instanceof Xyster_Orm_Repository ) {
	            $this->_repository = $session->repository;
	        } else {
	            $this->_repository = new Xyster_Orm_Repository();
	            $session->repository = $this->_repository;
	        }
	        
	    } else {
	        
	        $this->_repository = new Xyster_Orm_Repository();
	        
	    }

	    $this->_new = new Xyster_Collection_Set();
		$this->_dirty = new Xyster_Collection_Set();
		$this->_removed = new Xyster_Collection_Set();
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
     * Please note that if session persistence is enabled (via
     * {@link persistRepository()}), calling this method WILL start the session
     * if it hasn't already been started.
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
	 * Gets the time in seconds an entity should by cached
	 *
	 * @return int
	 */
	static public function getLifetime()
	{
		return self::$_lifetime;
	}
	/**
	 * Sets the time in seconds an entity should be cached
	 * 
	 * @param int $seconds
	 */
	static public function setLifetime( $seconds )
	{
		self::$_lifetime = intval($seconds);
	}

    /**
     * Sets the secondary repository for storing entities
     *
     * If $repository is null, then no secondary repository is used 
     *
     * @param  mixed $metadataCache Either a Cache object, or a string naming a Registry key
     */
    public static function setSecondaryRepository($repository = null)
    {
        self::$_secondaryRepository = self::_setupRepository($repository);
    }

    /**
     * Gets the default metadata cache for information returned by getFields()
     *
     * @return Zend_Cache_Core
     */
    public static function getSecondaryRepository()
    {
        return self::$_secondaryRepository;
    }
	
    /**
     * Whether to persist the repository in the user's session
     * 
     * Keep in mind that turning this on means that Xyster_Orm must be declared,
     * the entity paths must be added via {@link addPath}, and the
     * {@link autoload} method should be registered to be called by an
     * autoload method all BEFORE the session is even started.
     * 
     * <code>
     * require_once 'Xyster/Orm.php';
     * 
     * Xyster_Orm::addPath( '/path/to/my/entities' );
     * 
     * Xyster_Orm::registerAutoload();
     * 
     * Xyster_Orm::persistRepository();
     * 
     * // starts the session, unserializes the repository
     * $orm = Xyster_Orm::getInstance();
     * </code>
     * 
     * Use this option only if you fully understand the implications.
     *
     * @param boolean $persist
     */
    public static function persistRepository( $persist = true )
    {
        self::$_session = $persist;
    }
    
    /**
     * Adds a path to where class files for entities can be found
     *
     * @param string $path
     */
    public static function addPath( $path )
    {
        $path        = rtrim($path, '/');
        $path        = rtrim($path, '\\');
        $path       .= DIRECTORY_SEPARATOR;
        
        if ( @!is_dir($path) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception("The path '$path' does not exist'");
        }
        
        self::$_paths[$path] = $path; // no need for dups
    }

    /**
     * Tries to load the class in one of the paths defined for entities
     *
     * @param string $className
     * @return string
     */
    public static function loadClass( $className )
    {
        $dirs = self::$_paths;
        $file = $className . '.php';
        
        try {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadFile($file, $dirs, true);
        } catch (Zend_Exception $e) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Cannot load class "' . $className . '"');
        }

        if (!class_exists($className,false)) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Invalid class ("' . $className . '")');
        }

        return $className;
    }
    
    /**
     * spl_autoload() suitable implementation for supporting class autoloading.
     *
     * Attach to spl_autoload() using the following:
     * <code>
     * spl_autoload_register(array('Xyster_Orm', 'autoload'));
     * </code>
     * 
     * @param string $class 
     * @return mixed string class name on success; false on failure
     */
    public static function autoload($class)
    {
        try {
            self::loadClass($class);
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Register {@link autoload()} with spl_autoload()
     * 
     * @throws Zend_Exception if spl_autoload() is not found or if the specified class does not have an autoload() method.
     */
    public static function registerAutoload()
    {
        require_once 'Zend/Loader.php';
        Zend_Loader::registerAutoload('Xyster_Orm');
    }
    
    /**
     * @param mixed $repository Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Exception
     */
    protected static final function _setupRepository($repository)
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
    
	/**
	 * Commits pending operations to the data store
	 * 
	 */
	public function commit()
	{
	    /*
	     * Save all new entities
	     */
		foreach( $this->_new as $v ) {
			$this->getMapper(get_class($v))->save($v);
			$this->_repository->add($v);
		}
		$this->_new->clear();
		
		/*
		 * Update any existing entities
		 */
		foreach( $this->_dirty as $v ) {
			$this->getMapper(get_class($v))->save($v);
		}
		$this->_dirty->clear();
		
		/*
		 * Delete entities to be removed
		 */
		foreach( $this->_removed as $v ) {
			$this->getMapper(get_class($v))->delete($v);
			$this->_repository->remove($v);
		}
		$this->_removed->clear();
	}
	/**
	 * Sets an entity to be removed
	 *
	 * @param Xyster_Orm_Entity $entity
	 */
	public function delete( Xyster_Orm_Entity $entity )
	{
	    if ( $this->_new->contains($entity) )  {
			$this->_new->remove($entity);
			return;
	    }
		if ( $this->_dirty->contains($entity) ) {
			$this->_dirty->remove($entity);
		}
		$this->_removed->add($entity);
	}
	/**
	 * Abandons the current session
	 * 
	 * This will unload the repository and unset the singleton instance.  The
	 * next call to {@link getInstance} will return a new session. 
	 */
	public function destroy()
	{
	    $this->_repository = null;
	    $this->_dirty->clear();
	    $this->_new->clear();
	    $this->_removed->clear();
	    self::$_instance = null;
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
        if ( is_scalar($id) ) {
	        $keyNames = (array) $this->getMapper($className)->getPrimary();
	        $id = array( $this->getMapper($className)->translateField($keyNames[0]) => $id );
	    }
	    
	    $entity = $this->getRepository()->get($className,$id);
	    if ( $entity ) {
			return $entity;
	    }

	    $entity = $this->_getFromSecondaryRepository($className,$id);
		if ( $entity ) {
		    return $entity;
		}
		
	    $map = $this->getMapper($className);
		$entity = $map->get($id); 
		if ( $entity ) {
			$this->getRepository()->add($entity);
			$this->_putInSecondaryRepository($entity);
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
	    $map = $this->getMapper($className);
	    
	    if ( is_array($ids) && count($ids) ) {
	        // we're getting a few entities by primary key

			if ( $this->_repository->hasAll($className) ) {
			    $keyNames = (array) $this->getMapper($className)->getPrimary();
				$all = $map->getSet();
				foreach( $ids as $id ) {
				    if ( is_scalar($id) ) {
			            $id = array( $this->getMapper($className)->translateField($keyNames[0]) => $id );
				    }
				    $entity = $this->_repository->get($className,$id);
				    if ( $entity ) {
					    $all->add( $entity );
				    } else {
				        $entity = $this->_getFromSecondaryRepository($className,$id);
				        if ( $entity ) {
				            $all->add($entity);
				        }
				    }
				}
			} else {
				$all = $map->getAll($ids);
				$this->_repository->addAll($all);
			    if ( $map->getCache() !== Xyster_Orm_Cache::Request() ) {
				    foreach( $all as $entity ) {
				        $this->_putInSecondaryRepository($entity);
				    }
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
				if ( $map->getCache() !== Xyster_Orm_Cache::Request() ) {
				    foreach( $all as $entity ) {
				        $this->_putInSecondaryRepository($entity);
				    }
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
		    
			$map = $this->getMapper($className);
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
	    $all = $this->getMapper($className)->findAll($criteria,$sorts);
		if ( count($all) ) {
			$this->_repository->addAll($all);
		}
		return $all;
	}
	/**
	 * Gets the entity repository in use by the ORM session
	 *
	 * @return Xyster_Orm_Repository
	 */
	public function getRepository()
	{
	    return $this->_repository;
	}
	/**
	 * Gets a data mapper for a given entity type
	 *
	 * @param string $className  The type of entity mapper to return
	 * @return Xyster_Orm_Mapper
	 */
	public function getMapper( $className )
	{
		return Xyster_Orm_Mapper::factory($className);
	}
	/**
	 * Refreshes the values of an entity 
	 */
	public function refresh( Xyster_Orm_Entity $entity )
	{
	    $this->getMapper(get_class($entity))->refresh($entity);
	}
	/**
	 * Refreshes dirty entities and clears pending operations 
	 *
	 */
	public function rollBack()
	{
	    foreach( $this->_dirty as $entity ) {
	        $this->refresh($entity);
	    }
	    $this->_dirty->clear();
	    $this->_removed->clear();
	    $this->_new->clear();
	}
	/**
	 * Sets an entity to be added to the data store
	 *
	 * @param Xyster_Orm_Entity $entity
	 * @throws Xyster_Orm_Exception if the entity is already persisted
	 */
	public function persist( Xyster_Orm_Entity $entity )
	{
	    if ( $this->_dirty->contains($entity) ) {
			require_once 'Xyster/Orm/Exception.php';
			throw new Xyster_Orm_Exception('This entity is already persisted');
		}
		if ( $this->_removed->contains($entity) ) {
			require_once 'Xyster/Orm/Exception.php';
			throw new Xyster_Orm_Exception('This entity is already persisted');
		}
		if ( $this->_new->contains($entity) ) {
			require_once 'Xyster/Orm/Exception.php';
			throw new Xyster_Orm_Exception('This entity is already persisted');
		}
		$this->_new->add($entity);
	}
	/**
	 * Sets an entity to have changes added back to the data store
	 *
	 * @param Xyster_Orm_Entity $entity
	 * @throws Xyster_Orm_Exception if the entity is not persisted or in queue for removal
	 */
	public function update( Xyster_Orm_Entity $entity )
	{
	    if ( !$entity->getPrimaryKey() ) {
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
	 * Gets an entity from the secondary repository
	 *
	 * @param string $className
	 * @param array $id
	 * @return Xyster_Orm_Entity the entity found or null if none
	 */
	protected function _getFromSecondaryRepository( $className, $id )
	{
	    $repo = self::getSecondaryRepository();
	    if ( $repo ) {
	        $repoId = array( 'Xyster_Orm',
	            $this->getMapper($className)->getDomain(), $className );
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
	protected function _putInSecondaryRepository( Xyster_Orm_Entity $entity )
	{
	    $repo = self::getSecondaryRepository();
	    $className = get_class($entity);
	    $map = $this->getMapper($className);
	    $cache = $map->getCache();

	    // only store the entity if it should be cached longer than the request
	    // that's why we have the primary repository
	    if ( $repo && $cache !== Xyster_Orm_Cache::Request() ) {
	        
	        $repoId = array( 'Xyster_Orm', $map->getDomain(), $className );
	        foreach( $entity->getPrimaryKey() as $key => $value ) {
	            $repoId[] = $key . '=' . $value;
	        }
            $repoId = md5(implode("/",$repoId));
            
            $lifetime = ( $cache === Xyster_Orm_Cache::Session() ) ?
                null : self::getLifetime() + 60;

            $repo->save( $repoId, $entity, null, $lifetime );
	    }
	}
}