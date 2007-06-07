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
 * 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity
{
    /**
     * The entity values
     * 
     * @var array
     */
    protected $_values = array();
    /**
     * The "base" values from object instantiation
     * 
     * @var array
     */
    protected $_base = array();
    /**
     * Related entities or sets
     * 
     * @var array 
     */
    protected $_related = array();
    /**
     * The primary keys
     * 
     * @var array
     */
    protected $_primary = array();

    /**
     * Creates a new entity
     * 
     * @param array $values Values for the entity
     */
    public function __construct( array $values = null )
    {
        foreach( Xyster_Orm_Entity_Meta::getFields($this) as $name => $field ) {
            $this->_values[$name] = null;
            if ( $field->isPrimary() ) {
                $this->_primary[] = $name;
            }
        }
        if ( $values ) {
            $this->import($values);
        }
    }

    /**
     * Imports the values in the array into the corresponding fields
     * 
     * @param array $values
     */
    public function import( array $values )
    {
        foreach( array_keys($this->_values) as $field ) {
            $this->_values[$field] = array_key_exists($field,$values) ?
                $values[$field] : null;
        }
        $this->_base = $values;
    }
	/**
	 * Overloader for getting/setting linked properties
	 *
	 * @param string $name  The method name
	 * @param array $args  Any arguments used
	 * @magic
	 * @return mixed  The result of the method
	 */
	public function __call( $name, array $args )
	{
		$action = strtolower(substr($name,0,3));
		$field = strtolower($name[3]).substr($name,4);
		if ( array_key_exists($field, $this->_values) ) {
			if ( $action == 'get' ) {
				return $this->_values[$field];
			} else if ( $action == 'set' ) {
				$this->_baseSet( $field, $args[0] );
			}
		} else {
		    
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
        if (!array_key_exists($name,$this->_values)) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception("'" . $name . "' is not a valid field");
        }
        return $this->_values[$name];
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
        if (!array_key_exists($name,$this->_values)) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception("'" . $name . "' is not a valid field");
        }
        $this->_baseSet($name,$value);
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
     * Gets the primary key of the entity
     * 
     * @param boolean $base True to return the original primary key (if changed)
     * @return mixed An array or scalar key value
     */
    public function getPrimaryKey( $base = false )
    {
        $primary = array_flip($this->_primary);
        if (!$base) {
            return array_intersect_key($this->_values, $primary);
        } else {
            return array_intersect_key($this->_base, $primary);
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
        $dirty = array();
        foreach( $this->_values as $name => $value ) {
            if ( $this->_base[$name] != $value ) {
                 $dirty[$name] = $value;
            }
        }
        return $dirty;
    }
    
    /**
     * The base method for setting fields
     * 
     * @param string $name
     * @param mixed $value
     */
    protected function _baseSet( $name, $value )
    {
        /**
         * @todo notifications to listeners
         */
        $this->_values[$name] = $value;
    }
	/**
	 * Asserts that the class name passed is a subclass of wfDataEntity
	 *
	 * @param string $className
	 * @throws ORMException if the class isn't a subclass of wfDataEntity
	 */
	static public function assertSubclass( $className )
	{
		if ( !($className instanceof Xyster_Orm_Entity) &&
			!is_subclass_of($className,'Xyster_Orm_Entity') ) {
			require_once 'Xyster/Orm/Entity/Exception.php';
			throw new Xyster_Orm_Entity_Exception("'" . $className . "' is not a subclass of Xyster_Orm_Entity");
		}		
	}
}