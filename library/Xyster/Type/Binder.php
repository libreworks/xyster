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
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Type;
/**
 * A mediator that applies values to a target object
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Binder
{
    protected $_target;
    
    protected $_allowed = array();
    
    protected $_disallowed = array();
    
    protected $_setters = array();
    
    /**
     * @var Type
     */
    protected $_default;
    
    /**
     * Creates a new binder
     *
     * @param object $target
     * @param Type $defaultSetter  Optional. Must inherit from \Xyster\Type\Property\IProperty
     * @throws InvalidTypeException if the type provided doesn't inherit from \Xyster\Type\Property\IProperty
     */
    public function __construct( $target, Type $defaultSetter = null )
    {
        $this->_target = $target;
        if ( $defaultSetter ) {
            if ( !$defaultSetter->isAssignableFrom('\Xyster\Type\Property\IProperty') ) {
                throw new InvalidTypeException('The type provided must inherit from \Xyster\Type\Property\IProperty');
            }
            $this->_default = $defaultSetter;
        }
    }
        
    /**
     * Adds a setter to handle a type and property 
     * 
     * @param Property\IProperty $setter The setter to add
     * @param mixed $property Optional. A property name to which the setter applies.
     * @return Binder provides a fluent interface
     */
    public function addSetter( Property\IProperty $setter, $property )
    {
        $this->_setters[$property] = $setter;
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
            if ( $this->isAllowed($name) ) {
                $setter = $this->_getSetter($name);
                $setter->set($this->_target, $value);
            }
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
     * @return Binder provides a fluent interface
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
     * @return Binder provides a fluent interface
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
     * @return Property\IProperty
     */
    protected function _getSetter( $field )
    {
        if ( !isset($this->_setters[$field]) ) {
            return ( $this->_default ) ?
                $this->_default->getClass()->newInstance($field) :
                Property\Factory::get($this->_target, $field);
        } else {
            return $this->_setters[$field];
        }
    }
}