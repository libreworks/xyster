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
 * @see Xyster_Container_Component_Adapter_Abstract
 */
require_once 'Xyster/Container/Component/Adapter/Abstract.php';
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
final class Xyster_Container_Component_Adapter_Instance extends Xyster_Container_Component_Adapter_Abstract implements Xyster_Container_Behavior
{
    /**
     * @var object
     */
    private $_componentInstance;
    
    /**
     * Creates a new adapter for a key and implementation
     *
     * @param mixed $componentKey
     * @param mixed $componentImplementation string class name or ReflectionClass
     * @param Xyster_Container_Component_Monitor $monitor
     */
    public function __construct( $componentKey, $componentInstance, Xyster_Container_Component_Monitor $monitor = null )
    {
        parent::__construct($componentKey, $this->_getInstanceClass($componentInstance), $monitor);
        $this->_componentInstance = $componentInstance;
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
        return $this->_componentInstance;
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
     * @param string $componentInstance
     * @return ReflectionClass
     */
    private function _getInstanceClass( $componentInstance )
    {
        return new ReflectionClass(get_class($componentInstance));        
    }
}