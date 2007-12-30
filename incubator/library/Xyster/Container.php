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
 * @see Xyster_Container_Mutable
 */
require_once 'Xyster/Container/Mutable.php';
/**
 * @see Xyster_Container_Monitor_Strategy
 */
require_once 'Xyster/Container/Monitor/Strategy.php';
/**
 * @see Xyster_Collection_List
 */
require_once 'Xyster/Collection/List.php';
/**
 * @see Xyster_Collection_Map
 */
require_once 'Xyster/Collection/Map.php';
/**
 * @see Xyster_Collection_Map_String
 */
require_once 'Xyster/Collection/Map/String.php';
/**
 * The standard container implementation.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container implements Xyster_Container_Mutable, Xyster_Container_Monitor_Strategy
{
    /**
     * @var Xyster_Collection_List
     */
    protected $_adapters;
    
    /**
     * @var Xyster_Collection_Map
     */
    protected $_componentKeyToAdapterCache;
    
    /**
     * @var Xyster_Container_Adapter_Factory
     */
    protected $_componentFactory;

    /**
     * @var Xyster_Container_Monitor
     */
    protected $_monitor;
    
    /**
     * @var Xyster_Collection_Map_Interface
     */
    protected $_properties;
    
    /**
     * Creates a new container
     * 
     * Important note about caching: If you intend the components to be cached,
     * you should pass in a factory that creates {@link Xyster_Container_Behavior_Cached}
     * instances, such as for example {@link Xyster_Container_Behavior_Factory_Cached}
     * which can delegate to other Adapter Factories.
     *
     * @param Xyster_Container_Adapter_Factory $factory
     * @param Xyster_Container_Monitor $monitor
     */
    public function __construct( Xyster_Container_Adapter_Factory $factory = null, Xyster_Container_Monitor $monitor = null )
    {
        $this->_adapters = new Xyster_Collection_List;
        $this->_properties = new Xyster_Collection_Map_String;
        $this->_componentKeyToAdapterCache = new Xyster_Collection_Map;
        
        if ( $factory === null ) {
            require_once 'Xyster/Container/Behavior/Factory/Adaptive.php';
            $factory = new Xyster_Container_Behavior_Factory_Adaptive;
        }
        $this->_componentFactory = $factory;
        
        if ( $monitor === null ) {
            require_once 'Xyster/Container/Monitor/Null.php';
            $monitor = new Xyster_Container_Monitor_Null;
        }
        $this->_monitor = $monitor;
    }
    
    /**
     * Accepts a visitor that should visit the child containers, component adapters and component instances.
     *
     * @param visitor the visitor
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
        $visitor->visitContainer($this);
        foreach( $this->_adapters as $adapter ) {
            /* @var $adapter Xyster_Container_Adapter */
            $adapter->accept($visitor);
        }
    }
    
    /**
     * Register a component via an Adapter
     *
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Collection_Map_Interface $properties
     */
    public function addAdapter( Xyster_Container_Adapter $adapter, Xyster_Collection_Map_Interface $properties = null )
    {
        if ( $properties == null ) {
            $properties = $this->_properties;
        }
        
        $tempProps = clone $properties;
        require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
        require_once 'Xyster/Container/Features.php';
        if ( Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($tempProps, Xyster_Container_Features::NONE()) == false &&
            $this->_componentFactory instanceof Xyster_Container_Behavior_Factory ) {
            $factory = $this->_componentFactory; /* @var $factory Xyster_Container_Behavior_Factory */
            $this->_addAdapterInternal($factory->addComponentAdapter($this->_monitor,
                $tempProps, $adapter));
            $this->_throwIfPropertiesLeft($tempProps);
        } else {
            $this->_addAdapterInternal($adapter);
        }
        
        return $this;
    }
    
    /**
     * Register a component
     * 
     * {@inherit}
     *
     * @param mixed $implementation the component's implementation class
     * @param mixed $key a key unique within the container that identifies the component
     * @param mixed $parameters the parameters that gives hints about what arguments to pass
     * @return Xyster_Container provides a fluent interface
     * @throws Xyster_Container_Exception if registration of the component fails
     */
    public function addComponent($implementation, $key = null, array $parameters = null)
    {
        if ( !count($parameters) ) {
            $parameters = null;
        }
        if ( $key == null ) {
            require_once 'Xyster/Type.php';
            if ( $implementation instanceof Xyster_Type ) {
                $key = $implementation;
            } else if ( is_string($implementation) || $implementation instanceof ReflectionClass ) {
                $key = new Xyster_Type($implementation);
                $implementation = $key;
            }
        }
        
        $tempProps = clone $this->_properties;
        $adapter = $this->_componentFactory->createComponentAdapter($this->_monitor,
            $tempProps, $key, $implementation, $parameters);
        $this->_throwIfPropertiesLeft($tempProps);
        $this->_addAdapterInternal($adapter);
        
        return $this;
    }
    
    /**
     * Register a component
     * 
     * {@inherit}
     *
     * @param mixed $instance an instance of the compoent
     * @param mixed $key a key unique within the container that identifies the component
     * @param mixed $parameters the parameters that gives hints about what arguments to pass
     * @return Xyster_Container provides a fluent interface
     * @throws Xyster_Container_Exception if registration of the component fails
     */
    public function addComponentInstance( $instance, $key = null, array $parameters = null)
    {
        if ( !count($parameters) ) {
            $parameters = null;
        }
        if ( $key === null ) {
            require_once 'Xyster/Type.php';
            $key = new Xyster_Type(is_object($instance)
                ? get_class($instance) : gettype($instance));
        }
        
        require_once 'Xyster/Container/Adapter/Instance.php';
        $adapter = new Xyster_Container_Adapter_Instance($key, $instance,
            $this->_monitor);
        $this->addAdapter($adapter, $this->_properties);
        
        return $this;
    }
    
    /**
     * Register a config item
     *
     * @param string $name
     * @param mixed $value
     * @return Xyster_Container provides a fluent interface
     */
    public function addConfig( $name, $value )
    {
        return $this;
    }
    
    /**
     * You can change the characteristic of registration of all subsequent components in this container
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container provides a fluent interface
     */
    public function change( Xyster_Collection_Map_Interface $properties )
    {
        $this->_properties->merge($properties);
        return $this;
    }
    
    /**
     * Changes the component monitor used
     * 
     * @param Xyster_Container_Monitor $monitor the new monitor to use
     */
    public function changeMonitor( Xyster_Container_Monitor $monitor )
    {
        $this->_monitor = $monitor;
        foreach( $this->_adapters as $adapter ) {
            if ( $adapter instanceof Xyster_Container_Monitor_Strategy ) {
                /* @var $adapter Xyster_Container_Monitor_Strategy */
                $adapter->changeMonitor($monitor);
            }
        }
    }
    
    /**
     * Returns the first current monitor found in the Component Factory
     * {@inheritDoc}
     *
     * @return Xyster_Container_Monitor
     */
    public function currentMonitor()
    {
        return $this->_monitor;
    }
    
    /**
     * Retrieve a component instance registered with a specific key or type
     *
     * @param mixed $componentKeyOrType the key or Type that the component was registered with
     * @return object an instantiated component, or null if no component has been registered for the specified key
     */
    public function getComponent($componentKeyOrType)
    {
        $return = null;
        $adapter = null;
        
        if ( $componentKeyOrType instanceof Xyster_Type ) {
            $adapter = $this->getComponentAdapterByType($componentKeyOrType, null);
        } else {
            $adapter = $this->getComponentAdapter($componentKeyOrType);
        }
        
        $return = $adapter == null ? null : $this->_getInstance($adapter);
        if ( $return === null ) {
            $return = $this->_monitor->noComponentFound($this, $componentKeyOrType);
        }
        
        return $return;
    }

    /**
     * Retrieve all the registered component instances in the container
     * 
     * If the type parameter is supplied, this method returns the components of
     * the specified type. 
     *
     * @param Xyster_Type $componentType the type to search
     * @return Xyster_Collection_List all the components.
     * @throws Exception if the instantiation of the component fails
     */
    public function getComponents( Xyster_Type $componentType = null )
    {
        $result = new Xyster_Collection_List;

        $adapterToInstanceMap = new Xyster_Collection_Map;
        foreach( $this->_adapters as $adapter ) {
            /* @var $adapter Xyster_Container_Adapter */
            if ( $componentType == null || $componentType->isAssignableFrom($adapter->getImplementation()) ) {
                $instance = $this->_getLocalInstance($adapter);
                $result->add($instance);
            }
        }
        
        return $result;
    }
    
    /**
     * Find a component adapter associated with the specified key
     * 
     * @param mixed $componentKey the key that the component was registered with
     * @return Xyster_Container_Adapter the component adapter associated with this key, or null
     */
    public function getComponentAdapter( $componentKey )
    {
        return $this->_componentKeyToAdapterCache->get($componentKey);
    }
    
    /**
     * Find a component adapter associated with the specified key
     * 
     * @param mixed $componentType the key that the component was registered with
     * @param string $componentParameterName the name of the parameter
     * @return Xyster_Container_Adapter the component adapter associated with this key, or null
     */
    public function getComponentAdapterByType( $componentType, $componentParameterName = null )
    {
        if ( ! $componentType instanceof Xyster_Type ) {
            $componentType = new Xyster_Type($componentType);
        }
        
        $adapter = $this->getComponentAdapter($componentType);
        if ( $adapter == null ) {
            $found = $this->getComponentAdapters($componentType);
            if ( $found->isEmpty() ) {
                return null;
            } else if ( count($found) == 1 ) {
                return $found->get(0);
            } else {
                if ( $componentParameterName != null ) {
                    $ca = $this->getComponentAdapter($componentParameterName);
                    if ( $ca != null && $componentType->isAssignableFrom($ca->getImplementation()) ) {
                        $adapter = $ca;
                    }
                } else {
                    require_once 'Xyster/Container/Exception.php';
                    throw new Xyster_Container_Exception('Ambiguous component resolution');
                }
            }
        }
        
        return $adapter;
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
        $list = null;
        
        if ( $componentType == null ) {
            require_once 'Xyster/Collection/List.php';
            $list = new Xyster_Collection_List($this->_adapters); 
        } else {
            $list = new Xyster_Collection_List;
            foreach( $this->_adapters as $adapter ) {
                if ( $componentType->isAssignableFrom($adapter->getImplementation()) ) {
                    $list->add($adapter);
                }
            }
        }
        
        return $list;
    }
    
    /**
     * Unregister a component by key
     *
     * @param mixed $componentKey key of the component to unregister.
     * @return Xyster_Container_Adapter the adapter that was associated with this component
     */
    public function removeComponent($componentKey)
    {
        $adapter = null;
        
        if ( $this->_componentKeyToAdapterCache->containsKey($componentKey) ) {
            $adapter = $this->_componentKeyToAdapterCache->get($componentKey);
            $this->_componentKeyToAdapterCache->remove($componentKey);
            $this->_adapters->remove($adapter);
        }
        
        return $adapter;
    }
    
    
    /**
     * Unregister a component by instance
     *
     * @param mixed $componentInstance the component instance to unregister.
     * @return Xyster_Container_Adapter the adapter removed
     */
    public function removeComponentByInstance($componentInstance)
    {
        foreach( $this->_adapters as $adapter ) {
            /* @var $adapter Xyster_Container_Adapter */
            if ( $this->_getLocalInstance($adapter) == $componentInstance ) {
                return $this->removeComponent($adapter->getKey());
            }
        }
    }

    /**
     * You can set for the following operation only the characteristic of registration of a component on the fly
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @return Xyster_Container_Mutable the same instance with temporary properties
     */
    public function with()
    {
        
    }
    
    /**
     * Adds the adapter
     *
     * @param Xyster_Container_Adapter $adapter
     */
    protected function _addAdapterInternal( Xyster_Container_Adapter $adapter )
    {
        $key = $adapter->getKey();
        if ( $this->_componentKeyToAdapterCache->containsKey($key) ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Duplicate keys not allowed ' . 
                '(duplicate for "'. $key .'"');
        }
        $this->_adapters->add($adapter);
        $this->_componentKeyToAdapterCache->set($key, $adapter);
    }
    
    /**
     * Gets an instance of the component
     *
     * @param Xyster_Container_Adapter $adapter
     * @return object
     */
    protected function _getInstance( Xyster_Container_Adapter $adapter )
    {
        $instance = null;
        
        if ( $this->_adapters->contains($adapter) ) {
            $instance = $adapter->getInstance($this);
        }
        
        return $instance;
    }
    
    /**
     * Gets the local instance of an adapter
     *
     * @param Xyster_Container_Adapter $adapter
     * @return mixed
     */
    protected function _getLocalInstance( Xyster_Container_Adapter $adapter )
    {
        $instance = $adapter->getInstance($this);
        return $instance;
    }
    
    /**
     * Throws an exception if the properties map still has values
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @throws Xyster_Container_Exception
     */
    private function _throwIfPropertiesLeft( Xyster_Collection_Map_Interface $properties )
    {
        if ( !$properties->isEmpty() ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Unprocessed properties: ' . $properties);
        }
    }
}