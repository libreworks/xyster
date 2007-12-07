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
 * This adapter will instantiate a new object for each call to getInstance 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Injection_Abstract extends Xyster_Container_Component_Adapter_Abstract
{
    protected $_parameters = array();

    /**
     * Constructs a new adapter for the given key and implementation
     *
     * @param mixed $componentKey
     * @param ReflectionClass $componentImplementation
     * @param array $parameters
     * @param Xyster_Container_Component_Monitor $monitor
     */
    public function __construct( $componentKey, $componentImplementation, array $parameters = null, Xyster_Container_Component_Monitor $monitor )
    {
        parent::__construct($componentKey, $componentImplementation, $monitor);
        $this->_checkConcrete();
        if ( $parameters != null ) {
            foreach( $parameters as $k => $param ) {
                if ( ! $param instanceof Xyster_Container_Parameter ) {
                    require_once 'Xyster/Container/Exception.php';
                    throw new Xyster_Container_Exception('Parameter ' . $k . ' is an incorrect type');
                }
            }
        }
        $this->_parameters = $parameters;
    }
    
    /**
     * Checks to make sure the current implementation is a concrete class
     *
     * @throws Xyster_Container_Exception if the implementation isn't concrete
     */
    protected function _checkConcrete()
    {
        $isAbstract = $this->getImplementation()->isAbstract();
        if ( $this->getImplementation()->isInterface() || $isAbstract ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception($this->getImplementation()->getName() . ' is not a concrete class');
        }
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
        parent::accept($visitor);
        if ( $this->_parameters ) {
            foreach( $parameter as $param ) {
                $param->accept($visitor);
            }
        }
    }
    
    /**
     * Create default parameters for the given types
     *
     * @param array $parameters the parameter types (ReflectionClass objects)
     * @return array the array with the default parameters.
     */
    protected function _createDefaultParameters( array $parameters )
    {
        $componentParameters = array();
        foreach( $parameters as $parameter ) {
            $componentParameters[] = Xyster_Container_Parameter_Component::standard();
        }
        return $componentParameters;
    }
    
    /**
     * Instantiate an object with given parameters and respect the accessible flag
     * 
     * @param ReflectionClass $class the class to construct
     * @param array $parameters the parameters for the constructor 
     * @return object the new object
     */
    protected function _newInstance(ReflectionClass $class, array $parameters = array())
    {
        return $class->newInstanceArgs($parameters);
    }

    protected function _caughtInstantiationException(Xyster_Container_Component_Monitor $componentMonitor,
                                                ReflectionClass $class,
                                                Exception $e, Xyster_Container_Interface $container) {
        $this->_componentMonitor->instantiationFailed($container, $this, $class, $e);
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception("Should never get here");
    }

    protected function _caughtIllegalAccessException(Xyster_Container_Component_Monitor $componentMonitor,
                                                ReflectionClass $class,
                                                Exception $e, Xyster_Container_Interface $container) {
        $this->_componentMonitor->instantiationFailed($container, $this, $class, $e);
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception($e->getMessage());
    }

    protected function _caughtInvocationTargetException(Xyster_Container_Component_Monitor $componentMonitor,
                                                   ReflectionMethod $member,
                                                   $componentInstance, Exception $e) {
        $this->_componentMonitor->invocationFailed($member, $componentInstance, $e);
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception($e->getMessage());
    }

    protected function _caughtIllegalAccessException(Xyster_Container_Component_Monitor $componentMonitor,
                                                ReflectionMethod $member,
                                                $componentInstance, Exception $e) {
        $this->_componentMonitor->invocationFailed($member, $componentInstance, $e);
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception($e->getMessage());
    }
}