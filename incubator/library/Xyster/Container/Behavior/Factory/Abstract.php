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
 * @see Xyster_Container_Behavior_Factory
 */
require_once 'Xyster/Container/Behavior/Factory.php';
/**
 * @see Xyster_Container_Features
 */
require_once 'Xyster/Container/Features.php';
/**
 * Extends Xyster_Container_Adapter_Factory to provide methods for Behaviors
 * 
 * The main use of the factory is to customize the default component adapter 
 * used when none is specified explicitly.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Factory_Abstract implements Xyster_Container_Behavior_Factory
{
    /**
     * @var Xyster_Container_Adapter_Factory 
     */
    private $_delegate;

    /**
     * Adds a component adapter
     *
     * @param Xyster_Container_Monitor $monitor
     * @param Xyster_Collection_Map_Interface $properties
     * @param Xyster_Container_Adapter $adapter
     * @return Xyster_Container_Adapter
     */
    public function addComponentAdapter(Xyster_Container_Monitor $monitor, Xyster_Collection_Map_Interface $properties, Xyster_Container_Adapter $adapter)
    {
        if ($this->_delegate != null && $this->_delegate instanceof Xyster_Container_Behavior_Factory) {
            return $this->_delegate->addComponentAdapter($monitor, $properties, $adapter);
        }
        return $adapter;
    }
    
    /**
     * Creates a component adapter
     *
     * {@inherit}
     *
     * @param Xyster_Container_Monitor $monitor
     * @param Xyster_Collection_Map_Interface $properties
     * @param mixed $key
     * @param mixed $implementation
     * @param mixed $parameters
     */
    public function createComponentAdapter(Xyster_Container_Monitor $monitor, Xyster_Collection_Map_Interface $properties, $key, $implementation, $parameters)
    {
        if ($this->_delegate == null) {
            require_once 'Xyster/Container/Injection/Factory/Adaptive.php';
            $this->_delegate = new Xyster_Container_Injection_Factory_Adaptive;
        }
        return $this->_delegate->createComponentAdapter($monitor, $properties,
            $key, $implementation, $parameters);
    }

    /**
     * Removes properties from a map
     * 
     * @param Xyster_Collection_Map_Interface $current This must be writable!
     * @param Xyster_Collection_Map_Interface $present
     * @return boolean
     */
    public static function removePropertiesIfPresent(Xyster_Collection_Map_Interface $current, Xyster_Collection_Map_Interface $present)
    {
        foreach( $present as $key => $value ) {
            $presentValue = $present[$key];
            $currentValue = $current[$key];
            if ($currentValue == null) {
                return false;
            }
            if (!$presentValue == $currentValue) {
                return false;
            }
        }
        foreach( $present as $key => $value ) {
            unset($current[$key]);
        }
        return true;
    }

    /**
     * Wraps another factory
     *
     * @param Xyster_Container_Adapter_Factory $delegate
     * @return Xyster_Container_Behavior_Factory_Abstract 
     */
    public function wrap(Xyster_Container_Adapter_Factory $delegate)
    {
        $this->_delegate = $delegate;
        return $this;
    }
}