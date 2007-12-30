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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injection_Abstract
 */
require_once 'Xyster/Container/Injection/Abstract.php';
/**
 * This injector goes over a list of items
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Injection_Iterative extends Xyster_Container_Injection_Abstract
{
    /**
     * @var Xyster_Collection_List
     */
    protected $_injectionMembers;
    
    /**
     * @var array
     */
    protected $_injectionParameters = array();
    
    /**
     * Retrieve the component instance
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */
    public function getInstance( Xyster_Container_Interface $container )
    {
        $matchingParameters = $this->_getMatchingParameterListForSetters($container);
        
        $type = $this->getImplementation();
        $class = $type->getClass();
        $constructor = $class->getConstructor();
        $monitor = $this->currentMonitor();
        $componentInstance = $this->_getOrMakeInstance($container, $type, $monitor);
        $member = null;
        $injected = array();
        
        try {
            for( $i=0; $i<count($this->_injectionMembers); $i++ ) {
                $member = $this->_injectionMembers->get($i);
                $monitor->invoking($container, $this, $member, $componentInstance);
                $matchingParam = $matchingParameters[$i];
                /* @var $matchingParam Xyster_Container_Parameter */
                if ( $matchingParam === null ) {
                    continue;
                }
                $toInject = $matchingParam->resolveInstance($container, $this, $this->_injectionParameters[$i]);
                
                $this->_injectIntoMember($member, $componentInstance, $toInject);
                $injected[] = $toInject;
            }
            return $componentInstance;
        } catch ( Exception $e ) {
            $this->_caughtInvocationTargetException($monitor, $member, $componentInstance, $e);
        }
    }

    /**
     * Verify that all dependencies for this adapter can be satisifed
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @throws Exception if one or more dependencies cannot be resolved
     */
    public function verify( Xyster_Container_Interface $container )
    {
        $currentParameters = $this->_getMatchingParameterListForSetters($container);
        foreach( $currentParameters as $i => $param ) {
            /* @var $param Xyster_Container_Parameter */
            $param->verify($container, $this, $this->_injectionParameters[$i]);
        }
    }
    
    /**
     * Gets the parameter list
     * 
     * @return array an array of Xyster_Container_Parameter objects 
     */
    protected function _getMatchingParameterListForSetters( Xyster_Container_Interface $container )
    {
        if ( $this->_injectionMembers === null ) {
            $this->_initializeInjectionMembersAndParamLists();
        }
        
        require_once 'Xyster/Collection/List.php';
        $matchingParameterList = new Xyster_Collection_List;
        $nonMatchingParameterPositions = array();
        $currentParameters = $this->_parameters != null ? $this->_parameters :
            $this->_createDefaultParameters($this->_injectionParameters);
            
        foreach( $currentParameters as $k => $parameter ) {
            /* @var $parameter Xyster_Container_Parameter */
            $failedDependency = true;
            for( $i=0; $i<count($this->_injectionParameters); $i++ ) {
                if ( $parameter->isResolvable($container, $this, $this->_injectionParameters[$i]) ) {
                    $matchingParameterList->set($k, $parameter);
                    $failedDependency = false;
                    break;
                }
            }
            if ( $failedDependency ) {
                $nonMatchingParameterPositions[] = $k;
            }
        }
        
        $unsatisfiableDependencyTypes = array();
        foreach( $matchingParameterList as $i => $param ) {
            if ( $param === null ) {
                $unsatisfiableDependencyTypes[] = $this->_injectionParameters[$i];
            }
        }
        if ( count($unsatisfiableDependencyTypes) > 0 ) {
            throw new Xyster_Container_Exception('Unsatisfiable dependencies: ' . implode(',', $unsatisfiableDependencyTypes));
        } else if ( count($nonMatchingParameterPositions) > 0 ) {
            throw new Xyster_Container_Exception('Unmatched parameter positions: ' . implode(',', $nonMatchingParameterPositions));
        }
        
        return $matchingParameterList->toArray();
    }
    
    /**
     * Gets an instance of the object
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Type $class
     * @param Xyster_Container_Monitor $monitor
     * @return object
     */
    protected function _getOrMakeInstance( Xyster_Container_Interface $container, Xyster_Type $class, Xyster_Container_Monitor $monitor )
    {
        $startTime = microtime(true);
        $monitor->instantiating($container, $this, $class);
        
        $componentInstance = null;
        try {
            $componentInstance = $this->_newInstance($class);
        } catch ( Exception $thrown ) {
            $this->_caughtInstantiationException($monitor, $class, $thrown, $container);
        }
        
        $monitor->instantiated($container, $this, $class, $componentInstance,
            null, microtime(true) - $startTime);
        return $componentInstance;
    }
    
    /**
     * Traverses the type and caches all injection methods and their parameters 
     *
     */
    protected function _initializeInjectionMembersAndParamLists()
    {
        require_once 'Xyster/Collection/List.php';
        $this->_injectionMembers = new Xyster_Collection_List;
        
        $parameters = array();
        foreach( $this->getImplementation()->getClass()->getMethods() as $method ) {
            /* @var $method ReflectionMethod */
            if ( $method->getNumberOfParameters() == 1 ) {
                if ( $this->_isInjectorMethod($method) ) {
                    $this->_injectionMembers->add($method);
                    $parameters[] = current($method->getParameters());
                }
            }
        }
        $this->_injectionParameters = $parameters;
    }

    /**
     * Injects a value into a member
     *
     * @param ReflectionMethod $member
     * @param object $componentInstance
     * @param mixed $toInject
     */
    protected function _injectIntoMember( ReflectionMethod $member, $componentInstance, $toInject )
    {
        $member->invoke($componentInstance, $toInject);
    }
    
    /**
     * Gets whether the method passed in should be used for injection
     *
     * @param ReflectionMethod $method
     * @return boolean
     */
    protected function _isInjectorMethod( ReflectionMethod $method )
    {
        return false;
    }
}