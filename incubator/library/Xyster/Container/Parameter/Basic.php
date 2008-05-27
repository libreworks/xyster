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
 * Should be used to pass in a particular component as argument to a different component's constructor
 * 
 * This is particularly useful in cases where several components of the same
 * type have been registered, but with a different key. Passing a Parameter as a
 * parameter when registering a component will give the Container a hint about
 * what other component to use in the constructor. This Parameter will never
 * resolve against a collecting type, that is not directly registered in the
 * Container itself.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Parameter_Basic implements Xyster_Container_Parameter
{   
    private $_key;
    
    static protected $_default;
    
    /**
     * Gets a simple parameter
     *
     * @return Xyster_Container_Parameter_Basic
     */
    public static function standard()
    {
        if ( !self::$_default ) {
            self::$_default = new self;
        }
        return self::$_default;
    }
    
    /**
     * Creates a new basic parameter
     * 
     * If the $key parameter is null, this object will match any parameter of
     * the appropriate type.
     *
     * @param mixed $key The key of the desired component
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
     * @param Xyster_Container_Interface $container the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the adapter that is asking for the instance
     * @param Xyster_Type $expectedType the required type
     * @param Xyster_Container_NameBinding $expectedNameBinding the expected parameter name
     * @param boolean $useNames
     * @return boolean <code>true</code> if the component parameter can be resolved.
     */
    public function isResolvable(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter = null, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames)
    {
        return $this->_resolveAdapter($container, $adapter, $expectedType, $expectedNameBinding, $useNames) != null;
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
     * @throws Xyster_Container_Exception if a referenced component could not be instantiated.
     */
    public function resolveInstance(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter = null, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames)
    {
        $adapter = $this->_resolveAdapter($container, $adapter, $expectedType, $expectedNameBinding, $useNames);
        if ( $adapter !== null ) {
        	$o = $container->getComponent($adapter->getKey());
        	// if ( is_string($o) && $expectedType->getName() != 'string' ) {
        		
        	// }
        	return $o;
        }
//        if ( $expectedParameter->isDefaultValueAvailable() ) {
//            return $expectedParameter->getDefaultValue();
//        }
        return null;
    }
    
    /**
     * Verify that the Parameter can statisfied the expected type using the container
     *
     * @param Xyster_Container_Interface $container       the container from which dependencies are resolved.
     * @param Xyster_Container_Adapter $adapter the Component Adapter that is asking for the verification
     * @param Xyster_Type $expectedType the required type
     * @param Xyster_Container_NameBinding $expectedNameBinding the expected parameter name
     * @param boolean $useNames
     * @throws Xyster_Container_Parameter_Exception if parameter and its dependencies cannot be resolved
     */
    public function verify(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter = null, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames)
    {
        $adapter = $this->_resolveAdapter($container, $adapter, $expectedType, $expectedNameBinding, $useNames);
        if ( $adapter == null ) {
            require_once 'Xyster/Container/Parameter/Exception.php';
            throw new Xyster_Container_Parameter_Exception('Unsatisfiable dependencies: ' . $expectedType);
        }
        $adapter->verify($container);
    }
    
    /**
     * Tests to see if a type and an adapter are compatible
     *
     * @param Xyster_Type $expectedType
     * @param Xyster_Container_Adapter $found
     * @return boolean
     */
    protected function _areCompatible( Xyster_Type $expectedType, Xyster_Container_Adapter $found )
    {
        $foundImpl = $found->getImplementation();
        return $expectedType->isAssignableFrom($foundImpl);
    }
    
    /**
     * Gets the adapter for the parameter supplied
     *
     * @param Xyster_Container_Interface $container
     * @param ReflectionParameter $expectedParameter
     * @param Xyster_Container_Adapter $excludeAdapter
     * @return Xyster_Container_Adapter
     */
    protected function _getTargetAdapter( Xyster_Container_Interface $container, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, Xyster_Container_Adapter $excludeAdapter = null, $useNames )
    {
        if ( $this->_key !== null ) {
            return $container->getComponentAdapter($this->_key);
        } else if ( $excludeAdapter === null ) {
            return $container->getComponentAdapterByType($expectedType, null);
        } else {
	        // try to find it by key
	        $excludeKey = $excludeAdapter->getKey();
            $byKey = $container->getComponentAdapter($expectedType);
            if ( $byKey !== null && !Xyster_Type::areEqual($excludeKey, $byKey->getKey()) ) {
                return $byKey;
            }
            if ( $useNames ) {
            	$found = $container->getComponentAdapter($expectedNameBinding->getName());
            	if ( $found !== null && $this->_areCompatible($expectedType, $found) && $found !== $excludeAdapter ) {
            		return $found;
            	}
            }
            
	        // get all component adapters with the expected type
	        $found = $container->getComponentAdapters($expectedType);
	        $exclude = null;
	        foreach( $found as $foundAdapter ) {
	            if ( Xyster_Type::areEqual($foundAdapter->getKey(), $excludeKey) ) {
	                $exclude = $foundAdapter;
	            }
	        }
	        $found->remove($exclude);
	        if ( count($found) == 0 ) {
	            if ( $container->getParent() !== null ) {
	                return $container->getParent()->getComponentAdapterByType($expectedType, $expectedNameBinding); 
	            } else {
	                return null; // none registered
	            }
	        } else if ( count($found) == 1 ) {
	            return $found->get(0); // one registered
	        } else {
	        	$foundClasses = array();
	            foreach( $found as $adapter ) { // look for parameter name as key
	            	$foundClasses[] = $adapter->getImplementation();
	            }
	            require_once 'Xyster/Container/Parameter/Exception.php';
	            throw new Xyster_Container_Parameter_Exception('Ambiguous component resolution: ' .
	               $expectedType . ', found ' . implode(', ', $foundClasses));
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
    protected function _resolveAdapter( Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter = null, Xyster_Type $expectedType, Xyster_Container_NameBinding $expectedNameBinding, $useNames )
    {
        $result = $this->_getTargetAdapter($container, $expectedType,
            $expectedNameBinding, $adapter, $useNames);
            
        if ( $result === null || !$expectedType->isAssignableFrom($result->getImplementation()) ) {
            return null;
        } else {
        	return $result;
        }
    }
}