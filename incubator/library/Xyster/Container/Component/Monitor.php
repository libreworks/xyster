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
 * Responsible for monitoring the component instantiation and method invocation
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Component_Monitor
{
    /**
     * Event thrown as the component is being instantiated using the given constructor
     *
     * @param Xyster_Container_Interface $container
     * @param componentAdapter
     * @param constructor the Constructor used to instantiate the addComponent
     * @return ReflectionMethod the constructor to use in instantiation (nearly always the same one as passed in)
     */
    function instantiating(Xyster_Container_Interface $container, Xyster_Container_Component_Adapter $componentAdapter, ReflectionMethod $constructor);

    /**
     * Event thrown after the component has been instantiated using the given constructor.
     * This should be called for both Constructor and Setter DI.
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Component_Adapter $componentAdapter
     * @param ReflectionMethod $constructor the Constructor used to instantiate the addComponent
     * @param mixed $instantiated the component that was instantiated by PicoContainer
     * @param array $injected the components during instantiation.
     * @param float $duration the duration in millis of the instantiation
     */
    function instantiated(Xyster_Container_Interface $container, Xyster_Container_Component_Adapter $componentAdapter,
                      ReflectionMethod $constructor,
                      $instantiated,
                      array $injected,
                      $duration);

    /**
     * Event thrown if the component instantiation failed using the given constructor
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Component_Adapter $componentAdapter
     * @param ReflectionMethod $constructor the Constructor used to instantiate the addComponent
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    function instantiationFailed(Xyster_Container_Interface $container,
                             Xyster_Container_Component_Adapter $componentAdapter,
                             ReflectionMethod $constructor,
                             Exception $cause);

    /**
     * Event thrown as the component method is being invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Component_Adapter $componentAdapter
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     */
    function invoking(Xyster_Container_Interface $container, Xyster_Container_Component_Adapter $componentAdapter, ReflectionMethod $member, $instance);

    /**
     * Event thrown after the component method has been invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Component_Adapter $componentAdapter
     * @param ReflectionMethod $method the Method invoked on the component instance
     * @param mixed $instance the component instance
     * @param float $duration the duration in millis of the invocation
     */
    function invoked(Xyster_Container_Interface $container,
                 Xyster_Container_Component_Adapter $componentAdapter,
                 ReflectionMethod $method,
                 $instance,
                 $duration);

    /**
     * Event thrown if the component method invocation failed on the given instance
     * 
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    function invocationFailed(ReflectionMethod $member, $instance, Exception $cause);

    /**
     * 
     * @param Xyster_Container_Interface $container
     * @param mixed $componentKey
     * @return mixed 
     */
    function noComponentFound(Xyster_Container_Interface $container, $componentKey);
}