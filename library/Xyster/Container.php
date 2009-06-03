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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * @see Xyster_Container_IMutable
 */
require_once 'Xyster/Container/IMutable.php';
/**
 * Mutable container interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container implements Xyster_Container_IMutable
{
    /**
     * @var Xyster_Container_IContainer
     */
    protected $_parent;
    
    /**
     * @var Xyster_Container_IProvider[]
     */
    protected $_providers = array();
    
    protected $_types = array();
    
    /**
     * Creates a new container.
     * 
     * @param Xyster_Container_IContainer $parent The parent container
     */
    public function __construct(Xyster_Container_IContainer $parent = null)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Adds a definition to the container and autowires its dependencies based on the constructor.
     * 
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    public function autowire($type, $name = null)
    {
        return $this;
    }
    
    /**
     * Adds a definition to the container and autowires its dependencies.
     *
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @param array $except Optional.  An array of property names to ignore.
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    public function autowireByName($type, $name = null, array $except = array())
    {
        return $this;
    }   
    
    /**
     * Adds a definition to the container and autowires its dependencies.
     *
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @param array $except Optional.  An array of property names to ignore.
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    public function autowireByType($type, $name = null, array $except = array())
    {
        return $this;
    }
    
    /**
     * Adds a definition to the container.
     * 
     * @param Xyster_Container_Definition $definition The component definition
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    public function add(Xyster_Container_Definition $definition)
    {
        return $this;
    }
    
    /**
     * Adds a provider to the container.
     * 
     * @param Xyster_Container_IProvider $provider The provider
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    public function addProvider(Xyster_Container_IProvider $provider)
    {
        return $this;
    }
    
    /**
     * Whether this container contains a component with the given name.
     * 
     * @param string $name The component name
     * @return boolean
     */
    public function contains($name)
    {
        return array_key_exists($name, $this->_providers);
    }
    
    /**
     * Whether this container contains a component with the given type.
     * 
     * @param mixed $type A Xyster_Type or the name of a class
     * @return boolean
     */
    public function containsType($type)
    {
        $realType = $type instanceof Xyster_Type ? $type : new Xyster_Type($type);
        return in_array($realType->getName(), $this->_types); 
    }
    
    /**
     * Gets the component by name.
     * 
     * @param string $name The component name
     * @return object The component
     */
    public function get($name)
    {
        
    }
    
    /**
     * Gets the components in the contanier for the given type.
     * 
     * @param mixed $type A Xyster_Type or string class name
     * @return array Keys are component names, values are components themselves
     */
    public function getForType($type)
    {
        
    }
    
    /**
     * Gets the component names given a type.
     * 
     * If the type argument is omitted, this will return all component names.
     * 
     * @param mixed $type Optional. A Xyster_Type or string class name
     * @return array of strings
     */
    public function getNames($type = null)
    {
        if ( $type === null ) {
            return array_keys($this->_providers);
        } else {
            // @todo Implement
        }
    }
    
    /**
     * Gets the parent container.
     * 
     * @return Xyster_Container_IContainer
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Gets the type of component with the given name.
     * 
     * @param string $name The component name
     * @return Xyster_Type The component type
     */
    public function getType($name)
    {
        return $this->contains($name) ?
            $this->_providers[$name]->getType() : null;
    }   
}