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
 * Responsible for translating data store records into entities
 * 
 * Fowler describes a data mapper as "A layer of Mappers that moves data between
 * objects and a database while keeping them independent of each other and the
 * mapper itself". {@link http://www.martinfowler.com/eaaCatalog/dataMapper.html}
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Mapper
{
    /**
     * The type of backend associated with the entity
     * 
     * This will most commonly be SQL, so we'll set that as the default.
     * 
     * @var string
     */
    protected $_backend = "sql";

    /**
     * The type of cache the entity uses
     * 
     * @var string
     */
    protected $_cache = "Request"; // one of Session, Request, Timeout
    
    /**
     * The domain associated with the entity
     * 
     * @var string
     */
    protected $_domain = "";

    /**
     * An array of columns for the entity, will look up by default
     * 
     * @var array
     */
    protected $_fields = array();
    
    /**
     * An array of properties used to index the entity by value
     * 
     * The array consists of index names as keys and arrays of the columns 
     * contained within as values.
     * 
     * <code>array(
     *     'name_index' => array( 'name' ),
     *  'multi_index' => array( 'transactionNumber', 'transactionDate' )
     * );</code>
     *
     * @var array
     */
    protected $_index = array();

    /**
     * Any additional options
     * 
     * <dl>
     * <dt>metadataCache</dt><dd>The name of the Zend_Registry key to find a
     * Zend_Cache_Core object for caching metadata information.  If not
     * specified, the mapper will use the defaultMetadataCache.</dd>
     * </dl>
     *
     * @var array
     */
    protected $_options = array();

    /**
     * The primary key, either a string name, or an array for composite keys
     * 
     * The primary key defaults to the entity name + '_id'
     * 
     * @var mixed
     */
    protected $_primary;

    /**
     * The auto-incrementing sequence
     *
     * @var string
     */
    protected $_sequence;

    /**
     * The name of the table, defaults to entity name
     * 
     * @var string
     */
    protected $_table = "";

    /**
     * @var Xyster_Orm_Backend_Abstract
     */
    private $_backendInstance;

    /**
     * Cache for information provided the backend's getFields method
     *
     * @var Zend_Cache_Core
     */
    protected $_metadataCache;

    /**
     * Default cache for meta information provided by the backend
     *
     * @var Zend_Cache_Core
     */
    protected static $_defaultMetadataCache;
    
    /**
     * @var array
     */
    static private $_mappers = array();
    
    /**
     * Creates a new mapper
     * 
     */
    final public function __construct()
    {
        if ( !$this->_backend ) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('No backend type was specified for ' . get_class($this));
        }
        $this->_metadataCache = ( array_key_exists('metadataCache',$this->_options) ) ?
            self::_setupMetadataCache($this->_options['metadataCache']) :
            self::$_defaultMetadataCache;
    }

    /**
     * Gets the mapper for a given class
     * 
     * @param string $className The name of the entity class
     * @return Xyster_Orm_Mapper The mapper object
     */
    static public function factory( $className )
    {
        if ( !isset(self::$_mappers[$className]) ) {
            
            Xyster_Orm_Loader::loadMapperClass($className);
            self::$_mappers[$className] = new $mapperName();
            self::$_mappers[$className]->init();

        }

        return self::$_mappers[$className];
    }

    /**
     * Gets the default metadata cache for information returned by getFields()
     *
     * @return Zend_Cache_Core
     */
    public static function getDefaultMetadataCache()
    {
        return self::$_defaultMetadataCache;
    }

    /**
     * Sets the default metadata cache for information returned by getFields()
     *
     * If $defaultMetadataCache is null, then no metadata cache is used by
     * default.
     *
     * @param  mixed $metadataCache Either a Cache object, or a string naming a Registry key
     */
    public static function setDefaultMetadataCache($metadataCache = null)
    {
        self::$_defaultMetadataCache = self::_setupMetadataCache($metadataCache);
    }
    
    /**
     * Allows for customization of mapper at construction
     * 
     * Class authors should override this method.
     */
    public function init()
    {
    }

    /**
     * Gets the backend adapter
     * 
     * @return Xyster_Orm_Backend_Abstract
     */
    final public function getBackend()
    {
        if ( !$this->_backendInstance ) {
            $class = "Xyster_Orm_Backend_".ucfirst(strtolower($this->_backend));
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($class);
            $this->_backendInstance = new $class($this);
        }

        return $this->_backendInstance;
    }

    /**
     * Gets the type of caching allowed with this mapper's entities
     *
     * This enum is parsed based on the name in the $_cache property
     *
     * @return Xyster_Orm_Cache The type of caching allowed
     */
    final public function getCache()
    {
        require_once 'Xyster/Enum.php';
        return Xyster_Enum::parse('Xyster_Orm_Cache',$this->_cache);
    }

    /**
     * Gets the name of the domain to which this mapper belongs
     * 
     * @return string  The domain
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Gets the class name of the entity
     * 
     * Class authors should overwrite this method if their entity name isn't
     * the same as the mapper name.
     *
     * @return string  The class name of the entity
     */
    public function getEntityName()
    {
        return substr(get_class($this),0,-6);
    }

    /**
     * Gets an array of the fields belonging to this entity type
     *
     * @return array  The fields (Array of {@link Xyster_Orm_Field} objects)
     */
    public function getFields()
    {
        if ( !count($this->_fields) ) {
            require_once 'Xyster/Orm/Entity/Field.php';
            foreach( $this->getBackend()->getFields() as $name => $values ) {
                $translated = $this->translateField($name);
                $this->_fields[$translated] = new Xyster_Orm_Entity_Field($translated, $values);
            }
        }

        return $this->_fields;
    }

    /**
     * Gets the columns that should be used to index the entity
     * 
     * The array consists of index names as keys and arrays of the columns 
     * contained within as values.
     *
     * @return array
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * Gets the metadata cache
     *
     * @return Zend_Cache_Core
     */
    public function getMetadataCache()
    {
        return $this->_metadataCache;
    }

    /**
     * Gets the options for this mapper
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Gets the field name of the entity primary key
     *
     * @return mixed The primary key field name(s)
     */
    public function getPrimary()
    {
        if ( !$this->_primary ) {
            $this->_primary = $this->getTable()."_id";
        }
        return $this->_primary;
    }

    /**
     * Gets the sequence of this table
     * 
     * @return string The sequence
     */
    public function getSequence()
    {
        return $this->_sequence;
    }

    /**
     * Gets an empty entity set for the mapper's entity type
     *
     * @return Xyster_Orm_Set An empty set
     */
    public function getSet( Xyster_Collection_Interface $entities = null )
    {
        Xyster_Orm_Loader::loadSetClass($this->getEntityName());

        return new $collection($entities);
    }

    /**
     * Gets the table from which an entity comes
     * 
     * It is up to the Xyster_Orm_Backend to do something with this value.
     * 
     * @return string The table name
     */
    public function getTable()
    {
        if ( !$this->_table ) {
            require_once 'Xyster/String.php';
            $this->_table = Xyster_String::toUnderscores($this->getEntityName());
        }
        return $this->_table;
    }

    /**
     * Gets an entity with the supplied identifier
     *
     * @param mixed $id  The id of the entity to get
     * @return Xyster_Orm_Entity  The data entity found, or null if none
     */
    final public function get( $id )
    {
        return $this->getBackend()->findByPrimary($id);
    }

    /**
     * Gets all entities from the data store
     *
     * @param array $ids  An array of ids for which entities to retrieve
     * @return Xyster_Orm_Set  A collection of the entities
     */
    final public function getAll( array $ids = null )
    {
        return ( $ids ) ? $this->getBackend()->findManyByPrimary( $ids ) : 
            $this->getBackend()->findManyByCriteria();
    }

    /**
     * Gets the first entity from the data store matching the criteria
     *
     * @param mixed $criteria
     * @return Xyster_Orm_Entity  The entity found
     */
    final public function find( $criteria ) 
    {
        self::_assertCriteria($criteria);
        return $this->getBackend()->findByCriteria($criteria);
    }

    /**
     * Gets all entities from the data store matching the criteria
     *
     * @param mixed $criteria  
     * @param mixed $sorts
     * @return Xyster_Orm_Set  A collection of the entities
     */
    final public function findAll( $criteria, $sorts = null )
    {
        self::_assertCriteria($criteria);
        return $this->getBackend()->findManyByCriteria($criteria,$sorts);
    }

    /**
     * Refreshes the data of an entity
     *
     * @param Xyster_Orm_Entity $entity  The entity to refresh
     */
    final public function refresh( Xyster_Orm_Entity $entity )
    {
        return $this->getBackend()->refresh($entity);
    }

    /**
     * Saves an entity (insert or update)
     *
     * @param Xyster_Orm_Entity $entity  The entity to save
     */
    public function save( Xyster_Orm_Entity $entity )
    {
        /*
		 * Step 1: Sets ids for any single-entity relationships 
		 */
		foreach( Xyster_Orm_Relation::getAll($entity) as $k=>$v ) {
			if ( !$v->isCollection() && $entity->isLoaded($k) ) {
				$linked = $entity->$k;
				$key = $linked->getPrimaryKey();
				if ( !$key ) {
					Xyster_Orm_Mapper::factory($v->getTo())->save($linked);
					$key = $linked->getPrimaryKey();
				}
				$keyNames = array_keys($key);
				$foreignKey = $v->getId();
				for( $i=0; $i<count($key); $i++ ) {
				    $field = $foreignKey[$i];
				    $keyName = $keyNames[$i];
				    $entity->$field = $linked->$keyName;
				}
			}
		}
        
		/*
		 * Step 2: Save actual entity
		 */
        if ( !$entity->getBase() ) {
            $this->getBackend()->insert($entity);
        } else {
            $this->getBackend()->update($entity);
        }
        
        /*
		 * Step 3: work with many and joined relationships
		 */
		foreach( Xyster_Orm_Relation::getAll($entity) as $k=>$v ) {
			if ( $v->isCollection() && $entity->isLoaded($k) ) {
				$set = $entity->$k;

				$added = $set->getDiffAdded();
				$removed = $set->getDiffRemoved();
				if ( !$added && !$removed ) {
					continue;
				}

				$map = Xyster_Orm_Mapper::factory($v->getTo());
				foreach( $added as $entity ) {
				    $map->save($entity);
				}
				foreach( $removed as $entity ) {
				    $map->delete($entity);
				}
				$set->baseline();
			}
		}
    }

    /**
     * Deletes an entity
     *
     * @param Xyster_Orm_Entity $entity The entity to delete
     */
    public function delete( Xyster_Orm_Entity $entity )
    {
        $key = $entity->getPrimaryKey();

        // build a criterion object based on the primary key(s)
        $criteria = null;
        foreach( $key as $name => $value ) {
            require_once 'Xyster/Data/Expression.php';
            $thiskey = Xyster_Data_Expression::eq($name,$value);
            if ( !$criteria ) {
                $criteria = $thiskey;
            } else if ( $criteria instanceof Xyster_Data_Expression ) {
                require_once 'Xyster/Data/Junction.php';
                $criteria = Xyster_Data_Junction::all( $criteria, $thiskey );
            } else if ( $criteria instanceof Xyster_Data_Junction ) {
                $criteria->add($thiskey);
            }
        }

        $this->getBackend()->delete( $criteria );
    }

    /**
     * 
     * @param string $field
     * @return string
     */    
    public function translateField( $field )
    {
        require_once 'Xyster/String.php';
        return Xyster_String::toCamel($field);
    }

    /**
     * 
     * @param string $field
     * @return string
     */
    public function untranslateField( $field )
    {
        require_once 'Xyster/String.php';
        return Xyster_String::toUnderscores($field);
    }
    
    /**
     * Ensures the parameter passed is either an array or a {@link Xyster_Data_Criterion}
     *
     * @param mixed $criteria
     * @throws Xyster_Orm_Mapper_Exception if the value passed is invalid
     */
    static protected function _assertCriteria( $criteria )
    {
        if ( !is_array($criteria) && 
            ! $criteria instanceof Xyster_Data_Criterion &&
            $criteria !== null ) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Invalid criteria: ' . gettype($criteria) );
        }
    }
	
    /**
     * @param mixed $metadataCache Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Mapper_Exception
     */
    protected static final function _setupMetadataCache($metadataCache)
    {
        if ($metadataCache === null) {
            return null;
        }
        if (is_string($metadataCache)) {
            require_once 'Zend/Registry.php';
            $metadataCache = Zend_Registry::get($metadataCache);
        }
        if (!$metadataCache instanceof Zend_Cache_Core) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $metadataCache;
    }
}