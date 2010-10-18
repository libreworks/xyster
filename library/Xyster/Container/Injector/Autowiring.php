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
use Xyster\Container\Autowire;
/**
 * Autowires dependencies of a component as it creates it
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Autowiring extends Standard
{
    /**
     * @var \Xyster\Container\Autowire
     */
    protected $_autowire;
    protected $_ignore = array();
    
    /**
     * Creates a new provider
     * 
     * @param \Xyster\Container\Definition $def The component definintion
     * @param \Xyster\Container\Autowire $autowire Optional. The autowire mode.
     * @param array $ignore The autowire candidates (names or types) to ignore.
     */
    public function __construct(\Xyster\Container\Definition $def, Autowire $autowire = null, array $ignore = array())
    {
        parent::__construct($def);
        $this->_autowire = $autowire === null ? Autowire::None() : $autowire;
        $this->_ignore = $ignore;
    }
    
    /**
     * Gets the label for the type of provider.
     * 
     * @return string The provider label
     */
    public function getLabel()
    {
        return 'Autowiring';
    }
    
    /**
     * Gets the member arguments.
     * 
     * This method handles Constructor autowiring.  It will look in the
     * container for a component matching the type, if more than one is found,
     * it will try to use the parameter name to select the correct one.   If no
     * component is found and the method parameter has a default value available
     * it will be used.  If all else fails, an exception is thrown.
     *
     * @param \Xyster\Container\IContainer $container
     * @param \ReflectionMethod $member
     * @return array
     * @throws Xyster_Container_Injector_Exception if a parameter could not be autowired
     */
    public function getMemberArguments( \Xyster\Container\IContainer $container, \ReflectionMethod $member = null )
    {
        if ( $member === null || !$member->getNumberOfParameters()
            || $this->_autowire !== Autowire::Constructor() ) {
            return array();
        }
        $result = array();
        $types = Type::getForParameters($member);
        foreach( $member->getParameters() as $k => $reflectionParameter ) {
            /* @var $reflectionParameter \ReflectionParameter */
            $instance = null;
            $paramType = $types[$k];
            /* @var $paramType \Xyster\Type\Type */
            if ( !$paramType->isObject() && !$reflectionParameter->isOptional() ) {
                throw new Exception('Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ': non-object parameters cannot be autowired');
            }
            $names = $container->getNames($paramType);
            if ( count($names) == 1 ) {
                $instance = $container->get($names[0]);
            } else if ( count($names) > 1 && in_array($reflectionParameter->getName(), $names) ) {
                $instance = $container->get($reflectionParameter->getName());
            } else if ( $reflectionParameter->isDefaultValueAvailable() ) {
                $instance = $reflectionParameter->getDefaultValue();
            } else if ( !count($names) ) {
                throw new Exception('Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ': no matching types were found in the container');
            } else if ( count($names) > 1 ) {
                throw new Exception('Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ': more than one value is available in the container');
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
        if ( $this->_autowire === Autowire::ByType() ) {
            foreach( $this->_type->getClass()->getMethods() as $k => $method ) {
                /* @var $method \ReflectionMethod */
                if ( $method->getNumberOfParameters() == 1 &&
                    substr($method->getName(), 0, 3) == 'set' ) {
                    $types = Type::getForParameters($method);
                    $propertyType = $types[0];
                    /* @var $propertyType \Xyster\Type\Type */
                    $name = strtolower(substr($method->getName(),3,1)) . substr($method->getName(),4);
                    if ( in_array($name, $this->_ignore) || !$propertyType->isObject() ) {
                        continue;
                    }
                    if ( $container->containsType($propertyType) ) {
                        $propertyValues = $container->getForType($propertyType);
                        if ( count($propertyValues) == 1 ) {
                            // @todo wrap this exception if it occurs?
                            \Xyster\Type\Property\Factory::get($instance, $name)
                                ->set($instance, array_pop($propertyValues));
                        } else {
                            throw new Exception('Cannot inject property ' . $name .
                                ' into ' . $method->getDeclaringClass()->getName() .
                                ': more than one value is available in the container');
                        }
                    } else {
                        throw new Exception(
                            'Cannot inject property ' . $name . ' into ' .
                            $method->getDeclaringClass()->getName() .
                            ': type not found in the container: ' . $propertyType);
                    }
                }
            }
        } else if ( $this->_autowire === Autowire::ByName() ) {
            foreach( $this->_type->getClass()->getMethods() as $k => $method ) {
                /* @var $method \ReflectionMethod */
                if ( $method->getNumberOfParameters() == 1 &&
                    substr($method->getName(), 0, 3) == 'set' ) {
                    $name = strtolower(substr($method->getName(),3,1)) . substr($method->getName(),4);
                    $types = Type::getForParameters($method);
                    $propertyType = $types[0];
                    /* @var $propertyType \Xyster\Type\Type */
                    if ( in_array($name, $this->_ignore) || !$propertyType->isObject() ) {
                        continue;
                    }
                    $this->_injectByNameFromContainer($container, $instance, $name, $name);
                }
            }
        }
    }
}