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
 * @package   Xyster_Controller
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injection_Setter
 */
require_once 'Xyster/Container/Injection/Setter.php';
/**
 * An injector that can be used for setter injection with a front controller
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Action_Injector extends Xyster_Container_Injection_Setter
{
    /**
     * The methods to ignore
     *
     * @var array
     */
    protected static $_ignore = array('setFrontController', 'setRequest', 'setResponse');
     
    /**
     * {@inherit}
     *
     * @param ReflectionMethod $method
     * @return boolean
     */
    protected function _isInjectorMethod( ReflectionMethod $method )
    {
        $methodName = $method->getName();
        return !in_array($methodName, self::$_ignore) &&
            parent::_isInjectorMethod($method);
    }
}