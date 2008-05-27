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
 * Abstract base class for immutable delegation to a container 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Delegating_Abstract implements Xyster_Container_Interface
{
	/**
	 * @var Xyster_Container_Interface
	 */
	private $_delegate;
	
	/**
	 * Creates a new delegating container
	 *
	 * @param Xyster_Container_Interface $delegate
	 */
	public function __construct( Xyster_Container_Interface $delegate )
	{
		$this->_delegate = $delegate;
	}
	
    /**
     * Accepts a visitor that should visit the child containers, component adapters and component instances.
     *
     * @param visitor the visitor
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
    	$this->_delegate->accept($visitor);
    }
    
    /**
     * Retrieve a component instance registered with a specific key or type
     *
     * @param mixed $componentKeyOrType the key or Type that the component was registered with
     * @param Xyster_Type $into the type about to be injected into
     * @return object an instantiated component, or null if no component has been registered for the specified key
     */
    public function getComponent($componentKeyOrType, Xyster_Type $into = null)
    {
    	return $this->_delegate->getComponent($componentKeyOrType, $into);
    }
    
    /**
     * Find a component adapter associated with the specified key
     * 
     * @param mixed $componentKey the key that the component was registered with
     * @return Xyster_Container_Adapter the component adapter associated with this key, or null
     */
    public function getComponentAdapter( $componentKey )
    {
    	return $this->_delegate->getComponentAdapter($componentKey);
    }
    
    /**
     * Find a component adapter by type (and optionally by parameter name)
     *
     * @param mixed $componentType String class name or Xyster_Type
     * @param Xyster_Container_NameBinding $nameBinding the parameter binding
     */
    public function getComponentAdapterByType( $componentType, Xyster_Container_NameBinding $nameBinding = null )
    {
    	return $this->_delegate->getComponentAdapterByType($componentType, $nameBinding);
    }

    /**
     * Retrieve all the component adapters inside this container.
     *
     * @return Xyster_Collection_List a fixed collection containing all the adapters inside this container
     */
    public function getComponentAdapters( Xyster_Type $componentType = null )
    {
    	return $this->_delegate->getComponentAdapters($componentType);
    }

    /**
     * Retrieve all the registered component instances in the container 
     *
     * @param Xyster_Type $componentType the type to search
     * @return Xyster_Collection_List all the components.
     * @throws Exception if the instantiation of the component fails
     */
    public function getComponents( Xyster_Type $componentType = null )
    {
        return $this->_delegate->getComponents($componentType);
    }
    
    /**
     * Gets the delegate container
     *
     * @return Xyster_Container_Interface
     */
    public function getDelegate()
    {
    	return $this->_delegate;
    }
    
    /**
     * Gets the parent container of this container
     *
     * @return Xyster_Container_Interface
     */
    public function getParent()
    {
        return $this->_delegate->getParent();
    }
}