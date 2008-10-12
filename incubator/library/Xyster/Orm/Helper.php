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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * A helper for the ORM system
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Helper
{
    static private $_unfetchedProperty;

    /** 
     * Whether the object given is an unfetched property
     * 
     * @param mixed $object
     * @return boolean
     */
    public static function isUnfetchedProperty($object)
    {
        return $object == self::getUnfetchedProperty();
    }
    
    /**
     * Gets the unfetched property
     * 
     * @return object
     */
    static function getUnfetchedProperty()
    {
         if ( self::$_unfetchedProperty === null ) {
             $prop = new stdClass;
             $prop->value = 0x100000000;
             self::$_unfetchedProperty = $prop;
         }
         return self::$_unfetchedProperty;
    }
}