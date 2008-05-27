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
 * @see Xyster_Container_Injection_Iterative
 */
require_once 'Xyster/Container/Injection/Iterative.php';
/**
 * Instantiates components using empty constructors and setter injection
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Setter extends Xyster_Container_Injection_Iterative
{
    protected $_methodPrefix;

    /**
     * Creates a new setter injector
     *
     * @param mixed $key the search key for this implementation 
     * @param object $implementation the concrete implementation
     * @param array $parameters the parameters to use for the initialization
     * @param Xyster_Container_Monitor $monitor the component monitor used
     * @param string $setterMethodPrefix the prefix of the setter method
     * @param boolean $useNames use argument names when looking up dependencies
     */
    public function __construct( $key, $implementation, array $parameters = null, Xyster_Container_Monitor $monitor = null, $setterMethodPrefix = 'set', $useNames = false)
    {
        parent::__construct($key, $implementation, $parameters, $monitor, $useNames);
        $this->_methodPrefix = $setterMethodPrefix;
    }
    
    /**
     * Gets the descriptor of this adapter
     *
     * @return string
     */
    public function getDescriptor()
    {
        return 'SetterInjector-';
    }
    
    /**
     * Gets the setter method prefix
     *
     * @return string
     */
    protected function _getInjectorPrefix()
    {
        return $this->_methodPrefix;
    }
    
    /**
     * Injects a value into a member
     *
     * @param ReflectionMethod $member
     * @param object $instance
     * @param mixed $toInject
     */
    protected function _injectIntoMember( ReflectionMethod $member, $instance, $toInject )
    {
        $member->invoke($instance, $toInject);
    }
        
    /**
     * {@inherit}
     *
     * @param ReflectionMethod $method
     * @return boolean
     */
    protected function _isInjectorMethod( ReflectionMethod $method )
    {
        $methodName = $method->getName();
        $prefixLen = strlen($this->_getInjectorPrefix());
        return strlen($methodName) >= $prefixLen + 1 && 
            substr($methodName, 0, $prefixLen) == $this->_getInjectorPrefix() &&
            strtoupper($methodName[$prefixLen]) == $methodName[$prefixLen];
    }
}