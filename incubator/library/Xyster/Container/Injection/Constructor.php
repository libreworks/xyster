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
 * Instantiates component using Constructor injection
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Constructor extends Xyster_Container_Injection_SingleMember
{
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
        $constructor = $this->getImplementation()->getConstructor();
        $componentMonitor = $this->currentMonitor();
        
        try {
            $parameters = $this->_getMemberArguments($container, $constructor);
            $constructor = $componentMonitor->instantiating($container, $this, $constructor);
            $startTime = microtime(true);
            $inst = $this->_newInstance($constructor, $parameters);
            $componentMonitor->instantiated($container, $this, $constructor, $inst, $parameters, microtime(true) - $startTime);
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
        $constructor = $this->getImplementation()->getConstructor();
        /* @var $constructor ReflectionMethod */
        $parameterTypes = array();
        foreach( $constructor->getParameters() as $param ) {
            /* @var $param ReflectionParameter */
            $parameterTypes[] = $param->getClass();
        }
        $currentParameters = $this->_parameters !== null ? $this->_parameters :
            $this->_createDefaultParameters($parameterTypes);
        $reflectionParams = $constructor->getParameters();
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
        return "ConstructorInjector-" . parent::__toString();
    }
}