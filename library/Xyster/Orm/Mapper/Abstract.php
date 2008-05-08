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
 * @see Xyster_Orm_Entity_Type
 */
require_once 'Xyster/Orm/Entity/Type.php';
/**
 * Zend_Filter
 */
require_once 'Zend/Filter.php';
/**
 * An abstract implementation of the mapper interface
 * 
 * This class allows for a more simple implementation of the mapper interface,
 * taking care of common logic.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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
     * @var Xyster_Orm_Entity_Type
     */
    private $_meta;
    
    /**
     * Any additional options
     * 
     * <dl>
     * <dt>metadataCache</dt><dd>The name of the Zend_Registry key to find a
     * Zend_Cache_Core object for caching metadata information.  If not
     * specified, the mapper will use the defaultMetadataCache.</dd>
     * <dt>doNotRefreshAfterSave</dt><dd>This will cause the mapper not to
     * refresh the entity after it's inserted or updated.</dd>
     * <dt>locking</dt><dd>The name of the field which holds an integer
     * version number of the record (used to avoid concurrent changes)</dd>
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
     * Creates a new mapper
     * 
     * Class authors can overwrite this, but <em>be sure to call the parent</em>
     * 
     * @param Xyster_Orm_Mapper_Factory_Interface $factory
     */
    public function __construct( Xyster_Orm_Mapper_Factory_Interface $factory )
    {
        $this->_factory = $factory;
        
        $this->getEntityType(); // to assign the meta data to the entity class
        $this->getSet(); // to make sure the class is defined
    }
    
    /**
     * Allows for subclassing without overwriting constructor
     *
     * The mapper factory calls this method.  This is necessary because the init
     * method should contain the setup of relations, which might depend on the
     * mapper that's still being instantiated in the factory. 
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
        $this->_assertThisEntityName($entity);
        
        $manager = $this->_factory->getManager();
        $broker = $manager->getPluginBroker();
        $broker->preDelete($entity);
        $this->_delete($entity->getPrimaryKeyAsCriterion());
        $broker->postDelete($entity);
        
        $relations = $this->getEntityType()->getRelations();
        foreach( $relations as $relation ) { /* @var $relation Xyster_Orm_Relation */
            if ( $relation->getType() == 'many' ) {
                $onDelete = $relation->getOnDelete();
                $map = $this->_factory->get($relation->getTo());
                $name = $relation->getName();
                $reverseName = $relation->hasBelongsTo() ?
                    $relation->getReverse()->getName() : null;
                $related = $entity->$name; /* @var $related Xyster_Orm_Set */
                
                // just remove the association
                if ( $onDelete == Xyster_Orm_Relation::ACTION_SET_NULL && $reverseName ) {
                    foreach( $related as $relatedEntity ) {
                        $relatedEntity->$reverseName = null;
                        $map->save($relatedEntity);
                    }
                // rely on the database to cascade the delete
                } else if ( $onDelete == Xyster_Orm_Relation::ACTION_CASCADE ) {
                    $manager->getRepository()->removeAll($related);
                // we have to delete every last one ourselves
                } else if ( $onDelete == Xyster_Orm_Relation::ACTION_REMOVE ) {
                    $manager->getRepository()->removeAll($related);
                    foreach( $related as $relatedEntity ) {
                        $map->delete($relatedEntity);
                    }
                }
            }
        }
    }

    /**
     * Gets an entity with the supplied identifier
     *
     * @param mixed $id  The id of the entity to get
     * @return Xyster_Orm_Entity  The data entity found, or null if none
     */
    final public function get( $id )
    {
        $keyNames = $this->getEntityType()->getPrimary();
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
     * @return Xyster_Orm_Entity_Type
     */
    final public function getEntityType()
    {
        if ( !$this->_meta ) {
            $this->_meta = new Xyster_Orm_Entity_Type($this);
            Xyster_Orm_Entity::setMeta($this->_meta);
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
            $this->_table = strtolower(Zend_Filter::get($this->getEntityName(),
                'Word_CamelCaseToUnderscore'));
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
        $this->_assertThisEntityName($entity);
        
        /*
		 * Step 1: Sets ids for any single-entity relationships 
		 */
		foreach( $this->getEntityType()->getRelations() as $k=>$v ) {
		    /* @var $v Xyster_Orm_Relation */
			if ( !$v->isCollection() && $entity->isLoaded($k) && $entity->$k !== null ) {
				$linked = $entity->$k; /* @var $linked Xyster_Orm_Entity */
				// get the original primary key, in case it's not auto-generated
				$key = $linked->getPrimaryKey(true);
				if ( !$linked->getBase() ) {
					$this->_factory->get($v->getTo())->save($linked);
					$key = $linked->getPrimaryKey();
				} else if ( $key != $linked->getPrimaryKey() ) {
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
		
		$broker = $this->_factory->getManager()->getPluginBroker();
		$updatedKey = false;
		/*
		 * Step 2: Save actual entity
		 */
        if ( !$entity->getBase() ) {
            $broker->preInsert($entity);
            $this->_insert($entity);
            $broker->postInsert($entity);
        
        } else {
            $updatedKey = $entity->getPrimaryKey() != $entity->getPrimaryKey(true);
            $broker->preUpdate($entity);
            $this->_update($entity);
            $broker->postUpdate($entity);
        }
        
        // this is in case any triggers in the db, etc. have changed the record
        if ( !$this->getOption('doNotRefreshAfterSave') ) {
    	   $this->refresh($entity);
        }
    	$entity->setDirty(false);
    	
        /*
		 * Step 3: work with many and joined relationships
		 */
		foreach( $this->getEntityType()->getRelations() as $k=>$relation ) {
		    /* @var $relation Xyster_Orm_Relation */
            if ( $relation->isCollection() && ( $updatedKey
			    || $entity->isLoaded($k) ) ) {
			        
				$set = $entity->$k;
				$cascadeUpdate = $updatedKey &&
				    $relation->getOnUpdate() == Xyster_Orm_Relation::ACTION_CASCADE;

				$added = $set->getDiffAdded();
				$removed = $set->getDiffRemoved();
				if ( !$added && !$removed && !$cascadeUpdate ) {
					continue;
				}

				if ( $relation->getType() == 'joined' ) {
				    
				    $this->_joinedInsert($set);
				    $this->_joinedDelete($set);
				    
				} else if ( $relation->getType() == 'many' ) {

    				$map = $this->_factory->get($relation->getTo());
    				if ( $cascadeUpdate ) {
    				    // if we should cascade changed primary keys
    				    foreach( $set as $setEntity ) {
    				        $map->save($setEntity);
    				    }
       				} else {
    				    // no cascade, just save newly added to set
    				    array_walk($added, array($map, 'save'));
    				}
    				array_walk($removed, array($map, 'delete'));
    				
				}

				$set->baseline();
			}
		}
    }
    
    /**
     * 
     * @param string $field
     * @return string
     */    
    public function translateField( $field )
    {
        $field = Zend_Filter::get($field, 'Word_UnderscoreToCamelCase');
        return strtolower($field[0]) . substr($field, 1);
    }

    /**
     * 
     * @param string $field
     * @return string
     */
    public function untranslateField( $field )
    {
        return strtolower(Zend_Filter::get($field, 'Word_CamelCaseToUnderscore'));
    }
    
    /**
     * Removes entities from the backend
     *
     * @param Xyster_Data_Criterion $where  The criteria on which to remove entities
     * @return int The number of rows deleted
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
     * Adds the entity to the many-to-many join
     *
     * @param Xyster_Orm_Set $set
     */
    abstract protected function _joinedInsert( Xyster_Orm_Set $set );
    
    /**
     * Removes the entity from the many-to-many join
     *
     * @param Xyster_Orm_Set $set
     */
    abstract protected function _joinedDelete( Xyster_Orm_Set $set );
    
    /**
     * Updates the values of an entity in the backend
     *
     * Class authors must remember to implement optimistic offline locking in
     * this method.  See the {@link Xyster_Orm_Mapper::_update} method for an 
     * example.
     * 
     * @param Xyster_Orm_Entity $entity The entity to update
     * @throws Xyster_Orm_Mapper_Exception if the record was modified or deleted 
     */
    abstract protected function _update( Xyster_Orm_Entity $entity );
    
    /**
     * A convenience method to assert entity type
     *
     * @param Xyster_Orm_Entity $entity
     * @throws Xyster_Orm_Mapper_Exception if the entity supplied is of the wrong type
     */
    final protected function _assertThisEntityName( Xyster_Orm_Entity $entity )
    {
        $name = $this->getEntityName();
        if ( ! $entity instanceof $name ) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('This mapper only accepts entities of type ' . $name);
        }
    }

    /**
     * Convenience method to create a 'belongs' relationship
     * 
     * @see Xyster_Orm_Entity_Type::belongsTo
     * @param string $name The name of the relationship
     * @param array $options An array of options
     * @return Xyster_Orm_Mapper_Abstract provides a fluent interface
     */
    final protected function _belongsTo( $name, array $options = array() )
    {
        $this->getEntityType()->belongsTo($name, $options);
        return $this;
    }
    
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
            throw new Xyster_Orm_Mapper_Exception('Invalid criteria: ' . gettype($criteria));
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
        $keyNames = $this->getEntityType()->getPrimary();
        
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
        $fields = $this->getEntityType()->getFieldNames();
        
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
     * Creates an entity from the row supplied
     * 
     * If the row has already been loaded and the entity that represents the row
     * is in the repository, this method will return that exact instance instead
     * of creating a new one.
     *
     * @param array $row
     * @return Xyster_Orm_Entity  The entity created
     */
    protected function _create( $row )
    {
        $entityName = $this->getEntityName();
        // this class should already be loaded by the class' mapper
        
        $manager = $this->_factory->getManager();
        $primary = array_intersect_key($row,
            array_flip($this->getEntityType()->getPrimary()));
        $loaded = $manager->getFromCache($entityName, $primary);
        if ( $loaded instanceof Xyster_Orm_Entity ) {
            return $loaded;
        }
        
        $entity = new $entityName($row);
        $manager->getPluginBroker()->postLoad($entity);
        return $entity;
    }
    
    /**
     * Convenience method to create a 'many to many' relationship
     * 
     * @see Xyster_Orm_Entity_Type::hasJoined
     * @param string $name The name of the relationship
     * @param array $options An array of options
     * @return Xyster_Orm_Mapper_Abstract provides a fluent interface
     */
    final protected function _hasJoined( $name, array $options = array() )
    {
        $this->getEntityType()->hasJoined($name, $options);
        return $this;
    }
    
    /**
     * Convenience method to create a 'one to many' relationship 
     * 
     * @see Xyster_Orm_Entity_Type::hasMany
     * @param string $name The name of the relationship
     * @param array $options An array of options
     * @return Xyster_Orm_Mapper_Abstract provides a fluent interface 
     */ 
    final protected function _hasMany( $name, array $options = array() )
    {
        $this->getEntityType()->hasMany($name, $options);
        return $this;
    }
        
    /**
     * Convenience method to create a 'one to one' relationship
     * 
     * @see Xyster_Orm_Entity_Type::hasOne
     * @param string $name The name of the relationship
     * @param array $options An array of options
     * @return Xyster_Orm_Mapper_Abstract provides a fluent interface
     */
    final protected function _hasOne( $name, array $options = array() )
    {
        $this->getEntityType()->hasOne($name, $options);
        return $this;
    }
}