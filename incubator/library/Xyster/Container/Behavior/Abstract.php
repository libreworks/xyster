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
 * @see Xyster_Container_Behavior
 */
require_once 'Xyster/Container/Behavior.php';
/**
 * @see Xyster_Container_Monitor_Strategy
 */
require_once 'Xyster/Container/Monitor/Strategy.php';
/**
 * Component adapter which decorates another adapter
 *
 * This adapter supports a monitor strategoy and will propagate change of
 * monitor to the delegate if the delegate iteslf supports the monitor strategy.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Behavior_Abstract implements Xyster_Container_Behavior, Xyster_Container_Monitor_Strategy
{
    /**
     * @var Xyster_Container_Adapter
     */
    protected $_delegate;
    
    /**
     * Creates a new behavior.
     *
     * @param Xyster_Container_Adapter $delegate
     */
    public function __construct( Xyster_Container_Adapter $delegate )
    {
        $this->_delegate = $delegate;
    }
    
    /**
     * Accepts a visitor for this Adapter
     * 
     * {@inherit}
     *
     * @param Xyster_Container_Visitor $visitor the visitor.
     */
    public function accept( Xyster_Container_Visitor $visitor )
    {
        $visitor->visitComponentAdapter($this);
        $this->_delegate->accept($visitor);
    }
    
    /**
     * Delegates change of monitor if the delegate supports a component monitor strategy
     * {@inherit}
     */
    public function changeMonitor( Xyster_Container_Monitor $monitor )
    {
        if ( $this->_delegate instanceof Xyster_Container_Monitor_Strategy ) {
            $this->_delegate->changeMonitor($monitor);
        }
    }

    /**
     * Returns delegate's current monitor if the delegate supports 
     * a component monitor strategy.
     * {@inheritDoc}
     * @return Xyster_Container_Monitor
     * @throws Exception if no component monitor is found in delegate
     */
    public function currentMonitor()
    {
        if ( $this->_delegate instanceof Xyster_Container_Monitor_Strategy ) {
            return $this->_delegate->currentMonitor();
        }
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception('No component monitor found in delegate');
    }
    
    /**
     * @return Xyster_Container_Adapter
     */
    public function getDelegate()
    {
        return $this->_delegate;
    }
    
    /**
     * Retrieve the class of the component
     * 
     * {@inherit}
     *
     * @return Xyster_Type the component's implementation class
     */
    public function getImplementation()
    {
        return $this->_delegate->getImplementation();
    }
    
    /**
     * Retrieve the component instance
     * 
     * {@inherit}
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */
    public function getInstance( Xyster_Container_Interface $container )
    {
        return $this->_delegate->getInstance($container);
    }
    
    /**
     * Retrieve the key associated with the component
     *
     * {@inherit}
     * 
     * @return mixed the component's key
     */
    public function getKey()
    {
        return $this->_delegate->getKey();
    }
    
    /**
     * Verify that all dependencies for this adapter can be satisifed
     * 
     * {@inherit}
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @throws Exception if one or more dependencies cannot be resolved
     */
    public function verify( Xyster_Container_Interface $container )
    {
        return $this->_delegate->verify($container);
    }
    
    /**
     * Returns the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }
}