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
use Xyster\Type\Type;
/**
 * Provides a little assistance in injection and instantiation.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class InjectionHelper
{
    /**
     * @var Xyster\Type\Type
     */
    private $_type;

    /**
     * Creates a new InjectionHelper.
     *
     * @param Type $type The object type
     */
    public function __construct(Type $type)
    {
        $this->_type = $type;
    }

    /**
     * Finds a component in the container.
     *
     * @param \Xyster\Container\IContainer $container
     * @param string $component The name of the component to locate
     * @param Type $into the type into which the component will be injected
     * @return mixed The found component
     * @throws Exception if the component isn't in the container
     */
    public static function findInContainer(\Xyster\Container\IContainer $container, $component, Type $into = null)
    {
        if ( $container->contains($component) ) {
            return $container->get($component, $into);
        } else {
            throw new Exception('Key not found in the container: ' . $component);
        }
    }

    /**
     * Gets the member arguments
     *
     * @param \Xyster\Container\IContainer $container
     * @param array $constructorArguments Array of values or component names
     * @param Type $into The type into which this one is being injected
     * @return array
     */
    public function getMemberArguments(\Xyster\Container\IContainer $container, array $constructorArguments = array(), Type $into = null)
    {
        $class = $this->_type->getClass();
        $member = $class ? $class->getConstructor() : null;
        if ( $member === null || !$member->getNumberOfParameters() ) {
            return array();
        }
        $result = array();
        $types = Type::getForParameters($member);
        $numOfArgs = count($constructorArguments);
        if ( $numOfArgs < $member->getNumberOfRequiredParameters() ) {
            throw new Exception('The number of required method parameters must equal the number of arguments provided');
        }
        foreach( $member->getParameters() as $k => $reflectionParameter ) {
            /* @var $reflectionParameter \ReflectionParameter */
            $instance = null;
            $paramType = $types[$k];
            /* @var $paramType Type */
            $argument = isset($constructorArguments[$k]) ?
                $constructorArguments[$k] : null;
            if ( $paramType->isInstance($argument) ) {
                $instance = $argument;
            } else if ( $container->contains($argument) ) {
                $instance = $container->get($argument, $this->_type);
            } else if ( $reflectionParameter->isOptional() ) {
                $instance = $reflectionParameter->getDefaultValue();
            } else {
                throw new Exception('Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $this->_type->getName() .
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
     * @param array $properties Key is property name, value is the literal value
     * @param array $dependsOn Key is property name, value is component name in container
     * @param Type $into The type into which the instance is being injected
     */
    public function injectProperties($instance, \Xyster\Container\IContainer $container, array $properties, array $dependsOn, Type $into = null)
    {
        $values = array() + $properties;
        foreach( $dependsOn as $name => $component ) {
            $values[$name] = self::findInContainer($container, $component, $this->_type);
        }
        $binder = new \Xyster\Type\Binder($instance);
        $binder->bind($values);
    }
}