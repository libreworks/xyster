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
 * @see Xyster_Container_Monitor
 */
require_once 'Xyster/Container/Monitor.php';
/**
 * A monitor that does nothing
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Monitor_Null implements Xyster_Container_Monitor
{
    /**
     * Event thrown as the component is being instantiated using the given constructor
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $componentAdapter
     * @param Xyster_Type $class the class being instantiated
     */
    public function instantiating(Xyster_Container_Interface $container, Xyster_Container_Adapter $componentAdapter, Xyster_Type $class = null)
    {
    }

    /**
     * Event thrown after the component has been instantiated using the given constructor.
     * This should be called for both Constructor and Setter DI.
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $componentAdapter
     * @param Xyster_Type $class the class being instantiated
     * @param mixed $instantiated the component that was instantiated
     * @param array $injected the components during instantiation
     * @param float $duration the duration in millis of the instantiation
     */
    public function instantiated(Xyster_Container_Interface $container, Xyster_Container_Adapter $componentAdapter, Xyster_Type $class = null, $instantiated, array $injected = null, $duration)
    {
    }

    /**
     * Event thrown if the component instantiation failed using the given constructor
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $componentAdapter
     * @param Xyster_Type $class the class being instantiated
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    public function instantiationFailed(Xyster_Container_Interface $container, Xyster_Container_Adapter $componentAdapter, Xyster_Type $class, Exception $cause)
    {
    }

    /**
     * Event thrown as the component method is being invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $componentAdapter
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     */
    public function invoking(Xyster_Container_Interface $container, Xyster_Container_Adapter $componentAdapter, ReflectionMethod $member, $instance)
    {
    }

    /**
     * Event thrown after the component method has been invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $componentAdapter
     * @param ReflectionMethod $method the Method invoked on the component instance
     * @param mixed $instance the component instance
     * @param float $duration the duration in millis of the invocation
     */
    public function invoked(Xyster_Container_Interface $container, Xyster_Container_Adapter $componentAdapter, ReflectionMethod $method, $instance, $duration)
    {
    }

    /**
     * Event thrown if the component method invocation failed on the given instance
     * 
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    public function invocationFailed(ReflectionMethod $member, $instance, Exception $cause)
    {
    }

    /**
     * 
     * @param Xyster_Container_Interface $container
     * @param mixed $componentKey
     * @return mixed 
     */
    public function noComponentFound(Xyster_Container_Interface $container, $componentKey)
    {
        return null;
    }
}