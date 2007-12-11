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
 * @see Xyster_Container_Parameter_Basic
 */
require_once 'Xyster/Container/Parameter/Basic.php';
/**
 * Should be used to pass in a particular component as argument to a different component's constructor
 * 
 * This is particularly useful in cases where several components of the same
 * type have been registered, but with a different key. Passing a Component
 * Parameter as a parameter when registering a component will give the Container
 * a hint about what other component to use in the constructor. This Parameter
 * will never resolve against a collecting type, that is not directly registered
 * in the Container itself.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Parameter_Component implements Xyster_Container_Parameter_Basic
{   
    /**
     * @var Xyster_Container_Parameter
     */
    private $_collectionParameter;
    
    /**
     * Creates a new component parameter
     *
     * @param mixed $key
     * @param boolean $emptyCollection
     * @param mixed $valueType
     * @param mixed $keyType
     */
    public function __construct( $key = null, $emptyCollection = false, $valueType = null, $keyType = null )
    {
        parent::__construct($key);
        
        $collectionParameter = null;
        if ( $valueType == null && $keyType == null ) {
            $collectionParameter = $emptyCollection ?
                Xyster_Container_Parameter_Collection::ARRAY_ALLOW_EMPTY :
                Xyster_Container_Parameter_Collection::ARRAY_TYPE; 
        } else {
            $collectionParameter = new Xyster_Container_Parameter_Collection($keyType,
                $valueType, $emptyCollection);
        }
    }
    
    /**
     * Accepts a visitor for this Parameter
     *
     * @param Xyster_Container_Visitor $visitor the visitor
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
        parent::accept($visitor);
        if ( $this->_collectionParameter !== null ) {
            $this->_collectionParameter->accept($visitor);
        }
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
        if ( !parent::isResolvable($container, $adapter, $expectedParameter) ) {
            if ( $this->_collectionParameter !== null ) {
                return $this->_collectionParameter->isResolvable($container, $adapter, $expectedParameter);
            }
            return false;
        }
        return true;
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
        $result = parent::resolveInstance($container, $adapter, $expectedParameter);
        if ( $result === null && $this->_collectionParameter !== null ) {
            $result = $this->_collectionParameter->resolveInstance($container, $adapter, $expectedParameter);
        }
        return $result;
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
        try {
            parent::verify($container, $adapter, $expectedParameter);
        } catch ( Xyster_Container_Exception $thrown ) {
            if ( $this->_collectionParameter !== null ) {
                $this->_collectionParameter->verify($container, $adapter, $expectedParameter);
                return;
            }
            throw $thrown;
        }
    }
}