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
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * A data entity: the basic data unit of the ORM package
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity
{
    /**
     * The "base" values from object instantiation
     * 
     * @var array
     */
    protected $_base = array();

    /**
     * For quick modification checking
     *
     * @var boolean
     */
    protected $_dirty = false;
    
    /**
     * A set of event listeners
     *
     * @var Xyster_Collection_Set
     */
    protected $_listeners = null;
    
    /**
     * Looked-up values
     *
     * @var array
     */
    protected $_lookups = array();
    
    /**
     * Related entities or sets
     * 
     * @var array 
     */
    protected $_related = array();

    /**
     * The entity values
     * 
     * @var array
     */
    protected $_values = array();

    /**
     * Entity meta-data (fields, relations, etc.)
     *
     * @var Xyster_Orm_Entity_Type[]
     */
    static private $_types = array();
    
    /**
     * Creates a new entity
     * 
     * @param array $values Values for the entity
     */
    public function __construct( array $values = null )
    {
        if (! $this->_getType() instanceof Xyster_Orm_Entity_Type ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('The metadata for ' . get_class($this) . 'has not been setup');
        }

        foreach( $this->_getType()->getFieldNames() as $name ) {
            $this->_values[$name] = null;
        }

        if ( $values ) {
            $this->import($values);
        }
    }
    
    /**
     * Adds an entity meta information object
     * 
     * This shouldn't be called except by the Xyster_Orm_Mapper
     * 
     * @param Xyster_Orm_Entity_Type $type
     */
    static public function addType( Xyster_Orm_Entity_Type $type )
    {
        self::$_types[ $type->getName() ] = $type;
    }
    
    /**
     * Overloader for getting/setting linked properties
     *
     * @param string $name  The method name
     * @param array $args  Any arguments used
     * @magic
     * @return mixed  The result of the method
     * @throws Xyster_Orm_Entity_Exception if the name is invalid
     */
    public function __call( $name, array $args )
    {
        $action = strtolower(substr($name, 0, 3));
        $field = strtolower($name[3]) . substr($name, 4);
        if ( array_key_exists($field, $this->_values) ) {
            if ( $action == 'get' ) {
                return $this->_values[$field];
            } else if ( $action == 'set' ) {
                $this->_setField($field, $args[0]);
            }
        } else if ( array_key_exists($field, $this->_related) ||
            $this->_getType()->isRelation($field) ) {
            if ( $action == 'get' ) {
                return $this->_getRelated($field);
            } else if ( $action == 'set' ) {
                $this->_setRelated($field, $args[0]);
            }
        } else if ( array_key_exists($field, $this->_lookups) ||
            $this->_getType()->isLookup($field) ) {
            if ( $action == 'get' ) {
                return $this->_getLookup($field);
            } else if ( $action == 'set' ) {
                $this->_setLookup($field, $args[0]);
            }
        } else {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception("'" . $field . "' is not a valid field, relation, or lookup name");
        }
    }

    /**
     * Overloader for getting fields
     * 
     * @magic
     * @param string $name
     * @return mixed The value of the field
     * @throws Xyster_Orm_Entity_Exception if the field is invalid
     */
    public function __get( $name )
    {
        if ( array_key_exists($name, $this->_values) ) {
            return $this->_values[$name];
        } else if ( array_key_exists($name, $this->_related) ||
            $this->_getType()->isRelation($name) ) {
            return $this->_getRelated($name);
        } else if ( array_key_exists($name, $this->_lookups) ||
            $this->_getType()->isLookup($name) ) {
            return $this->_getLookup($name);
        }
        
        require_once 'Xyster/Orm/Entity/Exception.php';
        throw new Xyster_Orm_Entity_Exception("'" . $name . "' is not a valid field, relation, or lookup name");
    }

    /**
     * Overloader for setting fields
     * 
     * @magic
     * @param string $name The field name
     * @param mixed $value The field value
     * @throws Xyster_Orm_Entity_Exception if the field is invalid
     */
    public function __set( $name, $value )
    {
        $this->{'set'.ucfirst($name)}( $value );
    }
    
    /**
     * Adds a listener for value edit events
     * 
     * Listeners can only be registered once.  Trying to add a listener a second
     * time will return false.
     *
     * @param Xyster_Orm_Entity_Listener $listener The listener to add
     * @return boolean Whether the listener was added
     */
    public function addListener( Xyster_Orm_Entity_Listener $listener )
    {
        if ( $this->_listeners === null ) {
            require_once 'Xyster/Collection/Set.php';
            $this->_listeners = new Xyster_Collection_Set;
        }
        return $this->_listeners->add($listener);
    }
    
    /**
     * Removes the listeners
     *
     */
    public function clearListeners()
    {
        $this->_listeners = null;
    }
    
    /**
     * Compares two values for equality
     * 
     * An entity is equal to another entity if they're identical or they're the
     * same class name and have the same primary key.  If the primary key is
     * empty, the actual values are compared.
     *
     * @param Xyster_Orm_Entity $object
     * @return boolean
     */
    public function equals( $object )
    {
        if ( $this === $object ) {
            return true;
        } else {
            $primaryKey = $this->getPrimaryKey();
    		$notEmptyPrimaryKey = count($primaryKey) && current($primaryKey);
	    	return ( is_object($object) &&
                get_class($object) == get_class($this) &&
	    	    ( ( $notEmptyPrimaryKey && $this->getPrimaryKey() == $object->getPrimaryKey() ) || 
	    	    Xyster_Type::areEqual($this->_values, $object->_values) ));
    	}
    }
    
    /**
     * Gets the original values of the entity
     * 
     * @return array
     */
    public function getBase()
    {
        return $this->_base;
    }

    /**
     * Gets the fields that have been changed since the entity was created
     * 
     * @return array
     */
    public function getDirtyFields()
    {
        if ( !$this->_base ) {
            return $this->_values;
        }
        return array_diff_assoc($this->_values, $this->_base);
    }

    /**
     * Gets the primary key of the entity
     * 
     * @param boolean $base True to return the original primary key (if changed)
     * @return mixed An array or scalar key value
     */
    public function getPrimaryKey( $base = false )
    {
        return array_intersect_key((!$base ? $this->_values : $this->_base),
            array_flip($this->_getType()->getPrimary()));
    }

    /**
     * Gets the primary key of the entity as a Xyster_Data_Criterion
     * 
     * @param boolean $base True to return the original primary key (if changed)
     * @return Xyster_Data_Criterion The primary key
     */
    public function getPrimaryKeyAsCriterion( $base = false )
    {
        $key = $this->getPrimaryKey($base);

        // build a criterion object based on the primary key(s)
        $criteria = null;
        foreach( $key as $name => $value ) {
            require_once 'Xyster/Data/Expression.php';
            $thiskey = Xyster_Data_Expression::eq($name, $value);
            if ( !$criteria ) {
                $criteria = $thiskey;
            } else if ( $criteria instanceof Xyster_Data_Expression ) {
                require_once 'Xyster/Data/Junction.php';
                $criteria = Xyster_Data_Junction::all($criteria, $thiskey);
            } else if ( $criteria instanceof Xyster_Data_Junction ) {
                $criteria->add($thiskey);
            }
        }
        
        return $criteria;
    }
    
    /**
     * Gets the primary key of the entity as a string
     *
     * @param boolean $base True to return the original primary key (if changed)
     * @return string A string representation of the primary key
     */
    public function getPrimaryKeyAsString( $base = false )
    {
    	$pk = $this->getPrimaryKey($base);
    	
    	if ( is_array($pk) ) {
	    	$string = array();
	        foreach( $pk as $key => $value ) {
	            $string[] = $key . '=' . $value;
	        }
	        $pk = implode(',', $string); 
    	}
        
    	return $pk;
    }
    
    /**
     * Gets the hash code for the entity
     *
     * @return int
     */
    public function hashCode()
    {
    	return Xyster_Type::hash($this->__toString());
    }
    
    /**
     * Imports the values in the array into the corresponding fields
     * 
     * @param array $values
     */
    public function import( array $values )
    {
        foreach( array_keys($this->_values) as $field ) {
            $this->_values[$field] = array_key_exists($field, $values) ?
                $values[$field] : null;
        }
        $this->_base = $this->_values;
    }

    /**
     * Determines whether or not this entity has changed values since creation
     *
     * @return boolean Whether this entity has changed
     */
    public function isDirty()
    {
        return $this->_dirty;
    }
    
    /**
     * Checks whether a related entity or set has been loaded
     *
     * @param string $name The name of the relation
     * @return boolean true if the relation has been loaded
     * @throws Xyster_Orm_Exception if the relationship name is invalid
     */
    public function isLoaded( $name )
    {
        $this->_getType()->getRelation($name); // to test validity
	    return array_key_exists($name, $this->_related);
    }
    
    /**
     * Removes a specific listener from the entity
     *
     * @param Xyster_Orm_Entity_Listener $listener The listener to remove
     * @return boolean Whether anything was changed
     */
    public function removeListener( Xyster_Orm_Entity_Listener $listener )
    {
        return ( $this->_listeners instanceof Xyster_Collection_Set ) ?
            $this->_listeners->remove($listener) : false;
    }
    
    /**
     * Sets the entity as dirty or clean
     * 
     * This is only used by the transactional layer or associated collections to
     * notify changes in an entity's state.  You shouldn't call this method
     * directly.
     *
     * @param boolean $dirty Whether the entity should be set dirty
     */
    public function setDirty( $dirty = true )
    {
        $this->_dirty = $dirty;
    }
    
    /**
     * Returns an array copy of the entity
     * 
     * @return array The entity values
     */
    public function toArray()
    {
        return $this->_values;
    }

    /**
     * Returns a string value of this entity
     * 
     * @magic
     * @return string
     */
    public function __toString()
    {
        $string = get_class($this) . ' [';
        $first = true;
        foreach( $this->_values as $name => $value ) {
            if ( !$first ) {
                $string .= ',';
            }
            $string .= $name . '=' . $value;
            $first = false;
        }
        return $string . ']';
    }

    /**
     * Gets a lookup value
     *
     * @param string $name The name of the lookup
     * @return mixed The lookup value
     * @throws Xyster_Orm_Entity_Exception if the lookup name is invalid
     */
    protected function _getLookup( $name )
    {
        if ( !array_key_exists($name, $this->_lookups) ) {
            $this->_lookups[$name] = $this->_getType()->getLookup($name)->get($this);
        }
        return $this->_lookups[$name];
    }
    
    /**
     * Gets a related property
     *
     * @param string $name  The name of the property
     * @return mixed  The related Xyster_Orm_Entity or Xyster_Orm_Set
     * @throws Xyster_Orm_Exception if the property name is invalid
     */
    protected function _getRelated( $name )
    {
        if ( !array_key_exists($name, $this->_related) ) {
            $this->_related[$name] = $this->_getType()->getRelation($name)->load($this);
        }
        return $this->_related[$name];
    }
    
    /**
     * The base method for setting fields
     * 
     * If overriding this method, make sure to call the parent or else the
     * entity won't mark itself dirty.
     * 
     * @param string $name
     * @param mixed $value
     */
    protected function _setField( $name, $value )
    {
        $this->_dirty = true;
        if ( $this->_getType()->isValidationEnabled() &&
            !$this->_getType()->isValidateOnSave() ) {
            $this->_getType()->validate($name, $value, true);
        }
        $this->_notifyListeners('field', $name, $this->_values[$name], $value);
        $this->_values[$name] = $value;
    }
    
    /**
     * Sets the lookup value
     *
     * @param string $name
     * @param mixed $value
     * @throws Xyster_Orm_Entity_Exception if the lookup name is incorrect
     * @throws Xyster_Orm_Entity_Exception if the lookup value is incorrect
     */
    protected function _setLookup( $name, $value )
    {
        $this->_getType()->getLookup($name)->set($this, $value);
        // if no exception is thrown, we can assume the value is safe
        $this->_lookups[$name] = $value;
    }
    
    /**
     * Sets a linked property and registers the entity as dirty
     *
     * @param string $name  The name of the property
     * @param mixed $value The new property value
     * @throws Xyster_Orm_Exception if the property name is invalid
     * @throws Xyster_Orm_Exception if the value is incorrect for the property
     */
    protected function _setRelated( $name, $value )
    {
        $info = $this->_getType()->getRelation($name);
        $class = $info->getTo();

        if ( !$info->isCollection() ) {
            if ( $value !== null && ! $value instanceof $class ) {
                require_once 'Xyster/Orm/Exception.php';
                throw new Xyster_Orm_Exception("'" . $name . "' must be an instance of '" . $class . "'");
            }
        } else {
            $setClass = get_class($this->_getType()->getMapperFactory()->get($class)->getSet());
            if (! $value instanceof $setClass ) {
                require_once 'Xyster/Orm/Exception.php';
                throw new Xyster_Orm_Exception("'" . $name . "' must be an instance of '" . $setClass . "'");
            }
        }

        $this->_notifyListeners('relation', $name,
            ($this->isLoaded($name) ? $this->_getRelated($name) : null), $value);
        
        if ( $info->isCollection() ) {

            $value->relateTo($info, $this);

        } else if ( $value === null ) {
            
            $fkeyNames = $info->getId();
            foreach( $fkeyNames as $fkeyName ) {
                $this->{'set'.ucfirst($fkeyName)}(null);
            }
            
        } else if ( $value->getPrimaryKey() ) {
            
            $fkeyNames = $info->getId();
            $key = $value->getPrimaryKey();
            $keyNames = array_keys($key);
            for( $i=0; $i<count($key); $i++ ) {
                $keyValue = $key[ $keyNames[$i] ];
                $fkeyName = $fkeyNames[$i];
                // compare values so the entity isn't marked dirty unnecessarily
                if ( $this->$fkeyName != $keyValue ) {
                    $this->{'set'.ucfirst($fkeyName)}($keyValue);
                }
            }
            
        }

        $this->_related[$name] = $value;
        $this->_dirty = true;
    }
    
    /**
     * Gets the entity type
     * 
     * @return Xyster_Orm_Entity_Type
     */
    protected function _getType()
    {
        $class = get_class($this);
        return array_key_exists($class, self::$_types) ?
            self::$_types[$class] : null; 
    }
    
    /**
     * Notifies any observers of field/relation change events
     *
     * @param string $method Either 'relation' or 'field'
     * @param string $field
     * @param mixed $old
     * @param mixed $new
     */
    private function _notifyListeners( $method, $field, $old, $new )
    {
        $methodName = $method == 'relation' ? 'onSetRelation' : 'onSetField'; 
        if ( $this->_listeners !== null ) {
            foreach( $this->_listeners as $listener ) {
                /* @var $listener Xyster_Orm_Entity_Listener */
                $listener->$methodName($this, $field, $old, $new);
            }
        }
    }
}