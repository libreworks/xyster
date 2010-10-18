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
 * Provides instances of the component type
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractInjector extends \Xyster\Container\Provider\AbstractProvider
{
    /**
     * Creates a new provider
     * 
     * @param \Xyster\Container\Definition $def The component definintion
     */
    public function __construct(\Xyster\Container\Definition $def)
    {
        parent::__construct($def);
        $this->_checkConcrete();
    }
    
    /**
     * Checks to make sure the current implementation is a concrete class
     *
     * @throws Exception if the implementation isn't concrete
     */
    protected function _checkConcrete()
    {
        $class = $this->getType()->getClass();
        if ( $class instanceof \ReflectionClass && ( $class->isInterface() || $class->isAbstract() ) ) {
            throw new Exception($class->getName() . ' is not a concrete class');
        }
    }
    
    /**
     * Gets the member arguments
     *
     * @param \Xyster\Container\IContainer $container
     * @param \ReflectionMethod $member
     * @return array
     */
    public function getMemberArguments( \Xyster\Container\IContainer $container, \ReflectionMethod $member = null )
    {
        if ( $member === null || !$member->getNumberOfParameters() ) {
            return array();
        }
        $result = array();
        $types = \Xyster\Type\Type::getForParameters($member);
        $numOfArgs = count($this->_constructorArguments);
        if ( $numOfArgs < $member->getNumberOfRequiredParameters() ) {
            throw new Exception('The number of required method parameters must equal the number of arguments provided');
        }
        foreach( $member->getParameters() as $k => $reflectionParameter ) {
            /* @var $reflectionParameter \ReflectionParameter */
            $instance = null;
            $paramType = $types[$k];
            /* @var $paramType \Xyster\Type\Type */
            $argument = isset($this->_constructorArguments[$k]) ?
                $this->_constructorArguments[$k] : null;
            if ( $paramType->isInstance($argument) ) {
                $instance = $argument;
            } else if ( $container->contains($argument) ) {
                $instance = $container->get($argument);
            } else if ( $reflectionParameter->isOptional() ) {
                $instance = $reflectionParameter->getDefaultValue();
            } else {
                throw new Exception('Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ': key not found in the container: ' . $argument);
            }
            $result[] = $instance;
        }
        return $result;
    }

    /**
     * Injects properties into an instance
     * 
     * @param object $instance
     * @param \Xyster\Container\IContainer $container
     */
    public function injectProperties($instance, \Xyster\Container\IContainer $container)
    {
        foreach( $this->_properties as $name => $propertyValue ) {
            $prop = \Xyster\Type\Property\Factory::get($instance, $name);
            // @todo wrap this exception if it occurs?
            $prop->set($instance, $propertyValue);
        }
        foreach( $this->_dependsOn as $name => $component ) {
            $this->_injectByNameFromContainer($container, $instance, $name, $component);
        }
    }
    
    /**
     * Injects a component from the container into the instance by name.
     *  
     * @param $container The container
     * @param $instance The object instance
     * @param $name The property name on the object
     * @param $component The name of the component in the container
     */
    protected function _injectByNameFromContainer(\Xyster\Container\IContainer $container, $instance, $name, $component)
    {
        if ( $container->contains($component) ) {
            $propertyValue = $container->get($component);
            $prop = \Xyster\Type\Property\Factory::get($instance, $name);
            // @todo wrap this exception if it occurs?
            $prop->set($instance, $propertyValue);
        } else {
            require_once 'Xyster/Container/Injector/Exception.php';
            throw new Exception(
                'Cannot inject property ' . $name . ' into ' .
                get_class($instance) .
                ': key not found in the container: ' . $component);
        }
    }
    
    /**
     * Instantiate an object with given parameters
     * 
     * @param \Xyster\Type\Type $type the class to construct
     * @param \Xyster\Container\IContainer the container
     * @return object the new object
     */
    protected function _newInstance(\Xyster\Type\Type $type, \Xyster\Container\IContainer $container)
    {
        $class = $type->getClass();
        $constructor = $class ? $class->getConstructor() : null;
        $parameters = $this->getMemberArguments($container, $constructor);
        return $constructor ?
            $class->newInstanceArgs($parameters) : $class->newInstance();
    }
}