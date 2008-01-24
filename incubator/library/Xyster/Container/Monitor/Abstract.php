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
 * @see Xyster_Container_Monitor
 */
require_once 'Xyster/Container/Monitor.php';
/**
 * @see Xyster_Container_Monitor_Strategy
 */
require_once 'Xyster/Container/Monitor/Strategy.php';
/**
 * A monitor which delegates to another monitor
 * 
 * It provides a Xyster_Container_Monitor_Null by default, but does not allow to
 * use <code>null</code> for the delegate.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Monitor_Abstract implements Xyster_Container_Monitor, Xyster_Container_Monitor_Strategy
{
    const INSTANTIATING = "Xyster_Container: instantiating %s";
    const INSTANTIATED = "Xyster_Container: instantiated %s [%0.5f ms] injected [%s]";
    const INSTANTIATION_FAILED = "Xyster_Container: instantiation failed: %s, reason: %s";
    const INVOKING = "Xyster_Container: invoking %s on %s";
    const INVOKED = "Xyster_Container: invoked %s on %s [%0.5f ms]";
    const INVOCATION_FAILED = "Xyster_Container: invocation failed: %s on %s, reason: %s";
    const NO_COMPONENT = "Xyster_Container: No component for key: %s";
    
    /**
	 * @var Xyster_Container_Monitor
	 */
	private $_delegate;

	/**
	 * Creates a new delegating monitor
	 *
	 * @param Xyster_Container_Monitor $delegate
	 */
	public function __construct( Xyster_Container_Monitor $delegate = null )
	{
		if ( $delegate === null ) {
			require_once 'Xyster/Container/Monitor/Null.php';
			$delegate = new Xyster_Container_Monitor_Null;
		}
		$this->_delegate = $delegate;
	}
	
    /**
     * Changes the component monitor used
     * 
     * @param Xyster_Container_Monitor $monitor the new monitor to use
     */
    public function changeMonitor( Xyster_Container_Monitor $monitor )
    {
        if ( $this->_delegate instanceof Xyster_Container_Monitor_Strategy ) {
            $this->_delegate->changeMonitor($monitor);
        } else {
            $this->_delegate = $monitor;
        }
    }

    /**
     * Gets the monitor currently used
     * 
     * @return Xyster_Container_Monitor The monitor currently used
     */
    public function currentMonitor()
    {
        return ( $this->_delegate instanceof Xyster_Container_Monitor_Strategy ) ?
           $this->_delegate->currentMonitor() : $this->_delegate;
    }
	
    /**
     * Event thrown as the component is being instantiated using the given constructor
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Type $class the class being instantiated
     */
    public function instantiating(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $class = null)
    {
    	$this->_delegate->instantiating($container, $adapter, $class);
    }

    /**
     * Event thrown after the component has been instantiated using the given constructor.
     * This should be called for both Constructor and Setter DI.
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Type $class the class being instantiated
     * @param mixed $instantiated the component that was instantiated
     * @param array $injected the components during instantiation
     * @param float $duration the duration in millis of the instantiation
     */
    public function instantiated(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $class = null, $instantiated, array $injected = null, $duration)
    {
    	$this->_delegate->instantiated($container, $adapter, $class, $instantiated, $injected, $duration);
    }

    /**
     * Event thrown if the component instantiation failed using the given constructor
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param Xyster_Type $class the class being instantiated
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    public function instantiationFailed(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, Xyster_Type $class, Exception $cause)
    {
    	$this->_delegate->instantiationFailed($container, $adapter, $class, $cause);
    }

    /**
     * Event thrown as the component method is being invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     */
    public function invoking(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionMethod $member, $instance)
    {
    	$this->_delegate->invoking($container, $adapter, $member, $instance);
    }

    /**
     * Event thrown after the component method has been invoked on the given instance
     * 
     * @param Xyster_Container_Interface $container
     * @param Xyster_Container_Adapter $adapter
     * @param ReflectionMethod $method the Method invoked on the component instance
     * @param mixed $instance the component instance
     * @param float $duration the duration in millis of the invocation
     */
    public function invoked(Xyster_Container_Interface $container, Xyster_Container_Adapter $adapter, ReflectionMethod $method, $instance, $duration)
    {
    	$this->_delegate->invoked($container, $adapter, $method, $instance, $duration);
    }

    /**
     * Event thrown if the component method invocation failed on the given instance
     * 
     * @param ReflectionMethod $member
     * @param mixed $instance the component instance
     * @param Exception $cause the Exception detailing the cause of the failure
     */
    public function invocationFailed(ReflectionMethod $member, $instance, Exception $cause)
    {
    	$this->_delegate->invocationFailed($member, $instance, $cause);
    }

    /**
     * 
     * @param Xyster_Container_Interface $container
     * @param mixed $key
     * @return mixed 
     */
    public function noComponentFound(Xyster_Container_Interface $container, $key)
    {
        return $this->_delegate->noComponentFound($container, $key);
    }
    
    /**
     * Converts an array of values into a string of their type names
     *
     * @param array $injected
     * @return string
     */
    public static function parmsToString( array $injected )
    {
    	require_once 'Xyster/Type.php';
    	$types = array();
    	foreach( $injected as $object ) {
    		$type = Xyster_Type::of($object);
    		$types[] = $type->getName();
    	}
    	return implode(', ', $types);
    }
}