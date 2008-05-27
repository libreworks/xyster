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
 * @see Xyster_Container_Behavior_Factory_Abstract
 */
require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
/**
 * @see Xyster_Container_Behavior_Decorated
 */
require_once 'Xyster/Container/Behavior/Decorated.php';
/**
 * @see Xyster_Container_Behavior_Decorator
 */
require_once 'Xyster/Container/Behavior/Decorator.php';
/**
 * Behavior factory for decorating
 * 
 * This factory will create decorated that will allow you to decorate what you
 * like on the component instance that has been created.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Behavior_Factory_Decorating extends Xyster_Container_Behavior_Factory_Abstract implements Xyster_Container_Behavior_Decorator 
{
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
        return new Xyster_Container_Behavior_Decorated(
            parent::addComponentAdapter($monitor, $properties, $adapter), $this);
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
        return new Xyster_Container_Behavior_Decorated(parent::createComponentAdapter(
            $monitor, $properties, $key, $implementation, $parameters), $this);
    }
}