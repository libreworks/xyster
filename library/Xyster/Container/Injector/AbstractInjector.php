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
 * @see Xyster_Container_Provider_AbstractProvider
 */
require_once 'Xyster/Container/Provider/AbstractProvider.php';
/**
 * Provides instances of the component type
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Injector_AbstractInjector extends Xyster_Container_Provider_AbstractProvider
{
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
     * Checks to make sure the current implementation is a concrete class
     *
     * @throws Xyster_Container_Injection_Exception if the implementation isn't concrete
     */
    protected function _checkConcrete()
    {
        $class = $this->getType()->getClass();
        if ( $class instanceof ReflectionClass && ( $class->isInterface() || $class->isAbstract() ) ) {
            require_once 'Xyster/Container/Injector/Exception.php';
            throw new Xyster_Container_Injector_Exception($class->getName() . ' is not a concrete class');
        }
    }
    
    /**
     * Gets the member arguments
     *
     * @param Xyster_Container_IContainer $container
     * @param ReflectionMethod $member
     * @return array
     */
    protected function _getMemberArguments( Xyster_Container_IContainer $container, ReflectionMethod $member = null )
    {
        if ( $member === null || !$member->getNumberOfParameters() ) {
            return array();
        }
        $result = array();
        $types = Xyster_Type::getForParameters($member);
        $numOfArgs = count($this->_constructorArguments);
        if ( $numOfArgs != $member->getNumberOfRequiredParameters() ) {
            require_once 'Xyster/Container/Injector/Exception.php';
            throw new Xyster_Container_Injector_Exception('The number of required method parameters must equal the number of arguments provided');
        }
        foreach( $member->getParameters() as $k => $reflectionParameter ) {
            /* @var $reflectionParameter ReflectionParameter */
            $instance = null;
            $paramType = $types[$k];
            $argument = $this->_constructorArguments[$k];
            /* @var $paramType Xyster_Type */
            if ( $paramType->isInstance($argument) ||
                ($argument === null && $reflectionParameter->allowsNull()) ) {
                $instance = $argument;
            } else if ( $container->contains($argument) ) {
                $instance = $container->get($argument);
            } else {
                $names = $container->getNames($paramType);
                if ( count($names) == 1 ) {
                    $instance = $container->get($names[0]);
                } else if ( count($names) > 1 && in_array($reflectionParameter->getName(), $names) ) {
                    $instance = $container->get($reflectionParameter->getName());
                } else if ( $reflectionParameter->isDefaultValueAvailable() ) {
                    $instance = $reflectionParameter->getDefaultValue();
                } else if ( !count($names) ) {
                    require_once 'Xyster/Container/Injector/Exception.php';
                    throw new Xyster_Container_Injector_Exception(
                    	'Cannot inject method argument ' .
                        $reflectionParameter->getName() .
                        ' into ' . $member->getDeclaringClass()->getName() .
                        ' : key not found in the container: ' . $argument);
                } else if ( count($names) > 1 ) {
                    require_once 'Xyster/Container/Injector/Exception.php';
                    throw new Xyster_Container_Injector_Exception(
                    	'Cannot inject method argument ' .
                        $reflectionParameter->getName() .
                        ' into ' . $member->getDeclaringClass()->getName() .
                        ': more than one value is available in the container');
                }
            }
            $result[] = $instance;
        }
        return $result;
    }

    protected function _injectProperties(stdClass $instance, Xyster_Container_IContainer $container)
    {
        if ( !count($this->_properties) ) {
            return;
        }
        $class = $this->getType()->getClass();
        require_once 'Xyster/Type/Property/Factory.php';
        foreach( $this->_properties as $propertyName => $propertyValue ) {
            if ( $class->hasMethod('set' . ucfirst($propertyName)) ) {
                $method = $class->getMethod('set' . ucfirst($propertyName));
                /* @var $method ReflectionMethod */
                if ( $method->getNumberOfParameters() > 0 ) {
                    $types = Xyster_Type::getForParameters($method);
                    $type = $types[0];
                    /* @var $type Xyster_Type */
                    if ( !$type->isInstance($propertyValue) ) {
                        if ( $container->contains($propertyValue) ) {
                            $propertyValue = $container->get($propertyValue);
                        } else {
                            require_once 'Xyster/Container/Injector/Exception.php';
                            throw new Xyster_Container_Injector_Exception(
                            	'Cannot inject property' . $propertyName .
                                ' into ' . $member->getDeclaringClass()->getName() .
                                ' : key not found in the container: ' . $propertyValue);
                        }
                    }
                }
            }
            $prop = Xyster_Type_Property_Factory::get($instance, $propertyName);
            // @todo wrap this exception if it occurs?
            $prop->set($instance, $propertyValue);
        }
    }
    
    /**
     * Instantiate an object with given parameters and respect the accessible flag
     * 
     * @param Xyster_Type $type the class to construct
     * @param array $parameters the parameters for the constructor 
     * @return object the new object
     */
    protected function _newInstance(Xyster_Type $type, array $parameters = array())
    {
        $class = $type->getClass();
        if ( $type->getName() == 'array' ) {
            return array();
        } else {
            return ( $class->getConstructor() ) ?
                $class->newInstanceArgs($parameters) : $class->newInstance();
        }
    }
}