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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Type\Proxy;
/**
 * Handles calls from a proxy
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface IHandler
{
    /**
     * Called when a method is invoked on the proxy
     * 
     * @param object $object The object being called
     * @param ReflectionMethod $called The method in the proxy class
     * @param array $args The arguments passed to the method
     * @param object $delegate The proxied object: never null, but may be equal to <code>$object</code>.
     * @param ReflectionMethod $parent The method in the parent class (null if interface or abstract method)
     * @return mixed The result of the method 
     */
    function invoke($object, \ReflectionMethod $called, array $args, $delegate, \ReflectionMethod $parent = null);
}