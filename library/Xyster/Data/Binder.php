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
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Data_Binder_Setter
 */
require_once 'Xyster/Data/Binder/Setter.php';
/**
 * A mediator that applies values to a target object
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Binder
{
    protected $_target;
    
    protected $_allowed = array();
    
    protected $_disallowed = array();
    
    protected $_setters = array();
    
    /**
     * @var Xyster_Data_Binder_Setter
     */
    protected $_defaultSetter;
    
    /**
     * Creates a new binder
     *
     * @param object $target
     */
    public function __construct( $target )
    {
        if ( !is_object($target) ) {
            require_once 'Xyster/Data/Binder/Exception.php';
            throw new Xyster_Data_Binder_Exception('Only objects can be supplied as a target');
        }
        $this->_target = $target;
        $this->_defaultSetter = new Xyster_Data_Binder_Setter;
    }
        
    /**
     * Adds a setter to handle a type and property
     * 
     * Setting the property argument to null will apply the setter to all fields 
     * 
     * @param Xyster_Data_Binder_Setter_Interface $setter The setter to add
     * @param mixed $property Optional. A property name to which the setter applies.
     * @return Xyster_Data_Binder provides a fluent interface
     */
    public function addSetter( Xyster_Data_Binder_Setter_Interface $setter, $property = null )
    {
        if ( $property === null ) {
            $this->_defaultSetter = $setter;
        } else {
            $this->_setters[$property] = $setter;
        } 
        return $this;
    }
    
    /**
     * Binds the values in the array to the target
     *
     * @param array $values
     */
    public function bind( array $values )
    {
        foreach( $values as $name => $value ) {
            $setter = $this->_getSetter($name);
            $setter->set($this->_target, $name, $value);
        }
    }
    
    /**
     * Gets the allowed fields
     *
     * @return array
     */
    public function getAllowedFields()
    {
        return array() + $this->_allowed;
    }
    
    /**
     * Gets the disallowed fields
     *
     * @return array
     */
    public function getDisallowedFields()
    {
        return array() + $this->_disallowed;
    }
    
    /**
     * Gets the target object
     *
     * @return object
     */
    public function getTarget()
    {
        return $this->_target;
    }
    
    /**
     * Tests if a field is allowed
     *
     * @param string $field
     * @return boolean
     */
    public function isAllowed( $field )
    {
        return count($this->_allowed) > 0 ? in_array($field, $this->_allowed) :
            !in_array($field, $this->_disallowed);
    }
    
    /**
     * Sets the allowed fields
     *
     * @param array $fields
     * @return Xyster_Data_Binder provides a fluent interface
     */
    public function setAllowedFields( array $fields ) 
    {
        $this->_allowed = $fields;
        return $this;
    }
    
    /**
     * Sets the disallowed fields
     * 
     * For security reasons, it is much more preferable to set the allowed
     * fields explicitly using {@link setAllowedFields}.
     *
     * @param array $fields
     * @return Xyster_Data_Binder provides a fluent interface
     */
    public function setDisallowedFields( array $fields )
    {
        $this->_allowed = array();
        $this->_disallowed = $fields;
        return $this;
    }
    
    /**
     * Gets a setter for the field supplied
     *
     * @param string $field The field name
     * @return Xyster_Data_Binder_Setter_Interface
     */
    protected function _getSetter( $field )
    {
        return isset($this->_setters[$field]) ? $this->_setters[$field] : 
            $this->_defaultSetter;
    }
}