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
 * @see Xyster_Container_Injection_SingleMember
 */
require_once 'Xyster/Container/Injection/SingleMember.php';
/**
 * Instantiates component using method injection
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Method extends Xyster_Container_Injection_SingleMember
{
    protected $_methodName;

    /**
     * Creates a methodinjector
     *
     * @param mixed $componentKey
     * @param mixed $componentImplementation
     * @param array $parameters
     * @param Xyster_Container_Monitor $monitor
     * @param string $methodName
     */
    public function __construct( $componentKey, $componentImplementation, array $parameters = null, Xyster_Container_Monitor $monitor, $methodName )
    {
        parent::__construct($componentKey, $componentImplementation, $parameters, $monitor);
        $this->_methodName = $methodName;
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
        $componentMonitor = $this->currentMonitor();
        
        try {
            $componentMonitor->instantiating($container, $this, null);
            $startTime = microtime(true);
            $inst = $this->getImplementation()->newInstance();
            $parameters = $this->_getMemberArguments($container, $method);
            $method->invoke($inst, $parameters);
            $componentMonitor->instantiated($container, $this, null, $inst, $parameters, microtime(true) - $startTime);
            return $inst;
        } catch ( Exception $e ) {
            $this->_caughtInstantiationException($componentMonitor, $constructor, $e, $container);
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
        $parameterTypes = array();
        foreach( $method->getParameters() as $param ) {
            /* @var $param ReflectionParameter */
            $parameterTypes[] = $param->getClass();
        }
        $currentParameters = $this->_parameters !== null ? $this->_parameters :
            $this->_createDefaultParameters($parameterTypes);
        $reflectionParams = $method->getParameters();
        foreach( $currentParameters as $k => $param ) {
            $param->verify($container, $this, $reflectionParams[$k]);
        }
    }
    
    /**
     * Gets the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return "MethodInjector-" . parent::__toString();
    }
    
    /**
     * Gets the method to use for injection
     *
     * @return ReflectionMethod
     */
    protected function _getInjectorMethod()
    {
        return $this->getImplementation()->getMethod($this->_methodName);
    }
}