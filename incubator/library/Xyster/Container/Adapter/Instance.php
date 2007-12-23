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
 * @see Xyster_Container_Adapter_Abstract
 */
require_once 'Xyster/Container/Adapter/Abstract.php';
/**
 * @see Xyster_Container_Behavior
 */
require_once 'Xyster/Container/Behavior.php';
/**
 * Component adapter which wraps a component instance
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
final class Xyster_Container_Adapter_Instance extends Xyster_Container_Adapter_Abstract implements Xyster_Container_Behavior
{
    /**
     * @var object
     */
    private $_instance;
    
    /**
     * Creates a new adapter for a key and implementation
     *
     * @param mixed $key
     * @param mixed $instance The instance of the component
     * @param Xyster_Container_Monitor $monitor
     */
    public function __construct( $key, $instance, Xyster_Container_Monitor $monitor = null )
    {
        parent::__construct($key, $this->_getInstanceClass($instance), $monitor);
        $this->_instance = $instance;
    }
    
    /**
     * Retrieve the component instance
     * 
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */
    function getInstance(Xyster_Container_Interface $container)
    {
        return $this->_instance;
    }

    /**
     * Verify that all dependencies for this adapter can be satisifed
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @throws Exception if one or more dependencies cannot be resolved
     */
    public function verify(Xyster_Container_Interface $container)
    {
    }
    
    /**
     * Returns the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return "Instance-" . parent::__toString();
    }
    
    /**
     * Gets the class of an instance
     *
     * @param string $instance
     * @return ReflectionClass
     */
    private function _getInstanceClass( $instance )
    {
        return is_object($instance) ? new ReflectionClass(get_class($instance)) : null;        
    }
}