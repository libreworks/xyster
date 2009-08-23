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
 * @see Xyster_Orm_Type_Nullable
 */
require_once 'Xyster/Orm/Type/Nullable.php';
/**
 * Base type mapping for objects which cannot be changed (string, int, etc)
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_Immutable extends Xyster_Orm_Type_Nullable
{
    /**
     * Gets a deep copy of the persistent state; stop on entity and collection
     *
     * @param mixed $value
     * @return mixed A copy
     */
    public function deepCopy( $value )
    {
        return $value;
    }
    
    /**
     * Whether this type can be altered 
     *
     * @return boolean
     */
    public function isMutable()
    {
        return false;
    }
    
    /**
     * Replace the target value we are merging with the original from the detached 
     * 
     * @param object $original
     * @param object $target
     * @param object $owner
     * @param Xyster_Orm_Session_Interface $session
     * @param Xyster_Collection_Map_Interface $copyCache
     * @return object
     */
    public function replace( $original, $target, $owner, Xyster_Orm_Session_Interface $session, Xyster_Collection_Map_Interface $copyCache )
    {
        return $original;
    }
}