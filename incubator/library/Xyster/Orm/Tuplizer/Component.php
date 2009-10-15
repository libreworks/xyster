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
 * @see Xyster_Orm_Tuplizer_IComponent
 */
require_once 'Xyster/Orm/Tuplizer/IComponent.php';
/**
 * A component tuplizer
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Tuplizer_Component implements Xyster_Orm_Tuplizer_IComponent
{
    protected $_accessors = array();
    
    /**
     * @var Xyster_Type
     */
    protected $_componentType;
    
    protected $_propertySpan;
    
    /**
     * Creates a new component tuplizer
     *
     * @param Xyster_Orm_Meta_Value_Component $component
     */
    public function __construct( Xyster_Orm_Meta_Value_Component $component )
    {
        $this->_propertySpan = $component->getPropertySpan();
        foreach( $component->getProperties() as $prop ) {
            /* @var $prop Xyster_Orm_Meta_Property */
            $this->_accessors[] = $prop->getWrapper();
        }
        $this->_componentType = $component->getComponentType();
    }
    
    /**
     * Gets the actual class of the component
     * 
     * @return Xyster_Type
     */
    public function getComponentType()
    {
        return $this->_componentType;
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
        foreach( $this->_accessors as $i=>$wrapper ) {
            /* @var $wrapper Xyster_Type_Property_Interface */
            $values[$i] = $wrapper->get($component);
        }
        return $values;
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
     * Injects the values into the supplied component 
     *
     * @param mixed $component
     * @param array $values
     */
    public function setPropertyValues( $component, array $values )
    {
        foreach( $this->_accessors as $i=>$wrapper ) {
            /* @var $wrapper Xyster_Type_Property_Interface */
            $wrapper->set($component, $values[$i]);
        }
    }
}
