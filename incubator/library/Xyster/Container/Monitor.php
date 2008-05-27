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
 * Responsible for monitoring the component instantiation and method invocation
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Monitor
{
    /**
     * Event thrown as the component is being instantiated using the given constructor
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Type $class the class being instantiated
     */
    function instantiating(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $class = null);

    /**
     * Event thrown after the component has been instantiated using the given constructor.
     * This should be called for both Constructor and Setter DI.
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Type $class the class being instantiated
     * @param mixed $instantiated the component that was instantiated
     * @param array $injected the components during instantiation
     * @param float $duration the duration in millis of the instantiation
     */
    function instantiated(Xyster_Container_Interface $container,
        Xyster_Container_Adapter $adapter, Xyster_Type $class = null,
        $instantiated, array $injected = null, $duration);

    /**
     * Event thrown if the component instantiation failed using the given constructor
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Type $class the class being instantiated
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    function instantiationFailed(Xyster_Container_Interface $container,
        Xyster_Container_Adapter $adapter, Xyster_Type $class, Exception $cause);

    /**
     * Event thrown as the component method is being invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     */
    function invoking(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionMethod $member, $instance);

    /**
     * Event thrown after the component method has been invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param ReflectionMethod $method the Method invoked on the component instance
     * @param mixed $instance the component instance
     * @param float $duration the duration in millis of the invocation
     */
    function invoked(Xyster_Container_Interface $container,
        Xyster_Container_Adapter $adapter, ReflectionMethod $method, $instance, $duration);

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
     * @param mixed $key
     * @return mixed 
     */
    function noComponentFound(Xyster_Container_Interface $container, $key);
    
    /**
     * A mechanism to monitor or override the Abstract Injectors being made for components.
     *
     * @param Xyster_Container_Injection_Abstract the abstract injector the container intends to use for the component currently being added.
     * @return Xyster_Container_Injection_Abstract an abstract Injector. For most implementations, the same one as was passed in.
     */
     function newInjectionFactory(Xyster_Container_Injection_Abstract $abstractInjector);
}