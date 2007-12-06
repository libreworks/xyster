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
 * The core container interface
 * 
 * It is used to retrieve component instances from the container.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Interface
{
    /**
     * Accepts a visitor that should visit the child containers, component adapters and component instances.
     *
     * @param visitor the visitor
     */
    function accept(Xyster_Container_Visitor $visitor);
    
    /**
     * Retrieve a component instance registered with a specific key or type
     *
     * @param mixed $componentKeyOrType the key or Type that the component was registered with
     * @return object an instantiated component, or null if no component has been registered for the specified key
     */
    function getComponent($componentKeyOrType);

    /**
     * Retrieve all the registered component instances in the container
     * 
     * The components are returned in their order of instantiation, which
     * depends on the dependency order between them.
     *
     * @return Xyster_Collection_List all the components.
     * @throws Exception if the instantiation of the component fails
     */
    function getComponents();
    
    /**
     * Find a component adapter associated with the specified key
     * 
     * @param mixed $componentKey the key that the component was registered with
     * @return Xyster_Container_Component_Adapter the component adapter associated with this key, or null
     */
    function getComponentAdapter( $componentKeyOrType, $componentParameterName );

    /**
     * Retrieve all the component adapters inside this container.
     * 
     * If the type is supplied, this method returns the adapters associated with
     * the specified type.
     *
     * @return Xyster_Collection a fixed collection containing all the adapters inside this container
     */
    function getComponentAdapters( ReflectionClass $componentType = null );

    /**
     * Returns a List of components of a certain componentType
     * 
     * The list is ordered by instantiation order, starting with the components
     * instantiated first at the beginning.
     *
     * @param ReflectionClass $componentType the searched type.
     * @return Xyster_Collection_List a List of components.
     * @throws Exception if the instantiation of a component fails
     */
    function getComponents( ReflectionClass $componentType );
}