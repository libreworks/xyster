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
namespace Xyster\Container\Injector;
use Xyster\Type\Proxy\IHandler;
/**
 * Provides instances of the component type wrapped in a runtime Proxy
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Proxy extends AbstractInjector
{
    /**
     *@var IHandler
     */
    private $_handler;
    /**
     * @var AbstractInjector
     */
    private $_delegate;

    /**
     * Creates a new Proxy Injector
     *
     * @param IHandler $handler The handler to be used by the proxy
     * @param AbstractInjector $injector The injector that will be delegated
     */
    public function __construct(IHandler $handler, AbstractInjector $injector)
    {
        parent::__construct($injector->getType(), $injector->getName());
        $this->_handler = $handler;
        $this->_delegate = $injector;
    }

    /**
     * Instantiate an object with given parameters
     *
     * @param \Xyster\Type\Type $type the class to construct
     * @param \Xyster\Container\IContainer the container
     * @param \Xyster\Type\Type $into The type into which this one is being injected
     * @return object the new object
     */
    protected function _newInstance(\Xyster\Type\Type $type, \Xyster\Container\IContainer $container, array $constructorArguments = array(), \Xyster\Type\Type $into = null)
    {
        $class = $type->getClass();
        $constructor = $class ? $class->getConstructor() : null;
        $arguments = $constructor ?
            InjectionHelper::getMemberArguments($container, $constructor, $constructorArguments, $into) :
            array();
        $builder = new \Xyster\Type\Proxy\Builder();
        return $builder->setCallParentConstructor()
            ->setParent($type)
            ->setHandler($this->_handler)
            ->create($arguments);
    }

    /**
     * Get an instance of the provided component.
     *
     * @param \Xyster\Container\IContainer $container The container (used for dependency resolution)
     * @param \Xyster\Type\Type $into Optional. The type into which this component will be injected
     * @return mixed The component
     */
    public function get(\Xyster\Container\IContainer $container, \Xyster\Type\Type $into = null)
    {
        $type = $this->getType();
        // instantiate a copy of the type
        $instance = $this->_newInstance($type, $container, $this->_constructorArguments, $into);
        // inject literal and referenced properties
        InjectionHelper::injectProperties($instance, $container, $this->_properties, $this->_dependsOn, $into);
        // inject container if necessary
        if ( $instance instanceof \Xyster\Container\IContainerAware ) {
            $instance->setContainer($container);
        }
        // call init method if necessary
        if ( $method = $this->_initMethod ) {
            $instance->$method();
        }
        return $instance;
    }


    /**
     * Gets the label for the type of provider (for instance Caching or Singleton).
     *
     * @return string The provider label
     */
    public function getLabel()
    {
        return 'Proxy:' . $this->_delegate->getLabel();
    }

    /**
     * Verify that all dependencies for this component can be satisifed.
     *
     * @param \Xyster\Container\IContainer $container The container
     * @throws \Xyster\Container\Exception if one or more required dependencies aren't met
     */
    public function validate(\Xyster\Container\IContainer $container)
    {
        return $this->_delegate->validate($container);
    }
}