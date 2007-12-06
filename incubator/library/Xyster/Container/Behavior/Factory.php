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
 * @see Xyster_Container_Component_Factory
 */
require_once 'Xyster/Container/Component/Factory.php';
/**
 * Extends Xyster_Container_Component_Factory to provide methods for Behaviors
 * 
 * The main use of the factory is to customize the default component adapter 
 * used when none is specified explicitly.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Behavior_Factory extends Xyster_Container_Component_Factory
{
    /**
     * Adds a component adapter
     *
     * @param Xyster_Container_Component_Monitor $componentMonitor
     * @param Zend_Config $componentProperties
     * @param Xyster_Container_Component_Adapter $adapter
     * @return Xyster_Container_Component_Adapter
     */
    function addComponentAdapter(Xyster_Container_Component_Monitor $componentMonitor,
                        Zend_Config $componentProperties,
                        Xyster_Container_Component_Adapter $adapter);

    /**
     * Wraps a component factory
     *
     * @param Xyster_Container_Component_Factory $delegate
     * @return Xyster_Container_Component_Factory
     */
    function wrap(Xyster_Container_Component_Factory $delegate);
}