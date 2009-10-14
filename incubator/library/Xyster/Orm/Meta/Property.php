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
 * A field on an entity or component.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Meta_Property
{
    /**
     * @var boolean
     */
    protected $_lazy = false;
    
    /**
     * @var string
     */
    protected $_name;
    
    /**
     * @var boolean
     */
    protected $_natId = false;
    
    /**
     * @var Xyster_Orm_Meta_IValue
     */
    protected $_value;
    
    /**
     * @var Xyster_Type_Property_Interface
     */
    protected $_wrapper;

    /**
     * Creates a new Property
     * 
     * @param string $name The name of this property
     * @param Xyster_Orm_Meta_IValue $value The value contained in this property
     * @param Xyster_Type_Property_Interface $wrapper The property getter/setter.
     * @param boolean $naturalId Optional. Whether this property is part of a natural identifier
     * @param boolean $lazy Optional. Whether this property is lazy-loaded
     */
    public function __construct($name, Xyster_Orm_Meta_IValue $value, Xyster_Type_Property_Interface $wrapper, $naturalId = false, $lazy = false)
    {
        $this->_name = $name;
        $this->_value = $value;
        $this->_wrapper = $wrapper;
        $this->_natId = $naturalId;
        $this->_lazy = $lazy;
    }
    
    /**
     * Gets the columns in the property
     *
     * @return Traversable containing {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        return $this->_value->getColumns();
    }
    
    /**
     * Gets the number of columns in the property
     *
     * @return int
     */
    public function getColumnSpan()
    {
        return $this->_value->getColumnSpan();
    }
    
    /**
     * Gets the name of the property
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the type of the property
     *
     * @return Xyster_Orm_Type_Interface
     */
    public function getType()
    {
        return $this->_value->getType();
    }
    
    /**
     * Gets the value of the property
     *
     * @return Xyster_Orm_Mapping_Value_Interface
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Gets a getter/setter for this property
     *
     * @return Xyster_Type_Property_Interface
     */
    public function getWrapper()
    {
        return $this->_wrapper;
    }
        
    /**
     * Gets whether this property is lazy-loaded
     *
     * @return boolean
     */
    public function isLazy()
    {
        return $this->_lazy;
    }

    /**
     * Whether the property is a natural identifier
     * 
     * @return boolean
     */
    public function isNaturalId()
    {
        return $this->_natId;
    }
    
    /**
     * Whether this property is nullable
     *
     * @return boolean
     */
    public function isNullable()
    {
        return $this->_value->isNullable();
    }
}