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
 * @see Xyster_Container_Injector_AbstractInjector
 */
require_once 'Xyster/Container/Injector/AbstractInjector.php';
/**
 * Provides instances of the component type
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injector_Standard extends Xyster_Container_Injector_AbstractInjector
{
    /**
     * Get an instance of the provided component.
     * 
     * @param Xyster_Container_IContainer $container The container (used for dependency resolution)
     * @param Xyster_Type $into Optional. The type into which this component will be injected
     * @return mixed The component
     */
    public function get(Xyster_Container_IContainer $container, Xyster_Type $into = null)
    {
        $type = $this->getType();
        // instantiate a copy of the type
        $instance = $this->_newInstance($type, $container);
        // inject literal and referenced properties
        $this->injectProperties($instance, $container);
        // inject container if necessary
        if ( $instance instanceof Xyster_Container_IContainerAware ) {
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
     * @param Xyster_Container_IContainer $container The container
     * @throws Xyster_Container_Exception if one or more required dependencies aren't met
     */
    public function validate(Xyster_Container_IContainer $container)
    {
        $reflectionClass = $this->getType()->getClass();
        $member = ($reflectionClass) ? $reflectionClass->getConstructor() : null;
        if ( $member != null ) {
            $types = Xyster_Type::getForParameters($member);
            foreach( $member->getParameters() as $k => $reflectionParameter ) {
                /* @var $reflectionParameter ReflectionParameter */
                $paramType = $types[$k];
                /* @var $paramType Xyster_Type */
                $argument = isset($this->_constructorArguments[$k]) ?
                    $this->_constructorArguments[$k] : null;
                if ( !$paramType->isInstance($argument) &&
                    !$container->contains($argument) &&
                    !$reflectionParameter->isOptional() ) {
                    require_once 'Xyster/Container/Injector/Exception.php';
                    throw new Xyster_Container_Injector_Exception(
                        'Component not found in the container: ' . $argument);
                }
            }
        }
        foreach( $this->_dependsOn as $k => $v ) {
            if ( !$container->contains($v) ) {
                require_once 'Xyster/Container/Injector/Exception.php';
                throw new Xyster_Container_Injector_Exception('Component not found in the container: ' . $v);
            }
        }
    }
}