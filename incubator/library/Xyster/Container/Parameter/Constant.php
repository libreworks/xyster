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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Parameter_Constant implements Xyster_Container_Parameter
{   
    private $_value;
    
    /**
     * Creates a new constant value parameter
     *
     * @param mixed $value
     */
    public function __construct( $value )
    {
        $this->_value = $value;
    }
    
    /**
     * Accepts a visitor for this Parameter
     *
     * @param Xyster_Container_Visitor $visitor the visitor
     */
    public function accept( Xyster_Container_Visitor $visitor )
    {
        $visitor->visitParameter($this);
    }
    
    /**
     * Check if the Parameter can statisfy the expected type using the container.
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the instance
     * @param Xyster_Type $expectedType the required type
     * @param Xyster_Container_NameBinding $expectedNameBinding the expected parameter name
     * @param boolean $useNames
     * @return boolean <code>true</code> if the component parameter can be resolved.
     */
    public function isResolvable( Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames )
    {
        try {
            $this->verify($container, $adapter, $expectedType, $expectedNameBinding, $useNames);
            return true;
        } catch ( Xyster_Container_Exception $e ) {
            return false;
        }
    }
    
    /**
     * Retrieve the object from the Parameter that statisfies the expected type.
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the instance
     * @param Xyster_Type $expectedType the required type
     * @param Xyster_Container_NameBinding $expectedNameBinding the expected parameter name
     * @param boolean $useNames
     * @return mixed the instance or <code>null</code> if no suitable instance can be found.
     */
    public function resolveInstance( Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames )
    {
        return $this->_value;
    }
        
    /**
     * Verify that the Parameter can statisfied the expected type using the container
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the verification
     * @param Xyster_Type $expectedType the required type
     * @param Xyster_Container_NameBinding $expectedNameBinding the expected parameter name
     * @param boolean $useNames
     * @throws Xyster_Container_Exception if parameter and its dependencies cannot be resolved
     */
    public function verify( Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames )
    {
        if ( !$this->_checkPrimitive($expectedType) && ( !is_object($this->_value) || !$expectedType->getClass()->isInstance($this->_value) ) ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception($expectedType->getName() . " is not an instance of the value for this constant");
        }
    }

    /**
     * Checks if a type is considered primative
     *
     * @param Xyster_Type $expectedType
     * @return boolean
     */
    protected function _checkPrimitive( Xyster_Type $expectedType )
    {
        return !$expectedType->isObject();
    }
}