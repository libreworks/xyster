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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Meta_Value_Basic
 */
require_once 'Xyster/Orm/Meta/Value/Basic.php';
/**
 * A combination of an ORM Type and one or more Columns.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Meta_Value_Component extends Xyster_Orm_Meta_Value_Basic
{
    /**
     * @var Xyster_Type
     */
    protected $_componentType;
    protected $_properties = array();
    
    /**
     * Creates a component value
     * 
     * @param Xyster_Db_Table $table The table to which this value belongs.
     * @param Xyster_Type $type The actual class of the component
     * @param array $properties Array of {@link Xyster_Orm_Meta_Property} objects
     */    
    public function __construct(Xyster_Db_Table $table, Xyster_Type $type, array $properties)
    {
        $this->_table = $table;
        $this->_type = $type;
        foreach( $properties as $prop ) {
            $this->_properties[$prop->getName()] = $prop;
        }
    }
    
    /**
     * Gets the columns in the type
     *
     * @return Traversable containing {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        $iters = new AppendIterator();
        foreach( $this->_properties as $prop ) {
            /* @var $prop Xyster_Orm_Meta_Property */
            $iters->append($prop->getColumnIterator());
        }
        return $iters;
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
            /* @var $prop Xyster_Orm_Meta_Property */
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
     * Gets the properties in the class
     *
     * @return Traversable containing {@link Xyster_Orm_Meta_Property} objects
     */
    public function getProperties()
    {
        return new ArrayIterator($this->_properties);
    }
    
    /**
     * Gets a property by name
     *
     * @param string $name
     * @throws Xyster_Orm_Exception if the property isn't found
     * @return Xyster_Orm_Meta_Property
     */
    public function getProperty( $name )
    {
        if ( !array_key_exists($name, $this->_properties) ) {
            require_once 'Xyster/Orm/Meta/Exception.php';
            throw new Xyster_Orm_Meta_Exception('Property not found: ' . $name);
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
}