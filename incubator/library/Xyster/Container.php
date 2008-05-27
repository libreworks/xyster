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
 * @see Xyster_Collection_Set
 */
require_once 'Xyster/Collection/Set.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * The standard container implementation.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container implements Xyster_Container_Mutable, Xyster_Container_Monitor_Strategy
{
    /**
     * @var Xyster_Collection_List
     */
    private $_adapters;
    
    /**
     * @var Xyster_Collection_Set
     */
    private $_children;
    
    /**
     * @var Xyster_Collection_Map
     */
    private $_componentKeyToAdapterCache;
    
    /**
     * @var Xyster_Container_Adapter_Factory
     */
    private $_componentFactory;

    /**
     * @var Xyster_Container_Monitor
     */
    private $_monitor;
    
    /**
     * @var Xyster_Container_Interface
     */
    private $_parent;
    
    /**
     * @var Xyster_Collection_Map_Interface
     */
    private $_properties;
    
    /**
     * @var Xyster_Collection_Map_Interface
     */
    protected $_with;
    
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
    public function __construct( Xyster_Container_Adapter_Factory $factory = null, Xyster_Container_Interface $parent = null, Xyster_Container_Monitor $monitor = null )
    {
        $this->_adapters = new Xyster_Collection_List;
        $this->_properties = new Xyster_Collection_Map_String;
        $this->_children = new Xyster_Collection_Set;
        $this->_componentKeyToAdapterCache = new Xyster_Collection_Map;
        
        if ( $factory === null ) {
            require_once 'Xyster/Container/Behavior/Factory/Adaptive.php';
            $factory = new Xyster_Container_Behavior_Factory_Adaptive;
        }
        $this->_componentFactory = $factory;
        $this->_parent = $parent;
        $this->_parent = ( $parent != null && !($parent instanceof Xyster_Container_Empty) ) ? 
            new Xyster_Container_Immutable($parent) : $parent;
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
        $this->_componentFactory->accept($visitor); // will cascade through behaviors
        foreach( $this->_getModifiableComponentAdapterList() as $adapter ) {
            /* @var $adapter Xyster_Container_Adapter */
            $adapter->accept($visitor);
        }
        foreach( $this->_children as $child ) {
            $child->accept($visitor);
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
        if ( $properties === null ) {
            $properties = $this->_properties;
        }
        
        $tempProps = clone $properties;
        require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
        require_once 'Xyster/Container/Features.php';
        $this->_processWith($tempProps);
        $behaviors = ( Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($tempProps,
            Xyster_Container_Features::NONE()) == false &&
            $this->_componentFactory instanceof Xyster_Container_Behavior_Factory );
        if ( $behaviors ) {
            $factory = $this->_componentFactory; /* @var $factory Xyster_Container_Behavior_Factory */
            $adapter = $factory->addComponentAdapter($this->_monitor,
                $tempProps, $adapter);
        }
        $this->_addAdapterInternal($adapter);
        if ( $behaviors ) {
            $this->_throwIfPropertiesLeft($tempProps);
        }
        
        return $this;
    }

    /**
     * Adds a child container
     * 
     * This action will ilst the child as exactly that in the parents scope.  It
     * will not change the child's view of a parent.  That is determined by the
     * constructor arguments of the child itself.
     *
     * @param Xyster_Container_Interface $child
     * @return Xyster_Container_Mutable provides a fluent interface
     */
    public function addChildContainer(Xyster_Container_Interface $child)
    {
        $this->_checkCircularDependencies($child);
        $this->_children->add($child);
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
        if ( $key === null ) {
            if ( $implementation instanceof Xyster_Type ) {
                $key = $implementation;
            } else if ( is_string($implementation) || $implementation instanceof ReflectionClass ) {
                $key = new Xyster_Type($implementation);
                $implementation = $key;
            }
        } else if ( is_string($implementation) || $implementation instanceof ReflectionClass ) {
            $implementation = new Xyster_Type($implementation);
        }
        
        if ( $implementation instanceof Xyster_Type ) {
            $tempProps = clone $this->_properties;
            $this->_processWith($tempProps);
            $adapter = $this->_componentFactory->createComponentAdapter($this->_monitor,
                $tempProps, $key, $implementation, $parameters);
            $this->_throwIfPropertiesLeft($tempProps);
            $this->_addAdapterInternal($adapter);
        }

        return $this; 
    }
    
    /**
     * Register a component
     * 
     * {@inherit}
     *
     * @param mixed $instance an instance of the compoent
     * @param mixed $key a key unique within the container that identifies the component
     * @return Xyster_Container provides a fluent interface
     * @throws Xyster_Container_Exception if registration of the component fails
     */
    public function addComponentInstance( $instance, $key = null )
    {
        if ( $key === null ) {
            $key = Xyster_Type::of($instance);
        }
        
        $properties = clone $this->_properties;
        $this->_processWith($properties);
        require_once 'Xyster/Container/Adapter/Instance.php';
        $adapter = new Xyster_Container_Adapter_Instance($key, $instance,
            $this->_monitor);
        return $this->addAdapter($adapter, $properties);
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
        require_once 'Xyster/Container/Adapter/Instance.php';
        return $this->_addAdapterInternal(new Xyster_Container_Adapter_Instance($name,
            $value, $this->_monitor));
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
        foreach( $this->_getModifiableComponentAdapterList() as $adapter ) {
            if ( $adapter instanceof Xyster_Container_Monitor_Strategy ) {
                /* @var $adapter Xyster_Container_Monitor_Strategy */
                $adapter->changeMonitor($monitor);
            }
        }
        foreach( $this->_children as $child ) {
            if ( $child instanceof Xyster_Container_Monitor_Strategy ) {
                /* @var $child Xyster_Container_Monitor_Strategy */
                $child->changeMonitor($monitor);
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
     * @param Xyster_Type $into the type about to be injected into
     * @return object an instantiated component, or null if no component has been registered for the specified key
     */
    public function getComponent($componentKeyOrType, Xyster_Type $into = null)
    {
        $return = null;
        $adapter = null;
        
        // consult PicoContainer to see what the hell the IntoThreadLocal is
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

        foreach( $this->_getModifiableComponentAdapterList() as $adapter ) {
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
        $adapter = $this->_getComponentKeyToAdapterCache()->get($componentKey);
        if ( $adapter === null && $this->_parent !== null ) {
            $adapter = $this->_parent->getComponentAdapter($componentKey);
        }
        return $adapter;
    }
    
    /**
     * Find a component adapter associated with the specified key
     * 
     * @param mixed $componentType the key that the component was registered with
     * @param Xyster_Container_NameBinding $nameBinding the name binding of the parameter
     * @return Xyster_Container_Adapter the component adapter associated with this key, or null
     */
    public function getComponentAdapterByType( $componentType, Xyster_Container_NameBinding $nameBinding = null )
    {
        if ( ! $componentType instanceof Xyster_Type && $componentType !== null ) {
            $componentType = new Xyster_Type($componentType);
        }
        
        $adapter = $this->getComponentAdapter($componentType);
        if ( $adapter === null ) {
            $found = $this->getComponentAdapters($componentType);
            if ( $found->isEmpty() ) {
                return ( $this->_parent !== null ) ?
                    $this->_parent->getComponentAdapterByType($componentType, $nameBinding) : null;
            } else if ( count($found) == 1 ) {
                return $found->get(0);
            } else {
                if ( $nameBinding !== null ) {
                	$parameterName = $nameBinding->getName();
                	if ( $parameterName !== null ) {
	                    $ca = $this->getComponentAdapter($parameterName);
	                    if ( $ca !== null && $componentType->isAssignableFrom($ca->getImplementation()) ) {
	                        $adapter = $ca;
	                    }
                	}
                } else {
                	$foundClasses = array();
                	foreach( $found as $foundAdapter ) {
                		/* @var $foundAdapter Xyster_Container_Adapter */
                		$foundClasses[] = $foundAdapter->getImplementation();
                	}
                    require_once 'Xyster/Container/Exception.php';
                    throw new Xyster_Container_Exception('Ambiguous component resolution: '
                        . $componentType . ', found ' . implode(',', $foundClasses));
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
        	$list = Xyster_Collection::fixedList($this->_getModifiableComponentAdapterList());
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
     * Retrieve the parent container of this container
     *
     * @return Xyster_Container_Instance or null if no parent exists
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Make a child container using the same implementation as the parent
     * 
     * It will have a reference to this as parent.  This will list the resulting
     * container as a child.
     *
     * @return Xyster_Container_Mutable the new child container
     */
    public function makeChildContainer()
    {
        $mc = new Xyster_Container($this->_componentFactory, $this);
        $this->addChildContainer($mc);
        return $mc;
    }
    
    /**
     * Removes a child container from this container
     * 
     * It will not change the child's view of a parent.
     *
     * @param Xyster_Container_Interface $child
     * @return boolean true if the child container has been removed
     */
    public function removeChildContainer(Xyster_Container_Interface $child)
    {
        return $this->_children->remove($child);
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
        
        $cache = $this->_getComponentKeyToAdapterCache();
        if ( $cache->containsKey($componentKey) ) {
            $adapter = $cache->get($componentKey);
            $cache->remove($componentKey);
            $this->_getModifiableComponentAdapterList()->remove($adapter);
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
        foreach( $this->_getModifiableComponentAdapterList() as $adapter ) {
            /* @var $adapter Xyster_Container_Adapter */
            if ( Xyster_Type::areDeeplyEqual($this->_getLocalInstance($adapter), $componentInstance) ) {
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
    public function with( Xyster_Collection_Map_Interface $properties )
    {
        $this->_with = $properties;
        return $this;
    }
    
    /**
     * Adds the adapter
     *
     * @param Xyster_Container_Adapter $adapter
     * @return Xyster_Container
     */
    protected function _addAdapterInternal( Xyster_Container_Adapter $adapter )
    {
        $key = $adapter->getKey();
        if ( $this->_getComponentKeyToAdapterCache()->containsKey($key) ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Duplicate keys not allowed ' . 
                '(duplicate for "'. $key .'"');
        }
        $this->_getModifiableComponentAdapterList()->add($adapter);
        $this->_getComponentKeyToAdapterCache()->set($key, $adapter);
        return $this;
    }
    
    /**
     * Gets the component key to adapter map
     *
     * @return Xyster_Collection_Map
     */
    protected function _getComponentKeyToAdapterCache()
    {
    	return $this->_componentKeyToAdapterCache;
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
        $isLocal = $this->_getModifiableComponentAdapterList()->contains($adapter);
        
        if ( $isLocal ) {
            $instance = $adapter->getInstance($this);
        } else if ( $this->_parent !== null ) {
            $instance = $this->_parent->getComponent($adapter->getKey());
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
     * Gets the adapters
     *
     * @return Xyster_Collection_List
     */
    protected function _getModifiableComponentAdapterList()
    {
    	return $this->_adapters;
    }
    
    /**
     * Checks for identical references in the child container
     * 
     * It doesn't traverse an entire hierarchy, namely it simply checks for
     * child containers tht are identical to the current container.
     *
     * @param Xyster_Container_Interface $child
     */
    private function _checkCircularChildDependencies( Xyster_Container_Interface $child )
    {
        $message = "Cannot have circular dependency between parent " . get_class($this) .
            " and child: " . get_class($child);
        if ( $child === $this ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception($message);
        }
        if ( $child instanceof Xyster_Container_Delegating_Abstract ) {
            $delegateChild = $child;
            while( $delegateChild !== null ) {
                $delegateInstance = $delegateChild->getDelegate();
                if ( $this === $delegateInstance ) {
                    require_once 'Xyster/Container/Exception.php';
                    throw new Xyster_Container_Exception($message);
                }
                $delegateChild = ( $delegateInstance instanceof Xyster_Container_Delegating_Abstract ) ?
                    $delegateInstance : null;
            }
        }
    }
    
    /**
     * Merges supplied properties with those from the with() method
     *
     * @param Xyster_Collection_Map_Interface $properties
     */
    private function _processWith( Xyster_Collection_Map_Interface $properties )
    {
        if ( $this->_with instanceof Xyster_Collection_Map_Interface ) {                                                     
            $properties->merge($this->_with);                                                                                
            $this->_with = null;
        } 
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
            throw new Xyster_Container_Exception('Unprocessed properties: ' . print_r($properties->toArray(), true));
        }
    }
}