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
 * Creates {@link Xyster\Type\Property\IProperty} objects
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
     * If the target is an array, or it's an instance of <code>ArrayAccess</code>
     * and doesn't have a public field or method getter/setter for the property,
     * a {@link Map} is returned.
     * 
     * If the target has a getter or setter method for the property, or has the
     * magic <code>__call</code> method, a {@link Method} is returned.
     * 
     * Any other value will return a {@link Direct}.
     *
     * @param object $target The object or array
     * @param string $property The name of the field
     * @return IProperty
     * @throws InvalidTypeException if <code>$target</code> isn't an array/object
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