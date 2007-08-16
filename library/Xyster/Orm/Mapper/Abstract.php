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
 * @see Xyster_Orm_Loader
 */
require_once 'Xyster/Orm/Loader.php';
/**
 * @see Xyster_Orm_Mapper_Interface
 */
require_once 'Xyster/Orm/Mapper/Interface.php';
/**
 * @see Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';
/**
 * @see Xyster_Orm_Entity_Meta
 */
require_once 'Xyster/Orm/Entity/Meta.php';
/**
 * An abstract implementation of the mapper interface
 * 
 * This class allows for a more simple implementation of the mapper interface,
 * taking care of common logic.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Mapper_Abstract implements Xyster_Orm_Mapper_Interface
{
    /**
     * The domain associated with the entity
     * 
     * @var string
     */
    protected $_domain = "";
    
    /**
     * The factory that created this mapper
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_factory;
    
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
     * The period of time entities should persist in the secondary cache
     * 
     * A value of -1 means the entity shouldn't be added to secondary cache. A
     * value of 0 means the entity should be stored indefinitely.
     * 
     * @var int
     */
    protected $_lifetime = 60;
    
    /**
     * The class meta data
     *
     * @var Xyster_Orm_Entity_Meta
     */
    private $_meta;
    
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
     * The name of the table, defaults to entity name
     * 
     * @var string
     */
    protected $_table = "";

    /**
     * Allows for subclassing without overwriting constructor
     *
     */
    public function init()
    {
    }
    
    /**
     * Deletes an entity
     *
     * @param Xyster_Orm_Entity $entity The entity to delete
     */
    public function delete( Xyster_Orm_Entity $entity )
    {
        $this->_delete( $entity->getPrimaryKeyAsCriterion() );
    }

    /**
     * Gets an entity with the supplied identifier
     *
     * @param mixed $id  The id of the entity to get
     * @return Xyster_Orm_Entity  The data entity found, or null if none
     */
    final public function get( $id )
    {
        $keyNames = $this->getEntityMeta()->getPrimary();
        $keyValues = array();
        
	    if ( count($keyNames) > 1 ) {
	        
    	    $this->_checkPrimaryKey($id);
    	    $keyValues = $id;
	        
	    } else if ( is_array($id) ) {
	        
	        $keyValues = array( current($keyNames) => current($id) );
	        
	    } else {

	        $keyValues = array( $keyNames[0] => $id );

	    }
	    
	    return $this->find($keyValues);
    }
    
    /**
     * Gets the name of the domain to which this mapper belongs
     * 
     * @return string  The domain
     */
    final public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Gets the entity metadata
     *
     * @return Xyster_Orm_Entity_Meta
     */
    final public function getEntityMeta()
    {
        if ( !$this->_meta ) {
            $this->_meta = new Xyster_Orm_Entity_Meta($this);
            if ( !Xyster_Orm_Entity::getMeta($this->getEntityName()) ) {
                Xyster_Orm_Entity::setMeta($this->_meta);
            }
        }
        return $this->_meta;
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
     * Gets the factory that created this mapper
     *
     * @return Xyster_Orm_Mapper_Factory_Interface
     */
    public function getFactory()
    {
        return $this->_factory;
    }
    
    /**
     * Gets the columns that should be used to index the entity
     * 
     * The array consists of index names as keys and arrays of the columns 
     * contained within as values.
     *
     * @return array
     */
    final public function getIndex()
    {
        return $this->_index;
    }

	/**
	 * Gets the time in seconds an entity should be cached
	 *
	 * @return int
	 */
	final public function getLifetime()
	{
		return $this->_lifetime;
	}
    
    /**
     * Gets the value of an option
     *
     * @param string $name The name of the option
     * @return mixed The option value
     */
    final public function getOption( $name )
    {
        return array_key_exists($name, $this->_options) ?
            $this->_options[$name] : null;
    }
    
    /**
     * Gets the options for this mapper
     *
     * @return array
     */
    final public function getOptions()
    {
        return $this->_options;
    }
	
    /**
     * Gets an empty entity set for the mapper's entity type
     *
     * @return Xyster_Orm_Set An empty set
     */
    public function getSet( Xyster_Collection_Interface $entities = null )
    {
        $set = Xyster_Orm_Loader::loadSetClass($this->getEntityName());

        return new $set($entities);
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
     * Saves an entity (insert or update)
     *
     * @param Xyster_Orm_Entity $entity  The entity to save
     */
    public function save( Xyster_Orm_Entity $entity )
    {
        /*
		 * Step 1: Sets ids for any single-entity relationships 
		 */
		foreach( $this->getEntityMeta()->getRelations() as $k=>$v ) {
			if ( !$v->isCollection() && $entity->isLoaded($k) ) {
				$linked = $entity->$k;
				// get the original primary key, in case it's not auto-generated
				$key = $linked->getPrimaryKey(true);
				if ( !count($key) || !current($key) ) {
					$this->_factory->get($v->getTo())->save($linked);
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
            $this->_insert($entity);
        } else {
            $this->_update($entity);
        }
        
        /*
		 * Step 3: work with many and joined relationships
		 */
		foreach( $this->getEntityMeta()->getRelations() as $k=>$v ) {
			if ( $v->isCollection() && $entity->isLoaded($k) ) {
				$set = $entity->$k;

				$added = $set->getDiffAdded();
				$removed = $set->getDiffRemoved();
				if ( !$added && !$removed ) {
					continue;
				}

				$map = $this->_factory->get($v->getTo());
				foreach( $added as $aentity ) {
				    $map->save($aentity);
				}
				foreach( $removed as $rentity ) {
				    $map->delete($rentity);
				}
				$set->baseline();
			}
		}
    }
    
    /**
     * Sets the factory that created the mapper
     * 
     * This should only be called by the factory itself immediately after the
     * mapper is created.  It should throw an exception if the mapper is set
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $factory
     * @throws Xyster_Orm_Mapper_Exception if the factory is already set
     */
    public function setFactory( Xyster_Orm_Mapper_Factory_Interface $factory )
    {
        if ( $this->_factory ) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('The factory for this mapper has already been set');
        }
        $this->_factory = $factory;
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
     * Removes entities from the backend
     *
     * @param Xyster_Data_Criterion $where  The criteria on which to remove entities
     */
    abstract protected function _delete( Xyster_Data_Criterion $where );
    
    /**
     * Saves a new entity into the backend
     *
     * @param Xyster_Orm_Entity $entity  The entity to insert
     * @return mixed  The new primary key
     */
    abstract protected function _insert( Xyster_Orm_Entity $entity );
    
    /**
     * Updates the values of an entity in the backend
     *
     * @param Xyster_Orm_Entity $entity  The entity to update
     */
    abstract protected function _update( Xyster_Orm_Entity $entity );
        
    /**
     * Ensures the parameter passed is a Criterion
     *
     * @param Xyster_Data_Criterion|array $criteria
     * @return Xyster_Data_Criterion
     */
    protected function _buildCriteria( $criteria )
    {
        if ( !is_array($criteria) && 
            ! $criteria instanceof Xyster_Data_Criterion &&
            $criteria !== null ) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Invalid criteria: ' . gettype($criteria) );
        }
        
        $_criteria = null;
        
        if ( is_array($criteria) ) {
            $this->_checkPropertyNames($criteria);
            foreach( $criteria as $name => $value ) {
                require_once 'Xyster/Data/Expression.php';
                $thiskey = Xyster_Data_Expression::eq($name,$value);
                if ( !$_criteria ) {
                    $_criteria = $thiskey;
                } else if ( $_criteria instanceof Xyster_Data_Expression ) {
                    require_once 'Xyster/Data/Junction.php';
                    $_criteria = Xyster_Data_Junction::all( $_criteria, $thiskey );
                } else if ( $_criteria instanceof Xyster_Data_Junction ) {
                    $_criteria->add($thiskey);
                }
            }
        } else {
            $_criteria = $criteria;
        }
        
        return $_criteria;
    }
    
    /**
     * Checks an array for correct primary key names
     *
     * @param mixed $key
     */
    protected function _checkPrimaryKey( $id )
    {
        $keyNames = $this->getEntityMeta()->getPrimary();
        
        if ( (!is_array($id) && count($keyNames) > 1) || count($id) != count($keyNames)) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception("Missing value(s) for the primary key");
        }
        
        if ( !is_array($id) ) {
            $id = array( $keyNames[0] => $id );
        }
        
        foreach( array_keys($id) as $name ) {
            if ( !in_array($name, $keyNames) ) {
                require_once 'Xyster/Orm/Mapper/Exception.php';
                throw new Xyster_Orm_Mapper_Exception("'$name' is not a primary key");
            }
        }
        
        return $id;
    }
    
    /**
     * Asserts the correct property names in a criteria array
     *
     * @param array $criteria
     * @throws Xyster_Orm_Mapper_Exception if one of the field names is invalid
     */
    protected function _checkPropertyNames( array $criteria )
    {
        // get the array of Xyster_Orm_Entity_Field objects
        $fields = $this->getEntityMeta()->getFieldNames();
        
        foreach( $criteria as $k => $v ) { 
            if ( !in_array($k, $fields) ) {
                require_once 'Xyster/Orm/Mapper/Exception.php';
                throw new Xyster_Orm_Mapper_Exception("'" . $k
                    . "' is not a valid field for "
                    . $this->getEntityName() );
            }
        }
    }

    /**
     * Creates an entity from the row supplied and store it in the map
     *
     * @param array $row
     * @return Xyster_Orm_Entity  The entity created
     */
    protected function _create( $row )
    {
        $entityName = $this->getEntityName();
        // this class should already be loaded by the class' mapper
        return new $entityName($row);
    }
}