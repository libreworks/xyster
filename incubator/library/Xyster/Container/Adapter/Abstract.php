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
 * @see Xyster_Container_Adapter
 */
require_once 'Xyster/Container/Adapter.php';
/**
 * Responsible for providing an instance of a specific type
 * 
 * An instance of this interface will be used inside a container for every
 * component that is registered.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Adapter_Abstract implements Xyster_Container_Adapter, Xyster_Container_Monitor_Strategy
{
    private $_componentKey;
    /**
     * @var ReflectionClass
     */
    private $_componentImplementation;
    /**
     * @var Xyster_Container_Monitor
     */
    private $_componentMonitor;
    
    /**
     * Creates a new adapter for a key and implementation
     *
     * @param mixed $componentKey
     * @param mixed $componentImplementation string class name or ReflectionClass
     * @param Xyster_Container_Monitor $monitor
     */
    public function __construct( $componentKey, $componentImplementation, Xyster_Container_Monitor $monitor = null )
    {
        if ( $componentKey === null ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Key cannot be null'); 
        }
        $this->_componentKey = $componentKey;
        $this->_componentImplementation = $componentImplementation instanceof ReflectionClass ?
            $componentImplementation : new ReflectionClass($componentImplementation);
        if ( $monitor != null ) {
            $this->_componentMonitor = $monitor; 
        }
        $this->_checkTypeCompatibility();
    }
    
    /**
     * Accepts a visitor for this Adapter
     * 
     * {@inherit}
     *
     * @param Xyster_Container_Visitor $visitor the visitor.
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
        $visitor->visitComponentAdapter($this);
    }
    
    /**
     * Changes the component monitor used
     * 
     * @param Xyster_Container_Monitor $monitor the new monitor to use
     */
    public function changeMonitor( Xyster_Container_Monitor $monitor )
    {
        $this->_componentMonitor = $monitor;
    }

    /**
     * Gets the monitor currently used
     * 
     * @return Xyster_Container_Monitor The monitor currently used
     */
    public function currentMonitor()
    {
        return $this->_componentMonitor;
    }
    
    /**
     * @return Xyster_Container_Adapter
     */
    public function getDelegate()
    {
        return null;
    }

    /**
     * Retrieve the class of the component
     *
     * @return ReflectionClass the component's implementation class
     */
    public function getImplementation()
    {
        return $this->_componentImplementation;
    }

    /**
     * Retrieve the key associated with the component
     *
     * Should either be a class type (normally an interface) or an identifier
     * that is unique (within the scope of the current Container).
     * 
     * @return mixed the component's key
     */
    function getKey()
    {
        return $this->_componentKey;
    }
    
    /**
     * Returns the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getKey();
    }
    
    /**
     * If the key is a ReflectionClass, checks compatibility with implementation
     *
     */
    protected function _checkTypeCompatibility()
    {
        if ( $this->_componentKey instanceof ReflectionClass ) {
            $componentType = $this->_componentKey; /* @var $componentType ReflectionClass */
            if ( $componentType->getName() != $this->_componentImplementation->getName() &&
                $componentType->isSubclassOf($this->_componentImplementation->getName()) ) {
                require_once 'Xyster/Container/Exception.php';
                throw new Xyster_Container_Exception($this->_componentImplementation->getName() . ' is not a ' . 
                    $componentType->getName());
            }
        }
    }
}