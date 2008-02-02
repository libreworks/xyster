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
 * @see Xyster_Container_Delegating_Abstract
 */
require_once 'Xyster/Container/Delegating/Abstract.php';
/**
 * @see Xyster_Container_Mutable
 */
require_once 'Xyster/Container/Mutable.php';
/**
 * Abstract base class for delegation to a mutable container 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Delegating_MutableAbstract extends Xyster_Container_Delegating_Abstract implements Xyster_Container_Mutable
{
	/**
	 * Creates a new mutable delegate container
	 *
	 * @param Xyster_Container_Mutable $delegate
	 */
	public function __construct( Xyster_Container_Mutable $delegate )
	{
		parent::__construct($delegate);
	}
	
	/**
     * Register a component via an Adapter
     *
     * @param Xyster_Container_Adapter $componentAdapter the addAdapter
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration fails
     */
    public function addAdapter(Xyster_Container_Adapter $componentAdapter, Xyster_Collection_Map_Interface $properties = null)
    {
    	$this->getDelegate()->addAdapter($componentAdapter, $properties);
    	return $this;
    }
    
    /**
     * Register a component
     *
     * @param mixed $implementation the component's implementation class
     * @param mixed $key a key unique within the container that identifies the component
     * @param mixed $parameters the parameters that gives hints about what arguments to pass
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration of the component fails
     */
    public function addComponent($implementation, $key = null, array $parameters = null)
    {
        $this->getDelegate()->addComponent($implementation, $key, $parameters);
        return $this;
    }

    /**
     * Register a component instance
     *
     * @param mixed $instance an instance of the component
     * @param mixed $key a key unique within the container that identifies the component
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration of the component fails
     */
    public function addComponentInstance($instance, $key = null)
    {
    	$this->getDelegate()->addComponentInstance($instance, $key);
    	return $this;
    }
    
    /**
     * Register a config item
     *
     * @param string $name the name of the config item
     * @param mixed $val the value of the config item
     * @return Xyster_Container_Mutable provides a fluent interface
     * @throws Xyster_Container_Exception if registration fails
     */
    public function addConfig($name, $val)
    {
    	$this->getDelegate()->addConfig($name, $val);
    	return $this;
    }

    /**
     * You can change the characteristic of registration of all subsequent components in this container
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable provides a fluent interface
     */
    public function change( Xyster_Collection_Map_Interface $properties )
    {
    	$this->getDelegate()->change($properties);
    	return $this;
    }

    /**
     * Gets the delegate for this container
     *
     * @return Xyster_Container_Mutable
     */
    public function getDelegate()
    {
    	return parent::getDelegate();
    }
    
    /**
     * Unregister a component by key
     *
     * @param mixed $componentKey key of the component to unregister.
     * @return Xyster_Container_Adapter the adapter that was associated with this component
     */
    public function removeComponent($componentKey)
    {
    	return $this->getDelegate()->removeComponent($componentKey);
    }

    /**
     * Unregister a component by instance
     *
     * @param mixed $componentInstance the component instance to unregister.
     * @return Xyster_Container_Adapter the adapter removed
     */
    public function removeComponentByInstance($componentInstance)
    {
    	return $this->getDelegate()->removeComponentByInstance($componentInstance);
    }

    /**
     * You can set for the following operation only the characteristic of registration of a component on the fly
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable the same instance with temporary properties
     */
    public function with( Xyster_Collection_Map_Interface $properties )
    {
    	$this->getDelegate()->with($properties);
    	return $this;
    }
}