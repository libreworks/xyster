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
 * @see Xyster_Container_Adapter
 */
require_once 'Xyster/Container/Adapter.php';
/**
 * @see Xyster_Container_Monitor_Strategy
 */
require_once 'Xyster/Container/Monitor/Strategy.php';
/**
 * Responsible for providing an instance of a specific type
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Adapter_Abstract implements Xyster_Container_Adapter, Xyster_Container_Monitor_Strategy
{
    private $_key;
    
    /**
     * @var Xyster_Type
     */
    private $_implementation;
    
    /**
     * @var Xyster_Container_Monitor
     */
    private $_monitor;
    
    /**
     * Creates a new adapter for a key and implementation
     *
     * @param mixed $key
     * @param mixed $implementation string class name or Xyster_Type
     * @param Xyster_Container_Monitor $monitor
     */
    public function __construct( $key, $implementation, Xyster_Container_Monitor $monitor = null )
    {
        if ( $key === null ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception('Key cannot be null'); 
        }
        $this->_key = $key;
        if ( $implementation instanceof Xyster_Type ) {
            $this->_implementation = $implementation;
        } else if ( $implementation !== null ) {
            require_once 'Xyster/Type.php';
            $this->_implementation = new Xyster_Type($implementation);
        }
        if ( $monitor === null ) {
            require_once 'Xyster/Container/Monitor/Null.php';
            $monitor = new Xyster_Container_Monitor_Null; 
        }
        $this->_monitor = $monitor;
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
        $this->_monitor = $monitor;
    }

    /**
     * Gets the monitor currently used
     * 
     * @return Xyster_Container_Monitor The monitor currently used
     */
    public function currentMonitor()
    {
        return $this->_monitor;
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
     * @return Xyster_Type the component's implementation class
     */
    public function getImplementation()
    {
        return $this->_implementation;
    }

    /**
     * Retrieve the key associated with the component
     *
     * Should either be a class type (normally an interface) or an identifier
     * that is unique (within the scope of the current Container).
     * 
     * @return mixed the component's key
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return "" . $this->getKey();
    }
    
    /**
     * If the key is a Xyster_Type, checks compatibility with implementation
     *
     */
    protected function _checkTypeCompatibility()
    {
        if ( $this->_key instanceof Xyster_Type ) {
            $componentType = $this->_key; /* @var $componentType Xyster_Type */
            $className = $this->_implementation->getName();
            if ( !$componentType->isAssignableFrom($this->_implementation) ) {
                require_once 'Xyster/Container/Exception.php';
                throw new Xyster_Container_Exception($className . ' is not a ' . 
                    $componentType->getName());
            }
        }
    }
}