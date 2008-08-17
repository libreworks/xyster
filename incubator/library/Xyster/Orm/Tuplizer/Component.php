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
 * @see Xyster_Orm_Tuplizer_Component_Interface
 */
require_once 'Xyster/Orm/Tuplizer/Component/Interface.php';
/**
 * A tuplizer for mapping components
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Tuplizer_Component implements Xyster_Orm_Tuplizer_Component_Interface
{
    /**
     * @var Xyster_Type
     */
    protected $_componentType;
    
    protected $_accessors = array();

    /**
     * @var Xyster_Data_Field_Mapper_Interface
     */
    protected $_parentAccessor;
    
    protected $_propertySpan;
    
    /**
     * Creates a new component tuplizer
     *
     * @param Xyster_Orm_Mapping_Component $component
     */
    public function __construct( Xyster_Orm_Mapping_Component $component )
    {
        $this->_propertySpan = $component->getPropertySpan();
        foreach( $component->getProperties() as $prop ) {
            /* @var $prop Xyster_Orm_Mapping_Property */
            $this->_accessors[] = $prop->getMapper();
        }
        
        $this->_componentType = $component->getComponentType();
        $parentPropertyName = $component->getParentProperty();
        if ( $parentPropertyName !== null ) {
            $this->_parentAccessor = new Xyster_Data_Field_Mapper_Method($parentPropertyName);
        }
    }
    
    /**
     * Return the class managed by this tuplizer
     *
     * @return Xyster_Type
     */
    public function getMappedType()
    {
        return $this->_componentType;
    }
    
    /**
     * Gets the value of the parent property
     *
     * @param mixed $component
     * @return mixed
     */
    public function getParent( $component )
    {
        return $this->_parentAccessor->get($component);
    }
    
    /**
     * Get the value of a specified property from the given entity
     *
     * @param mixed $component
     * @param int $i
     * @return mixed
     */
    public function getPropertyValue( $component, $i )
    {
        return $this->_accessors[$i]->get($component);
    }
    
    /**
     * Get all values on the given entity (essentially, turn into assoc. array)
     *
     * @param mixed $component
     * @return array
     */
    public function getPropertyValues( $component )
    {
        $values = array();
        foreach( $this->_accessors as $i=>$mapper ) {
            /* @var $mapper Xyster_Data_Field_Mapper_Interface */
            $values[$i] = $mapper->get($component);
        }
        return $values;
    }
    
    /**
     * Whether the component managed by this tuplizer have a parent property
     *
     * @return boolean
     */
    public function hasParentProperty()
    {
        return $this->_parentAccessor !== null;
    }
        
    /**
     * Create a new instance of the entity
     *
     * @return mixed
     */
    public function instantiate()
    {
        return $this->_componentType->getClass()->newInstance();
    }
    
    /**
     * Whether the supplied object is an instance of the entity supported
     *
     * @param mixed $component
     */
    public function isInstance( $component )
    {
        return $this->_componentType->isInstance($component);
    }
    
    /**
     * Sets the value of the parent property
     *
     * @param mixed $component
     * @param mixed $parent
     */
    public function setParent( $component, $parent )
    {
        $this->_parentAccessor->set($component, $parent);
    }
            
    /**
     * Injects the values into the supplied component 
     *
     * @param mixed $component
     * @param array $values
     */
    public function setPropertyValues( $component, array $values )
    {
        foreach( $this->_accessors as $i=>$mapper ) {
            /* @var $mapper Xyster_Data_Field_Mapper_Interface */
            $mapper->set($component, $values[$i]);
        }
    }
}