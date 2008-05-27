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
 * @version   $Id: PropertyApplicator.php 211 2008-01-23 02:33:37Z doublecompile $
 */
/**
 * @see Xyster_Container_Behavior_Abstract
 */
require_once 'Xyster/Container/Behavior/Abstract.php';
/**
 * Behavior to decorate objects
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Decorated extends Xyster_Container_Behavior_Abstract
{
    /**
     * @var Xyster_Container_Behavior_Decorator
     */
    private $_decorator;
    
    /**
     * Creates a new decorated behavior
     *
     * @param Xyster_Container_Adapter $delegate 
     * @param Xyster_Container_Behavior_Decorator $decorator
     */
    public function __construct( Xyster_Container_Adapter $delegate, Xyster_Container_Behavior_Decorator $decorator )
    {
        parent::__construct($delegate);
        $this->_decorator = $decorator;
    }
    
    /**
     * Get a component instance
     *
     * @param Xyster_Container_Interface $container
     * @return object
     */
    public function getInstance( Xyster_Container_Interface $container, Xyster_Type $into = null )
    {
        $instance = parent::getInstance($container, $into);
        $this->_decorator->decorate($instance);
        return $instance;        
    }
    
    /**
     * Gets the descriptor for this adapter
     *
     * @return string
     */
    public function getDescriptor()
    {
        return 'Decorated';
    }
}