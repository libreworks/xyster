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
 * @see Xyster_Type_Property_Factory
 */
require_once 'Xyster/Type/Property/Factory.php';
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
     * Creates a new provider
     * 
     * @param Xyster_Container_Definition $def The component definintion
     */
    public function __construct(Xyster_Container_Definition $def)
    {
        parent::__construct($def);
        $this->_checkConcrete();
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
            /* @var $paramType Xyster_Type */
            $argument = $this->_constructorArguments[$k];
            if ( $paramType->isInstance($argument) ) {
                $instance = $argument;
            } else if ( $container->contains($argument) ) {
                $instance = $container->get($argument);
            } else if ( $reflectionParameter->allowsNull() ) {
                $instance = null;
            } else {
                require_once 'Xyster/Container/Injector/Exception.php';
                throw new Xyster_Container_Injector_Exception(
                    'Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ' : key not found in the container: ' . $argument);
            }
            $result[] = $instance;
        }
        return $result;
    }

    /**
     * Injects properties into an instance
     * 
     * @param stdClass $instance
     * @param Xyster_Container_IContainer $container
     */
    protected function _injectProperties(stdClass $instance, Xyster_Container_IContainer $container)
    {
        foreach( $this->_properties as $name => $propertyValue ) {
            $prop = Xyster_Type_Property_Factory::get($instance, $name);
            // @todo wrap this exception if it occurs?
            $prop->set($instance, $propertyValue);
        }
        $class = $this->getType()->getClass();
        foreach( $this->_dependsOn as $name => $component ) {
            if ( $container->contains($component) ) {
                $propertyValue = $container->get($component);
                $prop = Xyster_Type_Property_Factory::get($instance, $propertyName);
                // @todo wrap this exception if it occurs?
                $prop->set($instance, $propertyValue);                    
            } else {
                require_once 'Xyster/Container/Injector/Exception.php';
                throw new Xyster_Container_Injector_Exception(
                    'Cannot inject property' . $name . ' into ' .
                    $class->getName() .
                    ' : key not found in the container: ' . $component);
            }
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
        return ( $class->getConstructor() ) ?
            $class->newInstanceArgs($parameters) : $class->newInstance();
    }
}