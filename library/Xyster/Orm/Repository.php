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
 * @see Xyster_Collection_Map_String
 */
require_once 'Xyster/Collection/Map/String.php';
/**
 * The "identity map" of persisted entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Repository
{
    /**
     * A container for the entity objects
     * 
     * The array is structured using class names as keys, then another array
	 * with the keys 'byKey', 'byIndex', and 'hasAll'.  hasAll is a boolean, the
	 * other two are {@link Xyster_Collection_Map_String} objects.
	 * 
	 * <code>array(
	 * 		'entityName1' => array(
	 * 			'byKey' => Xyster_Collection_Map_String,
	 * 			'byIndex => Xyster_Collection_Map_String,
	 * 			'hasAll' => false
	 * 		),
	 * 		'entityName2' => array(
	 * 			'byKey' => Xyster_Collection_Map_String,
	 * 			'byIndex => Xyster_Collection_Map_String,
	 * 			'hasAll' => false
	 * 		)
	 * );</code>
     *
     * @var array
     */
    protected $_items = array();
    
    /**
     * The mapper factory
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mapFactory;
    
    /**
     * Creates a new entity repository
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $mapFactory
     */
    public function __construct( Xyster_Orm_Mapper_Factory_Interface $mapFactory )
    {
        $this->_mapFactory = $mapFactory;
    }
    
    /**
     * Adds an entity to the repository
     * 
     * @param Xyster_Orm_Entity $entity
     */
    public function add( Xyster_Orm_Entity $entity )
    {
        $this->_getByKeyMap($entity)->set($this->_stringifyPrimaryKey($entity), $entity);

        /*
         * Add the entity to its appropriate index(es)
         */
        $indexMap = $this->_getByIndexMap($entity);
        foreach( $this->_getMapper($entity)->getIndex() as $index ) {
	        if ( is_array($index) && count($index) ) {
				$hash = array();
				foreach( $index as $name ) {
					$hash[] = $name . '=' . $entity->$name;
				}
				$hash = implode(',', $hash);
				$indexMap->set($hash, $entity);
			}
        }
    }

    /**
     * Adds a set to the repository
     * 
     * @param Xyster_Orm_Set $set
     */
    public function addAll( Xyster_Orm_Set $set )
    {
        foreach( $set as $entity ) {
            $this->add($entity);
        }
    }

    /**
     * Gets whether the repository contains the entity supplied
     * 
     * @param Xyster_Orm_Entity $entity
     * @return boolean
     */
    public function contains( Xyster_Orm_Entity $entity )
    {
        return $this->_getByKeyMap($entity)->containsValue($entity);
    }
    
    /**
     * Finds an entity by indexed value
     * 
     * @param string $class
     * @param array $values
     * @return Xyster_Orm_Entity The entity found or null if none
     */
    public function find( $class, array $values )
    {
        Xyster_Orm_Loader::loadEntityClass($class);
        
        $hash = array();
		foreach( $values as $name => $value ) {
			$hash[] = $name . "=" . $value;
		}
		$hash = implode(',', $hash);

		return $this->_getByIndexMap($class)->get($hash);
    }
    
    /**
     * Gets an item from the repository
     * 
     * @param string $class
     * @param mixed $primaryKey
     * @return Xyster_Orm_Entity The entity found or null if none
     */
    public function get( $class, $primaryKey )
    {
        Xyster_Orm_Loader::loadEntityClass($class);
        return $this->_getByKeyMap($class)
            ->get($this->_stringifyPrimaryKey($primaryKey));
    }
    
    /**
     * Gets the names of classes that are currently stored in this map
     *
     * @return array The names of the classes stored
     */
    public function getClasses()
    {
        return array_keys($this->_items);
    }
    
    /**
     * Gets all entities of a particular type 
     * 
     * @return Xyster_Collection_Interface
     */
    public function getAll( $class )
    {
        Xyster_Orm_Loader::loadEntityClass($class);
        return $this->_getByKeyMap($class)
            ->values();
    }

    /**
     * Gets whether a given class has all of its entities loaded
     * 
     * @param string $class The entity class
     * @return boolean Whether all entities have been loaded
     */
    public function hasAll( $class )
    {
        return array_key_exists($class, $this->_items) &&
            array_key_exists('hasAll', $this->_items[$class]) &&
            $this->_items[$class]['hasAll'];
    }
    
    /**
     * Removes an entity from the repository
     * 
     * @param Xyster_Orm_Entity $entity The entity to remove
     */
    public function remove( Xyster_Orm_Entity $entity )
    {
        $this->_removeEntity($entity);
        $this->setHasAll(get_class($entity), false);
    }

    /**
     * Removes an entity by class and primary key
     * 
     * @param string $class
     * @param mixed $key
     */
    public function removeByKey( $class, $key )
    {
        Xyster_Orm_Loader::loadEntityClass($class);
        $this->_removeByClassAndKey($class, $key);
        $this->setHasAll($class, false);
    }

    /**
     * Removes all entities supplied from the repository
     * 
     * @param Xyster_Orm_Set $set The entities to remove
     */
    public function removeAll( Xyster_Orm_Set $set )
    {
        foreach( $set as $entity ) {
            $this->_removeEntity($entity);
        }
        $this->setHasAll($set->getEntityName(), false);
    }

    /**
     * Removes all entities by class and primary keys
     * 
     * @param string $class
     * @param array $ids
     */
    public function removeAllByKey( $class, array $ids )
    {
        Xyster_Orm_Loader::loadEntityClass($class);
        foreach( $ids as $key ) {
            $this->_removeByClassAndKey($class, $key);
        }
        $this->setHasAll($class, false);
    }
    
    /**
     * Sets whether a given class has all of its entities loaded
     * 
     * @param string $class The entity class
     * @param boolean $hasAll Whether all entities have been loaded
     */
    public function setHasAll( $class, $hasAll = true )
    {
        $this->_items[$class]['hasAll'] = $hasAll;
    }
    
    /**
     * Convenience method to get the map (and create it if necessary)
     * 
     * @param string $class The entity
     * @return Xyster_Collection_Map_String
     */
    protected function _getByKeyMap( $class )
    {
        return $this->_getByHelper($class, 'byKey');
    }

    /**
     * Convenience method to get the map (and create it if necessary)
     * 
     * @param string $class The entity
     * @return Xyster_Collection_Map_String
     */
    protected function _getByIndexMap( $class )
    {
        return $this->_getByHelper($class, 'byIndex');
    }
    
    /**
     * Sets up the array and class name
     *
     * @param mixed $class
     * @param string $key
     * @return string
     */
    protected function _getByHelper( $class, $key )
    {
        if ( is_object($class) ) {
            $class = get_class($class);
        }
        if ( !array_key_exists($class, $this->_items) ) {
            $this->_items[$class] = array();
        }
        if ( !array_key_exists($key, $this->_items[$class]) ) {
            $this->_items[$class][$key] = new Xyster_Collection_Map_String();
        }
        return $this->_items[$class][$key]; 
    }

    /**
     * Convenience method to get the entity's mapper
     * 
     * @return Xyster_Orm_Mapper
     */
    protected function _getMapper( Xyster_Orm_Entity $entity )
    {
        return $this->_mapFactory->get(get_class($entity));
    }

    /**
     * Convenience method to remove an entity
     * 
     * @param string $class
     * @param mixed $key
     */
    protected function _removeByClassAndKey( $class, $key )
    {
        $map = $this->_getByKeyMap($class);
        $entity = $map->get($this->_stringifyPrimaryKey($key));
        if ( $entity ) {
            $this->_removeEntity($entity);
        }
    }

    /**
     * Removes an entity from both maps
     *
     * @param Xyster_Orm_Entity $entity
     */
    protected function _removeEntity( Xyster_Orm_Entity $entity )
    {
        // remove the entity from the index map
        $indexMap = $this->_getByIndexMap($entity);
        foreach( $indexMap->keysFor($entity) as $key ) {
            $indexMap->remove($key);
        }
        
        // remove the entity from the key map
        $keyMap = $this->_getByKeyMap($entity);
        $keyMap->remove($this->_stringifyPrimaryKey($entity));
    }

    /**
     * Convenience method to stringify the primary key
     * 
     * @param mixed $key A primary key or an entity whose primary key is used
     * @return string The primary key as a string
     */
    protected function _stringifyPrimaryKey( $key )
    {
        if ( $key instanceof Xyster_Orm_Entity ) {
        	/* @var $key Xyster_Orm_Entity */
            $key = $key->getPrimaryKey();
        }
        if ( is_array($key) ) {
            $string = array();
            foreach( $key as $k => $value ) {
                $string[] = $k . '=' . $value;
            }
            $key = implode(',', $string);
        }
        return (string) $key;
    }
}