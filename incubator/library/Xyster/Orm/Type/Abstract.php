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
 * @see Xyster_Orm_Type_Interface
 */
require_once 'Xyster/Orm/Type/Interface.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Base type mapping
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_Abstract implements Xyster_Orm_Type_Interface
{
    /**
     * Called before the unpack to allow batch fetching of uncached entities
     *
     * @param unknown_type $cached
     * @param Xyster_Orm_Session_Interface $sess
     */
    public function beforeUnpack( $cached, Xyster_Orm_Session_Interface $sess )
    {
    }
    
    /**
     * Return a cacheable copy of the object
     *
     * @param mixed $value
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $owner
     * @return mixed Disassembled, deep copy
     */
    public function cachePack( $value, Xyster_Orm_Session_Interface $sess, $owner )
    {
        return ( $value === null ) ? null : $this->deepCopy($value);
    }
    
    /**
     * Reconstruct the object from its cached copy
     *
     * @param mixed $cached
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $owner
     * @return mixed The reconstructed value
     */
    public function cacheUnpack( $cached, Xyster_Orm_Session_Interface $sess, $owner )
    {
        return ( $cached === null ) ? null : $this->deepCopy($cached);
    }
    
    /**
     * Compare two instances of this type
     *
     * One probably wants to overwrite this method
     * 
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare( $a, $b )
    {
        return strcmp((string)$a, (string)$b);
    }

    /**
     * Resolves the value pulled from the statement into the proper type
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     */
    public function get( array $values, $owner, Xyster_Orm_Session_Interface $sess )
    {
    }
    
    /**
     * Whether this type needs to have {@link get}() called
     *
     * @return boolean
     */
    public function hasResolve()
    {
        return false;
    }

    /**
     * Whether this type is an association type
     * 
     * @return boolean
     */
    public function isAssociation()
    {
        return false;
    }
    
    /**
     * Whether this type is a collection
     *
     * @return boolean
     */
    public function isCollection()
    {
        return false;
    }
    
    /**
     * Whether this type is a component
     *
     * @return boolean
     */
    public function isComponentType()
    {
        return false;
    }
    
    /**
     * Tests whether an object is dirty
     *
     * @param mixed $old The old value
     * @param mixed $current The current value
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $checkable Boolean for each column's updatability
     */
    public function isDirty( $old, $current, Xyster_Orm_Session_Interface $sess, array $checkable = array() )
    {
        return !$this->isSame($old, $current);
    }
    
    /**
     * Whether this type is an entity
     *
     * @return boolean
     */
    public function isEntityType()
    {
        return false;
    }
    
    /**
     * Compares the values supplied for persistence equality
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function isEqual($a, $b)
    {
        return Xyster_Type::areDeeplyEqual($a, $b);
    }

    /**
     * Compares the values supplied for persistence equality
     * 
     * Types that compare actual objects should compare identity (===).
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function isSame($a, $b)
    {
        return $this->isEqual($a, $b);
    }
    
    /**
     * Replace the target value we are merging with the original from the detached 
     * 
     * @param object $original
     * @param object $target
     * @param object $owner
     * @param Xyster_Orm_Session_Interface $session
     * @param Xyster_Collection_Map_Interface $copyCache
     * @param Xyster_Orm_Engine_ForeignKeyDirection $fkDir
     * @return object
     */
    public function replaceWithDirection( $original, $target, $owner, Xyster_Orm_Session_Interface $session, Xyster_Collection_Map_Interface $copyCache, Xyster_Orm_Engine_ForeignKeyDirection $fkDir )
    {
        $direction = $this->isAssociation() ? $this->getForeignKeyDirection() :
            Xyster_Orm_Engine_ForeignKeyDirection::FromParent();
        
        return $direction === $fkDir ?
            $this->replace($original, $target, $owner, $session, $copyCache) :
            $target;
    }
}