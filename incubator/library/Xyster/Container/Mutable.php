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
 * @see Xyster_Container_Interface
 */
require_once 'Xyster/Container/Interface.php';
/**
 * Core interface for registration of components within a container
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Mutable extends Xyster_Container_Interface
{
    /**
     * Register a component via an Adapter
     * 
     * Use this if you need fine grained control over what Adapter to use for a
     * specific component.
     *
     * @param Xyster_Container_Adapter $componentAdapter the addAdapter
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration fails
     */
    function addAdapter(Xyster_Container_Adapter $componentAdapter, Xyster_Collection_Map_Interface $properties = null);
    
    /**
     * Register a component
     * 
     * This method creates specific instructions with which components
     * and/or constants to provide as constructor arguments.
     * 
     * If the key is null, the implementation class will be used as a key. 
     * 
     * These "directives" are provided through an array of Parameter objects.
     * Parameter[0] correspondes to the first constructor argument,
     * Parameter[N] corresponds to the  N+1th constructor argument.
     * 
     * Partial Autowiring: If you have two constructor args to match and you
     * only wish to specify one of the constructors and let the Container wire
     * the other one, you can use as parameters:
     * <code>new ComponentParameter(), new ComponentParameter("someService")</code>
     * The constructor for the component parameter indicates auto-wiring should
     * take place for that parameter.
     * 
     * Force No-Arg constructor usage: If you wish to force a component to be
     * constructed with the no-arg constructor, use a zero length Parameter
     * array.
     *
     * @param mixed $implementationOrInstance the component's implementation class or an intance of the compoent
     * @param mixed $key a key unique within the container that identifies the component
     * @param mixed $parameters the parameters that gives hints about what arguments to pass
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration of the component fails
     */
    function addComponent($implementationOrInstance, $key = null, array $parameters = null);

    /**
     * Register a config item
     *
     * @param string $name the name of the config item
     * @param mixed $val the value of the config item
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration fails
     */
    function addConfig($name, $val);

    /**
     * You can change the characteristic of registration of all subsequent components in this container
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable provides a fluent interface
     */
    function change( Xyster_Collection_Map_Interface $properties );

    /**
     * Unregister a component by key
     *
     * @param mixed $componentKey key of the component to unregister.
     * @return Xyster_Container_Adapter the adapter that was associated with this component
     */
    function removeComponent($componentKey);

    /**
     * Unregister a component by instance
     *
     * @param mixed $componentInstance the component instance to unregister.
     * @return Xyster_Container_Adapter the adapter removed
     */
    function removeComponentByInstance($componentInstance);

    /**
     * You can set for the following operation only the characteristic of registration of a component on the fly
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable the same instance with temporary properties
     */
    function with();
}