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
 * Type factory
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Factory
{
    /**
     * Convenience method to call {@link Xyster_Orm_Type_Interface::replace} for several values
     * 
     * @param array $original
     * @param array $target
     * @param array $types
     * @param object $owner
     * @param Xyster_Orm_Session_Interface $session
     * @param Xyster_Collection_Map_Interface $copyCache
     * @return array
     */
    public static function replace( array $original, array $target, array $types, $owner, Xyster_Orm_Session_Interface $session, Xyster_Collection_Map_Interface $copyCache )
    {
        $copied = array();
        foreach( $types as $i=>$type ) {
            /* @todo lazy properties and backreferences */
            $copied[] = $type->replace($original[$i], $target[$i], $owner, $session, $copyCache);
        }
        return $copied;
    }
    
    /**
     * Convenience method to call {@link Xyster_Orm_Type_Interface::replace} for several values
     * 
     * @param array $original
     * @param array $target
     * @param array $types
     * @param object $owner
     * @param Xyster_Orm_Session_Interface $session
     * @param Xyster_Collection_Map_Interface $copyCache
     * @param Xyster_Orm_Engine_ForeignKeyDirection $fkeyDir
     * @return array
     */
    public static function replaceWithDirection( array $original, array $target, array $types, $owner, Xyster_Orm_Session_Interface $session, Xyster_Collection_Map_Interface $copyCache, Xyster_Orm_Engine_ForeignKeyDirection $fkeyDir )
    {
        $copied = array();
        foreach( $types as $i=>$type ) {
            /* @todo lazy properties and backreferences */
            $copied[] = $type->replaceWithDirection($original[$i], $target[$i], $owner, $session, $copyCache);
        }
        return $copied;
    }
}