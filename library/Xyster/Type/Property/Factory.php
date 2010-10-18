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
namespace Xyster\Type\Property;
/**
 * Creates Xyster_Type_Property_Interface objects
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Factory
{
    protected static $_props = array();
    
    /**
     * Gets the property wrapper appropriate for the object
     *
     * @param object $target
     * @param string $property
     * @return IProperty
     */
    public static function get( $target, $property )
    {
        if ( is_array($target) ) {
            return new Map($property);
        } else if ( is_object($target) ) {
            $className = get_class($target);
            if ( !array_key_exists($className, self::$_props) ) {
                $magicCall = method_exists($target, '__call');
                $methodExists = method_exists($target, 'set' . ucfirst($property))
                        || method_exists($target, 'get' . ucfirst($property));
                $propertyExists = property_exists($target, $property);
                if ( $target instanceof \ArrayAccess && !$propertyExists && !$methodExists ) {
                    self::$_props[$className] = '\Xyster\Type\Property\Map';
                } else if ( $methodExists || $magicCall ) {
                    self::$_props[$className] = '\Xyster\Type\Property\Method';
                } else {
                    self::$_props[$className] = '\Xyster\Type\Property\Direct';
                }
            }
            $propertyClassName = self::$_props[$className];
            return new $propertyClassName($property);
        } else {
            throw new \Xyster\Type\InvalidTypeException('You can only create property wrappers for arrays or objects');
        }
    }
}