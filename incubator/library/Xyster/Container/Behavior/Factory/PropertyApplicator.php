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
 * @see Xyster_Container_Behavior_Factory_Abstract
 */
require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
/**
 * A component factory that creates property applicator instances
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Factory_PropertyApplicator extends Xyster_Container_Behavior_Factory_Abstract
{
    /**
     * Adds a component adapter
     *
     * @param Xyster_Container_Monitor $componentMonitor
     * @param Zend_Config $componentProperties
     * @param Xyster_Container_Adapter $adapter
     * @return Xyster_Container_Adapter
     */
    public function addComponentAdapter(Xyster_Container_Monitor $componentMonitor, Zend_Config $componentProperties, Xyster_Container_Adapter $adapter)
    {
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::PROPERTY_APPLYING());
        return new Xyster_Container_Behavior_PropertyApplicator(parent::addComponentAdapter(
            $componentMonitor, $componentProperties, $adapter));
    }
    
    /**
     * Creates a component adapter
     *
     * {@inherit}
     *
     * @param Xyster_Container_Monitor $componentMonitor
     * @param Zend_Config $componentProperties
     * @param mixed $componentKey
     * @param mixed $componentImplementation
     * @param mixed $parameters
     */
    public function createComponentAdapter(Xyster_Container_Monitor $componentMonitor, Zend_Config $componentProperties, $componentKey, $componentImplementation, $parameters)
    {
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::PROPERTY_APPLYING());
        return new Xyster_Container_Behavior_PropertyApplicator(parent::createComponentAdapter(
            $componentMonitor, $componentProperties, $componentKey, $componentImplementation, $parameters));
    }
}