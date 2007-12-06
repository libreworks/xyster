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
 * Zend_Config
 */
require_once 'Zend/Config.php';
/**
 * A class of Zend_Config objects holding behavior characteristics
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Features
{
    private static $_INJECTION = "injection";
    private static $_NONE = "none";
    private static $_CONSTRUCTOR = "constructor";
    private static $_METHOD = "method";
    private static $_SETTER = "setter";
    private static $_CACHE = "cache";
    private static $_PROPERTY_APPLYING = "property-applying";
    private static $_AUTOMATIC = "automatic";
    private static $_FALSE = "false";
    private static $_TRUE = "true";

    /**
     * A cache for the features
     *
     * @var array
     */
    private static $_features = array();
    
    public static function CDI()
    {
        return self::_config(self::$_INJECTION, self::$_CONSTRUCTOR);
    }

    public static function SDI()
    {
        return self::_config(self::$_INJECTION, self::$_SETTER);
    }

    public static function METHOD_INJECTION()
    {
        return self::_config(self::$_INJECTION, self::$_METHOD);
    } 

    public static function NO_CACHE()
    {
        return self::_config(self::$_CACHE, self::$_FALSE);
    }

    public static function CACHE()
    {
        return self::_config(self::$_CACHE, self::$_TRUE);
    }

    public static function SINGLE() 
    {
        return self::CACHE();
    }
    
    public static function NONE()
    {
        return self::_config(self::$_NONE, "");
    }

    public static function PROPERTY_APPLYING()
    {
        return self::_config(self::$_PROPERTY_APPLYING, self::$_TRUE);
    }

    public static function AUTOMATIC()
    {
        return self::_config(self::$_AUTOMATIC, self::$_TRUE);
    }
    
    /**
     * Gets a Zend_Config object for the name and value passed
     *
     * @param string $key
     * @param string $value
     * @return Zend_Config
     */
    protected function _config( $key, $value )
    {
        $featuresKey = $key.':'.$value;
        if ( !isset(self::$_features[$featuresKey]) ) {
            self::$_features[$featuresKey] = new Zend_Config(array($key => $value)); 
        }
        return self::$_features[$featuresKey];
    }
}