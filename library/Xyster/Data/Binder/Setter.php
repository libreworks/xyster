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
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Data_Binder_Setter_Interface
 */
require_once 'Xyster/Data/Binder/Setter/Interface.php';
/**
 * A mediator that applies a value to a target object
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Binder_Setter implements Xyster_Data_Binder_Setter_Interface
{
    /**
     * Sets the value in the target 
     *
     * @param object $target An object
     * @param string $field The name of the field to set
     * @param mixed $value The value to set
     * @throws Xyster_Data_Binder_Setter_Exception if there was a problem setting
     */
    public function set($target, $field, $value)
    {
        if ( !is_object($target) ) {
            require_once 'Xyster/Data/Binder/Setter/Exception.php';
            throw new Xyster_Data_Binder_Setter_Exception('Only objects can be set');
        }
        $propertyExists = property_exists($target, $field);
        $methodName = 'set' . ucfirst($field);
        $methodExists = method_exists($target, $methodName); 
        if ( $target instanceof ArrayAccess && !$propertyExists && !$methodExists ) {
            $target[$field] = $value; 
        } else if ( $methodExists ) {
            $target->$methodName($value);
        } else {
            $target->$field = $value;
        }
    }
}