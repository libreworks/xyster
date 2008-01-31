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
 * @see Xyster_Container_Visitor_TraversalChecking
 */
require_once 'Xyster/Container/Visitor/TraversalChecking.php';
/**
 * @see Xyster_Collection_List
 */
require_once 'Xyster/Collection/List.php';
/**
 * A visitor implementation that calls methods on the components of a specified type
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Visitor_MethodCalling extends Xyster_Container_Visitor_TraversalChecking
{
	/**
	 * @var ReflectionMethod
	 */
	private $_method;
	
	/**
	 * @var array
	 */
	private $_arguments = array();
	 
	/**
	 * @var boolean
	 */
	private $_visitInInstantiationOrder;
	
	/**
	 * @var Xyster_Collection_List
	 */
	private $_componentInstances;
	
	/**
	 * Creates a MethodCalling visitor for a method with arguments
	 *
	 * @param ReflectionMethod $method the method to invoke
	 * @param array $arguments the arguments for the method invocation
	 * @param boolean $visitInInstantiationOrder true if components are visited in instantiation order
	 */
	public function __construct(ReflectionMethod $method, array $arguments = array(), $visitInInstantiationOrder = true)
	{
		$this->_method = $method;
		$this->_arguments = $arguments;
		$this->_visitInInstantiationOrder = $visitInInstantiationOrder;
		$this->_componentInstances = new Xyster_Collection_List;
	}
	
	/**
     * Entry point for the Visitor traversal
     * 
     * {@inherit}
     * 
     * @param mixed $node the start node of the traversal 
     */
	public function traverse( $node )
	{
		$this->_componentInstances->clear();
		try {
			parent::traverse($node);
			if ( !$this->_visitInInstantiationOrder ) {
				$list = new Xyster_Collection_List;
				for( $i=count($this->_componentInstances)-1; $i>-1; $i-- ) {
					$list->add($this->_componentInstances->get($i));
				}
				$this->_componentInstances = $list;
			}
			foreach( $this->_componentInstances as $instance ) {
				$this->_invoke($instance);
			}
		} catch ( Exception $thrown ) {
			$this->_componentInstances->clear();
			throw $thrown;
		}
	}
	
    /**
     * Visit a container that has to accept the visitor
     * 
     * @param Xyster_Container_Interface $container the visited container.
     */
    public function visitContainer(Xyster_Container_Interface $container)
    {
        parent::visitContainer($container);
        $type = new Xyster_Type($this->_method->getDeclaringClass());
        $this->_componentInstances->merge($container->getComponents($type));
    }
    
    /**
     * Gets the arguments for the method
     *
     * @return array
     */
    protected function _getArguments()
    {
    	return $this->_arguments;
    }
    
    /**
     * Gets the method
     *
     * @return ReflectionMethod
     */
    protected function _getMethod()
    {
        return $this->_method;
    }
    
    /**
     * Invokes the method on the given object
     *
     * @param object $target
     */
    protected function _invoke( $target )
    {
    	$method = $this->_getMethod();
    	try {
    		$method->invokeArgs($target, $this->_getArguments());
    	} catch ( ReflectionException $e ) {
    		require_once 'Xyster/Container/Visitor/Exception.php';
    		throw new Xyster_Container_Visitor_Exception("Can't call " .
    		  $method->getName() . " on " . get_class($target) . ": " . $e->getMessage());
    	}
    }
}