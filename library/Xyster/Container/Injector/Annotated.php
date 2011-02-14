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
/**
 * Uses setter injection 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Standard extends AbstractInjector
{
    protected $_initMethod;
    protected $_constructorArguments = array();
    protected $_properties = array();
    protected $_dependsOn = array();

    /**
     * Creates a new standard injector
     *
     * @param \Xyster\Container\Definition $def The component definintion
     */
    public function __construct(\Xyster\Container\Definition $def)
    {
        parent::__construct($def->getType(), $def->getName());
        $this->_initMethod = $def->getInitMethod();
        $this->_constructorArguments = $def->getConstructorArgs();
        $this->_properties = $def->getProperties();
        $this->_dependsOn = $def->getDependsOn();
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
     * Gets the label for the type of provider.
     *
     * @return string The provider label
     */
    public function getLabel()
    {
        return 'Injector';
    }

    /**
     * Verify that all dependencies for this component can be satisifed.
     *
     * @param \Xyster\Container\IContainer $container The container
     * @throws \Xyster\Container\Exception if one or more required dependencies aren't met
     */
    public function validate(\Xyster\Container\IContainer $container)
    {
        $reflectionClass = $this->getType()->getClass();
        $member = ($reflectionClass) ? $reflectionClass->getConstructor() : null;
        /* @var $member \ReflectionMethod */
        if ( $member != null ) {
            $types = \Xyster\Type\Type::getForParameters($member);
            foreach( $member->getParameters() as $k => $reflectionParameter ) {
                /* @var $reflectionParameter \ReflectionParameter */
                $paramType = $types[$k];
                /* @var $paramType \Xyster\Type\Type */
                $argument = isset($this->_constructorArguments[$k]) ?
                    $this->_constructorArguments[$k] : null;
                if ( !$paramType->isInstance($argument) &&
                    !$container->contains($argument) &&
                    !$reflectionParameter->isOptional() ) {
                    throw new Exception('Component not found in the container: ' . $argument);
                }
            }
        }
        foreach( $this->_dependsOn as $k => $v ) {
            if ( !$container->contains($v) ) {
                throw new Exception('Component not found in the container: ' . $v);
            }
        }
    }
}