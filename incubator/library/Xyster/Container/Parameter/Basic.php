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
class Xyster_Container_Parameter_Basic implements Xyster_Container_Parameter
{   
    private $_key;
    
    /**
     * Creates a new basic parameter
     *
     * @param mixed $key
     */
    public function __construct( $key = null )
    {
        $this->_key = $key;
    }
    
    /**
     * Visit the current parameter
     *
     * @param Xyster_Container_Visitor $visitor
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
     * @param ReflectionParameter $expectedParameter      the expected parameter
     * @return boolean <code>true</code> if the component parameter can be resolved.
     */
    public function isResolvable(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionParameter $expectedParameter)
    {
        return $this->_resolveAdapter($container, $adapter, $expectedParameter) != null;
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
        $adapter = $this->_resolveAdapter($container, $adapter, $expectedParameter);
        if ( $adapter !== null ) {
            return $container->getComponent($adapter->getKey());
        }
        return null;
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
        $adapter = $this->_resolveAdapter($container, $adapter, $expectedParameter);
        if ( $componentAdapter == null ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Unsatisfiable dependencies');
        }
        $adapter->verify($container);
    }
    
    /**
     * Gets the adapter for the parameter supplied
     *
     * @param Xyster_Container_Interface $container
     * @param ReflectionParameter $expectedParameter
     * @param Xyster_Container_Adapter $excludeAdapter
     * @return Xyster_Container_Adapter
     */
    protected function _getTargetAdapter( Xyster_Container_Interface $container, ReflectionParameter $expectedParameter, Xyster_Container_Adapter $excludeAdapter = null )
    {
        $expectedType = $expectedParameter->getClass();
        if ( $this->_key !== null ) {
            return $container->getComponentAdapter($this->_key);
        } else if ( $excludeAdapter == null ) {
            return $container->getComponentAdapter($expectedType, null);
        } else {
            $excludeKey = $excludeAdapter->getKey();
            $byKey = $container->getComponentAdapter($expectedType);
            if ( $byKey !== null && $excludeKey == $byKey->getKey() ) {
                return $byKey;
            }
            
            $found = $container->getComponentAdapters($expectedType);
            $exclude = null;
            foreach( $found as $work ) {
                if ( $work->getKey() == $excludeKey ) {
                    $exclude = $work;
                }
            }
            $found->remove($exclude);
            if ( count($found) == 0 ) {
                return null;
            } else if ( count($found) == 1 ) {
                return $found->get(0);
            } else {
                foreach( $found as $adapter ) {
                    $key = $adapter->getKey();
                    if ( $key == $expectedParameter->getName() ) {
                        return $adapter;
                    }
                }
                
                throw new Xyster_Container_Exception('Ambiguous component resolution: ' . $expectedType->getName());
            }
        }
    }
    
    /**
     * Tries to resolve the adapter used for the type 
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param ReflectionParameter $expectedParameter
     * @return Xyster_Container_Adapter
     */
    protected function _resolveAdapter( Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionParameter $expectedParameter )
    {
        $result = $this->_getTargetAdapter($container, $expectedParameter, $adapter);
        if ( $result === null ) {
            return null;
        }
        
        $expectedType = $expectedParameter->getClass();
        /* @var $expectedType ReflectionClass */
        if ( $expectedType->getName() != $result->getImplementation()->getName() && !is_subclass_of($result->getImplementation->getName(), $expectedType->getName()) ) {
            return null;
        }
        
        return $result;
    }
}