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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Parameter
 */
require_once 'Xyster/Container/Parameter.php';
/**
 * Used to pass in "constant" arguments to constructors and methods
 * 
 * This includes strings, integers, or any other objects that is not registered
 * in the container.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @todo finish implementing
 */
class Xyster_Container_Parameter_Collection implements Xyster_Container_Parameter
{   
    private $_emptyCollection;
    
    private $_componentKeyType;
    
    private $_componentValueType;
    
    /**
     * Creates a new Collection parameter
     *
     * @param boolean $emptyCollection
     * @param mixed $componentValueType
     * @param mixed $componentKeyType
     */
    public function __construct( $emptyCollection = false, $componentValueType = null, $componentKeyType = null )
    {
        $this->_emptyCollection = $emptyCollection;
        $this->_componentKeyType = $componentKeyType;
        $this->_componentValueType = $componentValueType;
    }
    
    /**
     * Accepts a visitor for this Parameter
     *
     * @param Xyster_Container_Visitor $visitor the visitor
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
        $visitor->visitParameter($this);
    }

    /**
     * Check if the Parameter can statisfy the expected type using the container.
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the instance
     * @param ReflectionParameter $expectedParameter      the expected parameter
     * @return boolean <code>true</code> if the component parameter can be resolved.
     */
    public function isResolvable(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionParameter $expectedParameter)
    {
        
    }
    
    /**
     * Retrieve the object from the Parameter that statisfies the expected type.
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the instance
     * @param ReflectionParameter $expectedParameter      the expected parameter
     * @return mixed the instance or <code>null</code> if no suitable instance can be found.
     * @throws Xyster_Container_Exception if a referenced component could not be instantiated.
     */
    public function resolveInstance(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionParameter $expectedParameter)
    {
        
    }
    
    /**
     * Verify that the Parameter can statisfied the expected type using the container
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the verification
     * @param ReflectionParameter $expectedParameter      the expected parameter
     * @throws Xyster_Container_Exception if parameter and its dependencies cannot be resolved
     */
    public function verify(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionParameter $expectedParameter)
    {
        
    }
}