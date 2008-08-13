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
 * @see Xyster_Orm_Mapping_Value
 */
require_once 'Xyster/Orm/Mapping/Value.php';
/**
 * @see Xyster_Orm_Mapping_Entity
 */
require_once 'Xyster/Orm/Mapping/Entity.php';
/**
 * A component or composite value
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapping_Component extends Xyster_Orm_Mapping_Value
{
    /**
     * @var Xyster_Type
     */
    protected $_componentType;
    protected $_isKey = false;
    /**
     * @var Xyster_Orm_Mapping_Entity
     */
    protected $_owner;
    protected $_parentProperty;
    protected $_properties = array();
    protected $_roleName;
    /**
     * @var Xyster_Type
     */
    protected $_tuplizerType;
        
    /**
     * Adds a column to the value
     *
     * @param Xyster_Db_Column $column
     * @return Xyster_Orm_Mapping_Value provides a fluent interface
     * @throws Xyster_Orm_Mapping_Exception always
     */
    public function addColumn( Xyster_Db_Column $column )
    {
        require_once 'Xyster/Orm/Mapping/Exception.php';
        throw new Xyster_Orm_Mapping_Exception('Columns cannot be added to components');
    }
    
    /**
     * Adds a property to the component
     *
     * @param Xyster_Orm_Mapping_Property $prop
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function addProperty( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_properties[$prop->getName()] = $prop;
        return $this;
    }
    
    /**
     * Gets the columns in the type
     *
     * @return array containing {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        $cols = array();
        foreach( $this->_properties as $prop ) {
            /* @var $prop Xyster_Orm_Mapping_Property */
            $cols = array_merge($cols, $prop->getColumns());
        }
        return $cols;
    }
    
    /**
     * Gets the number of columns in the value
     *
     * @return int
     */
    public function getColumnSpan()
    {
        $count = 0;
        foreach( $this->_properties as $prop ) {
            /* @var $prop Xyster_Orm_Mapping_Property */
            $count += $prop->getColumnSpan();
        }
        return $count;
    }
    
    /**
     * Gets the type of component
     *
     * @return Xyster_Type
     */
    public function getComponentType()
    {
        return $this->_componentType;
    }
    
    /**
     * Gets the owning entity class
     *
     * @return Xyster_Orm_Mapping_Entity
     */
    public function getOwner()
    {
        return $this->_owner;
    }
    
    /**
     * Gets the name of the property that holds the parent entity
     *
     * @return string
     */
    public function getParentProperty()
    {
        return $this->_parentProperty;
    }
    
    /**
     * Gets the properties in the class
     *
     * @return array of {@link Xyster_Orm_Mapping_Property} objects
     */
    public function getProperties()
    {
        return array() + $this->_properties;
    }
    
    /**
     * Gets a property by name
     *
     * @param string $name
     * @throws Xyster_Orm_Exception if the property isn't found
     * @return Xyster_Orm_Mapping_Property
     */
    public function getProperty( $name )
    {
        if ( !array_key_exists($name, $this->_properties) ) {
            require_once 'Xyster/Orm/Mapping/Exception.php';
            throw new Xyster_Orm_Mapping_Exception('Property not found: ' . $name);
        }
        return $this->_properties[$name];
    }
    
    /**
     * Gets the number of properties in the component
     *
     * @return int
     */
    public function getPropertySpan()
    {
        return count($this->_properties);
    }
    
    /**
     * Gets the role name
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->_roleName;
    }
    
    /**
     * Gets the type of tuplizer used
     *
     * @return Xyster_Type
     */
    public function getTuplizerType()
    {
        return $this->_tuplizerType;
    }
    
    /**
     * Gets the underlying ORM type
     *
     * @return Xyster_Orm_Type_Interface
     */
    public function getType()
    {
    
    }
    
    /**
     * Whether this component is used as a key
     *
     * @return boolean
     */
    public function isKey()
    {
        return $this->_isKey;
    }
    
    /**
     * Sets the type of component generated
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function setComponentType( Xyster_Type $type )
    {
        $this->_componentType = $type;
        return $this;
    }
    
    /**
     * Sets whether this component is used as a key
     *
     * @param boolean $flag
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function setKey( $flag = true )
    {
        $this->_isKey = $flag;
        return $this;
    }
    
    /**
     * Sets the type of owning entity
     *
     * @param Xyster_Orm_Mapping_Entity $owner
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function setOwner( Xyster_Orm_Mapping_Entity $owner )
    {
        $this->_owner = $owner;
        return $this;
    }
    
    /**
     * Sets the name of the property containing the parent entity
     *
     * @param string $name
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function setParentProperty( $name )
    {
        $this->_parentProperty = $name;
        return $this;
    }
    
    /**
     * Sets the role name
     *
     * @param string $name
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function setRoleName( $name )
    {
        $this->_roleName = $name;
        return $this;
    }
    
    /**
     * Sets the type of tuplizer to be used
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Component provides a fluent interface
     */
    public function setTuplizerType( Xyster_Type $type )
    {
        $this->_tuplizerType = $type;
        return $this;
    }
}