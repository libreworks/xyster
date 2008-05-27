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
 * @see Xyster_Container_Adapter
 */
require_once 'Xyster/Container/Adapter.php';
/**
 * Instantiates and injects dependancies into Constructors, Methods and Fields
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Injector extends Xyster_Container_Adapter
{
    /**
     * A decorator method 
     *
     * @param Xyster_Container_Interface $container
     * @param Xyster_Type $into
     * @param object $instance An instance of the type supported by this injector
     */
    function decorateInstance(Xyster_Container_Interface $container, Xyster_Type $into, $instance);
}