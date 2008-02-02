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
 * @see Xyster_Container_Interface
 */
require_once 'Xyster/Container/Interface.php';
/**
 * Empty container serving as a null value object
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Empty implements Xyster_Container_Interface
{
	/**
     * Accepts a visitor that should visit the child containers, component adapters and component instances.
     *
     * @param visitor the visitor
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
    }
    
    /**
     * Retrieve a component instance registered with a specific key or type
     *
     * @param mixed $componentKeyOrType the key or Type that the component was registered with
     * @return object an instantiated component, or null if no component has been registered for the specified key
     */
    public function getComponent($componentKeyOrType)
    {
    	return null;
    }

    /**
     * Retrieve all the registered component instances in the container
     * 
     * The components are returned in their order of instantiation, which
     * depends on the dependency order between them.
     * 
     * If the type parameter is supplied, this method returns the components of
     * the specified type. 
     *
     * @param Xyster_Type $componentType the type to search
     * @return Xyster_Collection_List all the components.
     */
    public function getComponents( Xyster_Type $componentType = null )
    {
    	require_once 'Xyster/Collection.php';
    	return Xyster_Collection::emptyList();
    }
    
    /**
     * Find a component adapter associated with the specified key
     * 
     * @param mixed $componentKey the key that the component was registered with
     * @return Xyster_Container_Adapter the component adapter associated with this key, or null
     */
    public function getComponentAdapter( $componentKey )
    {
    	return null;
    }
    
    /**
     * Find a component adapter by type (and optionally by parameter name)
     *
     * @param mixed $componentType String class name or Xyster_Type
     * @param Xyster_Container_NameBinding $nameBinding the parameter binding
     */
    public function getComponentAdapterByType( $componentType, Xyster_Container_NameBinding $nameBinding = null )
    {
    	return null;
    }

    /**
     * Retrieve all the component adapters inside this container.
     * 
     * If the type is supplied, this method returns the adapters associated with
     * the specified type.
     *
     * @return Xyster_Collection_List a fixed collection containing all the adapters inside this container
     */
    public function getComponentAdapters( Xyster_Type $componentType = null )
    {
        require_once 'Xyster/Collection.php';
        return Xyster_Collection::emptyList();
    }
}