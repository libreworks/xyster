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
 * @see Xyster_Container_Monitor_Abstract
 */
require_once 'Xyster/Container/Monitor/Abstract.php';
/**
 * @see Xyster_Container_Monitor_Strategy
 */
require_once 'Xyster/Container/Monitor/Strategy.php';
/**
 * A monitor that writes out to a Zend_Log instance.
 * 
 * Tip: You can use a Zend_Log to write to the standard out stream
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Monitor_Log extends Xyster_Container_Monitor_Abstract
{
	/**
	 * @var Zend_Log
	 */
	private $_log;
	
	/**
	 * Creates a new delegating monitor
	 *
	 * @param Zend_Log $log
	 * @param Xyster_Container_Monitor $delegate
	 */
	public function __construct( Zend_Log $log, Xyster_Container_Monitor $delegate = null )
	{
		$this->_log = $log;
		parent::__construct($delegate);
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
    	$this->_log->log(sprintf(parent::INSTANTIATING, (string)$class), Zend_Log::INFO);
    	parent::instantiating($container, $adapter, $class);
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
    	$this->_log->log(sprintf(parent::INSTANTIATED, (string)$class, $duration, parent::parmsToString($injected)), Zend_Log::INFO);
    	parent::instantiated($container, $adapter, $class, $instantiated, $injected, $duration);
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
    	$this->_log->log(sprintf(parent::INSTANTIATION_FAILED, (string)$class, $cause->getMessage()), Zend_Log::ERR);
    	parent::instantiationFailed($container, $adapter, $class, $cause);
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
    	$this->_log->log(sprintf(parent::INVOKING, (string)$member, $member->getDeclaringClass()->getName()), Zend_Log::INFO);
    	parent::invoking($container, $adapter, $member, $instance);
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
    	$this->_log->log(sprintf(parent::INVOKED, (string)$method, $method->getDeclaringClass()->getName(), $duration), Zend_Log::INFO);
    	parent::invoked($container, $adapter, $method, $instance, $duration);
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
    	$this->_log->log(sprintf(parent::INVOCATION_FAILED, (string)$member, $member->getDeclaringClass()->getName(), $cause->getMessage()), Zend_Log::ERR);
    	parent::invocationFailed($member, $instance, $cause);
    }

    /**
     * 
     * @param Xyster_Container_Interface $container
     * @param mixed $key
     * @return mixed 
     */
    public function noComponentFound(Xyster_Container_Interface $container, $key)
    {
    	$this->_log->log(sprintf(parent::NO_COMPONENT, $key), Zend_Log::NOTICE);
        return parent::noComponentFound($container, $key);
    }
}