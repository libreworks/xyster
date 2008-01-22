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
 * @see Xyster_Container_Injection_SingleMember
 */
require_once 'Xyster/Container/Injection/SingleMember.php';
/**
 * Instantiates component using method injection
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Method extends Xyster_Container_Injection_SingleMember
{
    protected $_methodName;

    /**
     * Creates a methodinjector
     *
     * @param mixed $key
     * @param mixed $implementation
     * @param array $parameters
     * @param Xyster_Container_Monitor $monitor
     * @param string $methodName
     */
    public function __construct( $key, $implementation, array $parameters = null, Xyster_Container_Monitor $monitor = null, $methodName = 'inject' )
    {
        parent::__construct($key, $implementation, $parameters, $monitor);
        $this->_methodName = $methodName;
    }
    
    /**
     * Gets the descriptor of this adapter
     *
     * @return string
     */
    public function getDescriptor()
    {
        return 'MethodInjector-';
    }
    
    /**
     * Retrieve the component instance
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */
    public function getInstance( Xyster_Container_Interface $container )
    {
        $method = $this->_getInjectorMethod();
        $inst = null;
        
        $monitor = $this->currentMonitor();
        
        try {
            $monitor->instantiating($container, $this, null);
            $startTime = microtime(true);
            $parameters = null;
            $inst = $this->getImplementation()->getClass()->newInstance();
            if ( $method instanceof ReflectionMethod ) {
                $parameters = $this->_getMemberArguments($container, $method,
                    Xyster_Type::getForParameters($method));
                $method->invokeArgs($inst, $parameters);
            }
            $monitor->instantiated($container, $this, null, $inst, $parameters, microtime(true) - $startTime);
            return $inst;
        } catch ( ReflectionException $e ) {
            $this->_caughtInstantiationException($monitor, null, $e, $container);
        }
    }
    
    /**
     * Verify that all dependencies for this adapter can be satisifed
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @throws Exception if one or more dependencies cannot be resolved
     */
    public function verify( Xyster_Container_Interface $container )
    {
        $method = $this->_getInjectorMethod();
        $parameterTypes = Xyster_Type::getForParameters($method);
        $currentParameters = $this->_parameters !== null ? $this->_parameters :
            $this->_createDefaultParameters($parameterTypes);
        foreach( $currentParameters as $k => $param ) {
        	/* @var $param Xyster_Container_Parameter */
            $param->verify($container, $this, $parameterTypes[$k],
                new Xyster_Container_NameBinding_Parameter($method, $k),
                $this->useNames());
        }
    }
    
    /**
     * Gets the method to use for injection
     *
     * @return ReflectionMethod
     */
    protected function _getInjectorMethod()
    {
        $class = $this->getImplementation()->getClass();
        return $class->hasMethod($this->_methodName) ?
            $class->getMethod($this->_methodName) : null;
    }
}