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
     * @param array $parameterTypes an array of {@link Xyster_Type} objects
     * @return array
     */
    protected function _getMemberArguments( Xyster_Container_IContainer $container, ReflectionMethod $member = null )
    {
        if ( $member === null ) {
            return array();
        }
        $result = array();
        $types = Xyster_Type::getForParameters($member);
        foreach( $member->getParameters() as $k => $reflectionParameter ) {
            /* @var $reflectionParameter ReflectionParameter */
            $instance = null;
            // @todo some lookup thing
            $paramType = $types[$k];
            
            $names = $container->getNames($paramType);
            if ( count($names) > 1 && !in_array($reflectionParameter->getName(), $names) ) {
                require_once 'Xyster/Container/Injector/Exception.php';
                throw new Xyster_Container_Injector_Exception('');
            }
            if ( $instance === null && $reflectionParameter->isDefaultValueAvailable() ) {
                $instance = $reflectionParameter->getDefaultValue();
            }
            $result[] = $instance;
        }
        return $result;
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