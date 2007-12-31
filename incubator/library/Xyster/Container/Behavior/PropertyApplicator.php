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
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Behavior_Abstract
 */
require_once 'Xyster/Container/Behavior/Abstract.php';
/**
 * Behavior to apply properties
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_PropertyApplicator extends Xyster_Container_Behavior_Abstract
{
    /**
     * @var Xyster_Collection_Map_String
     */
    private $_properties;
    
    /**
     * @var Xyster_Collection_Map_String
     */
    private $_setters;
    
    /**
     * Get a component instance and set given property values
     *
     * @param Xyster_Container_Interface $container
     * @return object the component instance with any properties set
     */
    public function getInstance( Xyster_Container_Interface $container )
    {
        $componentInstance = parent::getInstance($container);
        if ( $this->_setters == null ) {
            $this->_setters = $this->_getSetters($this->getImplementation());
        }
        
        if ( $this->_properties != null ) {
            $monitor = $this->currentMonitor();
            $propertyNames = $this->_properties->keys();
            foreach( $propertyNames as $propertyName ) {
                $propertyValue = $this->_properties[$propertyName];
                $setter = $this->_setters[$propertyName];
                /* @var $setter ReflectionMethod */
                
                $valueToInvoke = $this->_getSetterParameter($propertyName, $propertyValue, $componentInstance, $container);

                try {
                    $monitor->invoking($container, $this, $setter, $componentInstance);
                    $startTime = microtime(true);
                    $setter->invoke($componentInstance, $valueToInvoke);
                    $monitor->invoked($container, $this, $setter, $componentInstance, getmicrotime(true) - $startTime);
                } catch ( Exception $thrown ) {
                    $monitor->invocationFailed($setter, $componentInstance, $thrown);
                    throw new Xyster_Container_Exception("Failed to set property " . $propertyName . " to " . $propertyValue . ": " . $thrown->getMessage());
                }
            }
        }
        return $componentInstance;
    }
    
    /**
     * Sets the property values
     * 
     * @param Xyster_Collection_Map_String $properties
     */
    public function setProperties( Xyster_Collection_Map_String $properties )
    {
        $this->_properties = $properties;
    }
    
    /**
     * Sets a property
     *
     * @param string $key
     * @param string $value
     */
    public function setProperty( $key, $value )
    {
        if ( $this->_properties == null ) {
            require_once 'Xyster/Collection/Map/String.php';
            $this->_properties = new Xyster_Collection_Map_String;
        }
        $this->_properties->set($key, $value);
    }
    
    /**
     * Gets the string equivalent of the object
     *
     * @return string
     */
    public function __toString()
    {
        return "PropertyApplied:" . parent::__toString();
    }
    
    /**
     * Gets the property name for a method
     *
     * @param ReflectionMethod $method
     * @return string
     */
    protected function _getPropertyName(ReflectionMethod $method)
    {
        $name = $method->getName();
        $result = substr($name, 3);
        if ( strlen($result) > 1 && $result[1] != strtoupper($result[1]) ) {
            $result = strtolower($result[0]) . substr($result, 1);
        } else if ( strlen($result) == 1 ) {
            $result = strtolower($result);
        }
        return $result;
    }
    
    /**
     * Converts and validates the given property value to an appropriate object for calling the bean's setter
     * 
     * @param string $propertyName the property name on the component 
     * @param mixed $propertyValue the property value that we've been given
     * @param mixed $componentInstance the component that we're looking to provide the setter to
     * @param Xyster_Container_Interface $container
     * @return object the final converted object that can be used in the setter.
     */
    protected function _getSetterParameter( $propertyName, $propertyValue, $componentInstance, Xyster_Container_Interface $container )
    {
        if ( $propertyValue == null ) {
            return null;
        }
        
        $setter = $this->_setters[$propertyName];
        /* @var $setter ReflectionMethod */
        
        $setterParameterInst = current($setter->getParameters());
        /* @var $setterParameterInst ReflectionParameter */
        $setterParameter = $setterParameterInst->getClass();
        /* @var $setterParameter ReflectionClass */

        $convertedValue = $propertyValue;
        
        if ( $convertedValue === null && $setterParameter !== null ) {
            $givenParameterClass = is_object($propertyValue) ?
                get_class($propertyValue) : gettype($propertyValue);
            require_once 'Xyster/Type.php';
            $setterParameterType = new Xyster_Type($setterParameter);
            if ( $setterParameterType->isAssignableFrom($givenParameterClass) ) {
                $convertedValue = $propertyValue;
            } else {
                throw new Xyster_Container_Exception("Setter: " . $setter->getName() . " for addComponent: "
                    . $componentInstance->__toString() . " can only take objects of: " . $setterParameter->getName()
                    . " instead got: " . $givenParameterClass);
            }
        }
        return $convertedValue;
    }
    
    /**
     * Gets the setters for a class
     *
     * @param ReflectionClass $class
     * @return array an array full of ReflectionMethod objects
     */
    protected function _getSetters(Xyster_Type $type)
    {
        $class = $type->getClass();
        $result = array();
        if ( $class instanceof ReflectionClass ) {
            $methods = $class->getMethods();
            foreach( $methods as $method ) {
                /* @var $method ReflectionMethod */
                if ( $this->_isSetter($method)) {
                    $result[ $this->_getPropertyName($method) ] = $method;
                }
            }
        }
        return $result;
    }
    
    /**
     * Gets whether a method is a setter
     *
     * @param ReflectionMethod $method
     * @return boolean
     */
    protected function _isSetter(ReflectionMethod $method)
    {
        $name = $method->getName();
        return strlen($name) > 3 && preg_match('/^set/', $name)
            && $method->getNumberOfParameters() == 1;
    }
}