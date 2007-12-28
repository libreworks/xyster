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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injection_Abstract
 */
require_once 'Xyster/Container/Injection/Abstract.php';
/**
 * This adapter will instantiate a new object for each call to getInstance 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Injection_SingleMember extends Xyster_Container_Injection_Abstract
{
    /**
     * Gets the member arguments
     *
     * @param Xyster_Container_Interface $container
     * @param ReflectionMethod $member
     * @param array $parameterTypes an array of {@link Xyster_Type} objects
     */
    protected function _getMemberArguments( Xyster_Container_Interface $container, ReflectionMethod $member = null, array $parameterTypes = null )
    {
        if ( $member === null ) {
            return array();
        }
        
        $reflectionParams = $member->getParameters();
        
        if ( $parameterTypes === null ) {
            $parameterTypes = array();
            foreach( $reflectionParams as $param ) {
                $parameterTypes[] = $param->getClass();
            }
        }
        
        $result = array();
        $currentParameters = $this->_parameters !== null ? $this->_parameters :
            $this->_createDefaultParameters($parameterTypes);
        
        foreach( $currentParameters as $k => $parameter ) {
            /* @var $parameter Xyster_Container_Parameter */
            $result[] = $parameter->resolveInstance($container, $this,
                $reflectionParams[$k]);
        }
        
        return $result;
    }
    
    /**
     * Looks up the parameter names
     *
     * @param ReflectionMethod $method
     * @return string
     */
    protected function _lookupParameterNames( ReflectionMethod $method )
    {
        $names = array();
        foreach( $method->getParameters() as $parameter ) {
            /* @var $parameter ReflectionParameter */
            $names[] = $parameter->getName();
        }
        return $names;
    }
}