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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Container;
use Xyster\Type\Type;
/**
 * The standard implementation of the dependency injection container.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Container implements IMutable
{
    /**
     * @var IContainer
     */
    protected $_parent;
    /**
     * @var \Xyster\Container\Provider\IProvider[]
     */
    protected $_providers = array();
    protected $_types = array();

    /**
     * Creates a new container.
     * 
     * @param IContainer $parent The parent container
     */
    public function __construct(IContainer $parent = null)
    {
        $this->_parent = $parent;
    }

    /**
     * Adds a definition to the container and autowires its dependencies based on the constructor.
     * 
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @return IMutable provides a fluent interface
     */
    public function autowire($type, $name = null)
    {
        return $this->addProvider(new Injector\Autowiring(
                        new Definition($type, $name), Autowire::Constructor()));
    }

    /**
     * Adds a definition to the container and autowires its dependencies.
     *
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @param array $except Optional.  An array of property names to ignore.
     * @return IMutable provides a fluent interface
     */
    public function autowireByName($type, $name = null, array $except = array())
    {
        return $this->addProvider(new Injector\Autowiring(
                        new Definition($type, $name), Autowire::ByName(), $except));
    }

    /**
     * Adds a definition to the container and autowires its dependencies.
     *
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @param array $except Optional.  An array of property names to ignore.
     * @return IMutable provides a fluent interface
     */
    public function autowireByType($type, $name = null, array $except = array())
    {
        return $this->addProvider(new Injector\Autowiring(
                        new Definition($type, $name), Autowire::ByType(), $except));
    }

    /**
     * Adds a definition to the container.
     * 
     * @param Definition $definition The component definition
     * @return IMutable provides a fluent interface
     */
    public function add(Definition $definition)
    {
        return $this->addProvider(new Injector\Standard($definition));
    }

    /**
     * Adds a provider to the container.
     * 
     * @param Provider\IProvider $provider The provider
     * @return IMutable provides a fluent interface
     */
    public function addProvider(Provider\IProvider $provider)
    {
        if (in_array($provider, $this->_providers, true) ||
                array_key_exists($provider->getName(), $this->_providers)) {
            throw new ContainerException('A component with the name "' .
                    $provider->getName() . '" is already registered');
        }
        $this->_types[] = $provider->getType()->getName();
        $this->_providers[$provider->getName()] = $provider;
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
     * @param mixed $type A \Xyster\Type\Type or the name of a class
     * @return boolean
     */
    public function containsType($type)
    {
        $realType = $type instanceof Type ? $type : new Type($type);
        /* @var $realType \Xyster\Type\Type */
        foreach ($this->_types as $ctype) {
            if ($realType->isAssignableFrom($ctype)) {
                return true;
            }
        }
    }

    /**
     * Creates a new definition.
     *
     * This method is just for convenience.  Prevents having to create a new
     * definition and then populating it, then passing it to the add method.
     *
     * @param mixed $type A Xyster_Type or the name of a class
     * @param string $name Optional. The component name.
     * @return Definition the definition created
     */
    static public function definition($type, $name = null)
    {
        return new Definition($type, $name);
    }

    /**
     * Gets the component by name.
     * 
     * @param string $name The component name
     * @param Type $into Optional. The type into which the component is being injected
     * @return object The component
     */
    public function get($name, Type $into = null)
    {
        if (!$this->contains($name)) {
            return ( $this->_parent !== null ) ?
                    $this->_parent->get($name, $into) : null;
        } else {
            return $this->_providers[$name]->get($this);
        }
    }

    /**
     * Gets the components in the contanier for the given type.
     * 
     * @param mixed $type A Xyster_Type or string class name
     * @return array Keys are component names, values are components themselves
     */
    public function getForType($type)
    {
        $type = $type instanceof Type ? $type : new Type($type);
        $components = array();
        foreach ($this->_providers as $name => $provider) {
            /* @var $provider \Xyster\Container\Provider\IProvider */
            if ($type->isAssignableFrom($provider->getType())) {
                $components[$name] = $provider->get($this);
            }
        }
        return $components;
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
        if ($type === null) {
            return array_keys($this->_providers);
        } else {
            $type = $type instanceof Type ? $type : new Type($type);
            $names = array();
            foreach ($this->_providers as $name => $provider) {
                /* @var $provider \Xyster\Container\Provider\IProvider */
                if ($type->isAssignableFrom($provider->getType())) {
                    $names[] = $provider->getName();
                }
            }
            return $names;
        }
    }

    /**
     * Gets the parent container.
     * 
     * @return IContainer
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Gets the type of component with the given name.
     * 
     * @param string $name The component name
     * @return Type The component type
     */
    public function getType($name)
    {
        return $this->contains($name) ?
                $this->_providers[$name]->getType() : null;
    }
}