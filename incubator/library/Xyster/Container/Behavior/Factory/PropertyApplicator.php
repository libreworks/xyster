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
 * @see Xyster_Container_Behavior_PropertyApplicator
 */
require_once 'Xyster/Container/Behavior/PropertyApplicator.php';
/**
 * A component factory that creates property applicator instances
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Factory_PropertyApplicator extends Xyster_Container_Behavior_Factory_Abstract
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
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::PROPERTY_APPLYING());
        return new Xyster_Container_Behavior_PropertyApplicator(parent::addComponentAdapter(
            $monitor, $properties, $adapter));
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
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::PROPERTY_APPLYING());
        return new Xyster_Container_Behavior_PropertyApplicator(parent::createComponentAdapter(
            $monitor, $properties, $key, $implementation, $parameters));
    }
}