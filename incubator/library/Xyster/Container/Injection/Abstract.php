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
 * @see Xyster_Container_Adapter_Abstract
 */
require_once 'Xyster/Container/Adapter/Abstract.php';
/**
 * This adapter will instantiate a new object for each call to getInstance 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Injection_Abstract extends Xyster_Container_Adapter_Abstract
{
    protected $_parameters = array();

    /**
     * Constructs a new adapter for the given key and implementation
     *
     * @param mixed $key
     * @param Xyster_Type $implementation
     * @param array $parameters
     * @param Xyster_Container_Monitor $monitor
     */
    public function __construct( $key, $implementation, array $parameters = null, Xyster_Container_Monitor $monitor = null )
    {
        parent::__construct($key, $implementation, $monitor);
        
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
        $class = $this->getImplementation()->getClass();
        if ( $class instanceof ReflectionClass && ( $class->isInterface() || $class->isAbstract() ) ) {
            require_once 'Xyster/Container/Exception.php';
            throw new Xyster_Container_Exception($class->getName() . ' is not a concrete class');
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
            foreach( $this->_parameters as $param ) {
                /* @var $param Xyster_Container_Parameter */
                $param->accept($visitor);
            }
        }
    }
    
    /**
     * Create default parameters for the given types
     *
     * @param array $parameters the parameter types (Xyster_Type objects)
     * @return array the array with the default parameters.
     */
    protected function _createDefaultParameters( array $parameters )
    {
        $componentParameters = array();
        foreach( $parameters as $parameter ) {
            require_once 'Xyster/Container/Parameter/Basic.php';
            $componentParameters[] = Xyster_Container_Parameter_Basic::standard();
        }
        return $componentParameters;
    }
    
    /**
     * Instantiate an object with given parameters and respect the accessible flag
     * 
     * @param Xyster_Type $type the class to construct
     * @param array $parameters the parameters for the constructor 
     * @return object the new object
     */
    protected function _newInstance(Xyster_Type $type, array $parameters = array())
    {
        $class = $type->getClass();
        if ( $type->getName() == 'array' ) {
            return array();
        } else {
            return ( $class->getConstructor() ) ?
                $class->newInstanceArgs($parameters) : $class->newInstance();
        }
    }

    protected function _caughtInstantiationException(Xyster_Container_Monitor $monitor, Xyster_Type $class, Exception $e, Xyster_Container_Interface $container)
    {
        $monitor->instantiationFailed($container, $this, $class, $e);
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception($e->getMessage());
    }

    protected function _caughtInvocationTargetException(Xyster_Container_Monitor $monitor, ReflectionMethod $member, $componentInstance, Exception $e)
    {
        $monitor->invocationFailed($member, $componentInstance, $e);
        require_once 'Xyster/Container/Exception.php';
        throw new Xyster_Container_Exception($e->getMessage());
    }
}