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
 * @see Xyster_Collection
 */
require_once 'Xyster/Collection.php';
/**
 * @see Xyster_Collection_Map_String
 */
require_once 'Xyster/Collection/Map/String.php';
/**
 * A class of map objects holding behavior characteristics
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
    
    /**
     * Used for Constructor Dependency Injection 
     *
     * @return Xyster_Collection_Map
     */
    public static function CDI()
    {
        return self::_config(self::$_INJECTION, self::$_CONSTRUCTOR);
    }
    
    /**
     * Used for Setter Dependency Injection 
     *
     * @return Xyster_Collection_Map
     */
    public static function SDI()
    {
        return self::_config(self::$_INJECTION, self::$_SETTER);
    }

    /**
     * Used for Method Dependency Injection 
     *
     * @return Xyster_Collection_Map
     */
    public static function METHOD_INJECTION()
    {
        return self::_config(self::$_INJECTION, self::$_METHOD);
    } 

    /**
     * Used for Non-caching 
     *
     * @return Xyster_Collection_Map
     */
    public static function NO_CACHE()
    {
        return self::_config(self::$_CACHE, self::$_FALSE);
    }

    /**
     * Used for caching 
     *
     * @return Xyster_Collection_Map
     */
    public static function CACHE()
    {
        return self::_config(self::$_CACHE, self::$_TRUE);
    }

    /**
     * An alias for CACHE 
     *
     * @see CACHE
     * @return Xyster_Collection_Map
     */
    public static function SINGLE() 
    {
        return self::CACHE();
    }
    
    /**
     * Used for no features 
     *
     * @return Xyster_Collection_Map
     */
    public static function NONE()
    {
        return self::_config(self::$_NONE, "");
    }

    /**
     * Used for Property Applying adapters 
     *
     * @return Xyster_Collection_Map
     */
    public static function PROPERTY_APPLYING()
    {
        return self::_config(self::$_PROPERTY_APPLYING, self::$_TRUE);
    }

    /**
     * Used for automatic behaviors
     *
     * @return Xyster_Collection_Map
     */
    public static function AUTOMATIC()
    {
        return self::_config(self::$_AUTOMATIC, self::$_TRUE);
    }
    
    /**
     * Gets a map object for the name and value passed
     *
     * @param string $key
     * @param string $value
     * @return Xyster_Collection_Map
     */
    protected static function _config( $key, $value )
    {
        $featuresKey = $key.':'.$value;
        if ( !isset(self::$_features[$featuresKey]) ) {
            $map = new Xyster_Collection_Map_String;
            $map->set($key, $value);
            self::$_features[$featuresKey] = Xyster_Collection::fixedMap($map); 
        }
        return self::$_features[$featuresKey];
    }
}