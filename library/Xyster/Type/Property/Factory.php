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
 * Creates Xyster_Type_Property_Interface objects
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Type_Property_Factory
{
    protected static $_props = array();
    
    /**
     * Gets the property wrapper appropriate for the object
     *
     * @param stdClass $target
     * @param string $property
     * @return Xyster_Type_Property_Interface
     */
    public static function get( stdClass $target, $property )
    {
        $className = get_class($target);
        if ( !array_key_exists($className, self::$_props) ) {
            $magicSetGet = method_exists($target, '__get') &&
                method_exists($target, '__set');
            $magicCall = method_exists($target, '__call');
            $methodExists = method_exists($target, 'set' . ucfirst($property));
            $propertyExists = property_exists($target, $property);
            
            if ( $target instanceof ArrayAccess && !$propertyExists && !$methodExists ) {
                require_once 'Xyste/Type/Property/Map.php';
                self::$_props[$className] = 'Xyster_Type_Property_Map';
            } else if ( $methodExists || $magicCall ) {
                require_once 'Xyster/Type/Property/Method.php';
                self::$_props[$className] = 'Xyster_Type_Property_Method';
            } else {
                require_once 'Xyster/Type/Property/Direct.php';
                self::$_props[$className] = 'Xyster_Type_Property_Direct';
            }
        }
        $propertyClassName = self::$_props[$className];
        return new $propertyClassName($property); 
    }
}