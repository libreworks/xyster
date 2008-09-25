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
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Handles calls from a proxy
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Type_Proxy_Handler_Interface
{
    /**
     * Called when a method is invoked on the proxy
     * 
     * @param stdClass $object The object being called
     * @param ReflectionMethod $called The method in the proxy class
     * @param array $args The arguments passed to the method
     * @param ReflectionMethod $parent The method in the parent class (null if interface or abstract method)
     * @return mixed The result of the method 
     */
    function invoke($object, ReflectionMethod $called, array $args, ReflectionMethod $parent = null);
}