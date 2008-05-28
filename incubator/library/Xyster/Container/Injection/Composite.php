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
 * @version   $Id: Setter.php 206 2008-01-20 23:01:09Z doublecompile $
 */
/**
 * @see Xyster_Container_Injection_Abstract
 */
require_once 'Xyster/Container/Injection/Abstract.php';
/**
 * Uses multiple injectors
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Composite extends Xyster_Container_Injection_Abstract
{
    protected $_injectors = array();

    /**
     * Creates a new setter injector
     *
     * @param mixed $key the search key for this implementation 
     * @param object $implementation the concrete implementation
     * @param array $parameters the parameters to use for the initialization
     * @param Xyster_Container_Monitor $monitor the component monitor used
     * @param boolean $useNames use argument names when looking up dependencies
     * @param array $injectors An array of injector objects
     */
    public function __construct( $key, $implementation, array $parameters = null, Xyster_Container_Monitor $monitor = null, $useNames = false, array $injectors = array())
    {
        parent::__construct($key, $implementation, $parameters, $monitor, $useNames);
        foreach( $injectors as $v ) {
            if (! $v instanceof Xyster_Container_Injector ) {
                require_once 'Xyster/Container/Injection/Exception.php';
                throw new Xyster_Container_Injection_Exception('Arguments must be injectors');
            }
            $this->_injectors[] = $v;
        }
    }
    
    /**
     * Accepts a visitor for this Adapter
     *
     * @param Xyster_Container_Visitor $visitor the visitor.
     */
    public final function accept(Xyster_Container_Visitor $visitor)
    {
        parent::accept($visitor);
        foreach( $this->_injectors as $v ) {
            /* @var $v Xyster_Container_Injector */
            $v->accept($visitor);
        }
    }

    /**
     * A decorator method 
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Type $into
     * @param object $instance An instance of the type supported by this injector
     */
    public function decorateInstance(Xyster_Container_Interface $container, Xyster_Type $into = null, $instance)
    {
    }
    
    /**
     * Gets the descriptor of this adapter
     *
     * @return string
     */
    public function getDescriptor()
    {
        return 'CompositeInjector';
    }
    
    /**
     * Retrieve the component instance
     * 
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @param Xyster_Type $into the class that is about to be injected into.
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */    
    public function getInstance( Xyster_Container_Interface $container, Xyster_Type $into = null )
    {
        $instance = null;
        foreach( $this->_injectors as $injector ) {
            /* @var $injector Xyster_Container_Injector */
            if ( $instance === null ) {
                $instance = $injector->getInstance($container);
            } else {
                $injector->decorateInstance($container, $into, $instance);
            }
        }
        return $instance;
    }
    
    /**
     * Verify that all dependencies for this adapter can be satisifed
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @throws Exception if one or more dependencies cannot be resolved
     */
    public function verify(Xyster_Container_Interface $container)
    {
        foreach( $this->_injectors as $v ) {
            /* @var $v Xyster_Container_Injector */
            $v->verify($container);
        }
    }
}