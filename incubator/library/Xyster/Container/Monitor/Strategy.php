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
 * Interface responsible for changing monitoring strategy
 * 
 * It may be implemented by containers and single component adapters. The choice
 * of supporting the monitor strategy is left to the implementers of the
 * container and adapters.
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Monitor_Strategy
{
    /**
     * Changes the component monitor used
     * 
     * @param Xyster_Container_Monitor $monitor the new monitor to use
     */
    function changeMonitor( Xyster_Container_Monitor $monitor );

    /**
     * Gets the monitor currently used
     * 
     * @return Xyster_Container_Monitor The monitor currently used
     */
    function currentMonitor();
}