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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Component definition class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Definition
{
    protected $_name;
    protected $_type;
    protected $_initMethod;
    protected $_constructorArguments = array();
    protected $_properties = array();

    /**
	 * Creates a new definition
	 * 
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name. 
     */
    public function __construct($type, $name = null)
    {
        $this->_type = ( $type instanceof Xyster_Type ) ?
            $type : new Xyster_Type($type);
        $this->_name = $name;
    }
    
    /**
	 * Adds a constructor argument.
	 * 
	 * Call this method multiple times for several constructor arguments.  The
	 * value argument can either be a literal value or the name of another
	 * component in the container.
	 * 
	 * @param mixed $value The argument value
     */
    public function constructorArg($value)
    {
        $this->_constructorArguments[] = $value;
        return $this;
    }
    
    /**
     * Gets the constructor arguments 
     * 
     * @return array
     */
    public function getConstructorArgs()
    {
        return $this->_constructorArguments;
    }
    
    /**
     * Gets the initialization method
     * 
     * @return string
     */
    public function getInitMethod()
    {
        return $this->_initMethod;
    }
    
    /**
     * Gets the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the properties

     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }
    
    /**
     * Gets the type
     * 
     * @return Xyster_Type
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
	 * Sets the name of the method to be invoked when an object has been created
	 * 
	 * @param string $name The method name
	 * @return Xyster_Container_Definition provides a fluent interface
	 * @throws Xyster_Container_Exception if the method wasn't found
     */
    public function initMethod($name)
    {
        if ( !$this->_type->getClass()->hasMethod($name) && 
            !method_exists($this->_type->getName(), '__call') ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Method not found: ' .
                $this->_type->getName() . '::' . $name);
        }
        $this->_initMethod = $name;
        return $this;
    }
    
    /**
	 * Adds a property to be injected.
	 * 
	 * The value argument can either be a literal value or the name of another
	 * component in the container.
	 * 
	 * @param string $name The property name
	 * @param mixed $value The property value or reference
	 * @return Xyster_Container_Definition provides a fluent interface
     */
    public function property($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }
}