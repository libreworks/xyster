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
 * Responsible for providing an instance of a specific type
 * 
 * An instance of this interface will be used inside a container for every
 * component that is registered.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Adapter
{
    /**
     * Accepts a visitor for this Adapter
     * 
     * The method is normally called by visiting a
     * {@link Xyster_Container_Interface}, that cascades the visitor also down
     * to all its Component Adapter instances.
     *
     * @param Xyster_Container_Visitor $visitor the visitor.
     */
    function accept(Xyster_Container_Visitor $visitor);
    
    /**
     * @return Xyster_Container_Adapter
     */
    function getDelegate();

    /**
     * Retrieve the class of the component
     * 
     * Should normally be a concrete class (ie, a class that can be
     * instantiated).
     *
     * @return ReflectionClass the component's implementation class
     */
    function getImplementation();

    /**
     * Retrieve the component instance
     * 
     * This method will usually create a new instance each time it is called,
     * but that is not required.
     * 
     * For example, {@link Xyster_Container_Behavior_Cached} will always return
     * the same instance.
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */
    function getInstance(Xyster_Container_Interface $container);

    /**
     * Retrieve the key associated with the component
     *
     * Should either be a class type (normally an interface) or an identifier
     * that is unique (within the scope of the current Container).
     * 
     * @return mixed the component's key
     */
    function getKey();
    
    /**
     * Verify that all dependencies for this adapter can be satisifed
     * 
     * Normally, the adapter should verify this by checking that the associated
     * Container contains all the needed dependnecies.
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @throws Exception if one or more dependencies cannot be resolved
     */
    function verify(Xyster_Container_Interface $container);
}