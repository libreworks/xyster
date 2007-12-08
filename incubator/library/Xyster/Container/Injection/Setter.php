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
 * @see Xyster_Container_Injection_Iterative
 */
require_once 'Xyster/Container/Injection/Iterative.php';
/**
 * Instantiates components using empty constructors and setter injection
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Setter extends Xyster_Container_Injection_Iterative
{
    protected $_methodPrefix;

    /**
     * Creates a new setter injector
     *
     * @param mixed $componentKey
     * @param object $componentImplementation
     * @param array $parameters
     * @param Xyster_Container_Monitor $monitor
     * @param string $setterMethodPrefix
     */
    public function __construct( $componentKey, $componentImplementation, array $parameters, Xyster_Container_Monitor $monitor, $setterMethodPrefix = 'set')
    {
        parent::__construct($componentKey, $componentImplementation, $parameters, $monitor);
        $this->_methodPrefix = $setterMethodPrefix;
    }
    
    /**
     * Gets the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return "SetterInjector-" . parent::__toString();
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