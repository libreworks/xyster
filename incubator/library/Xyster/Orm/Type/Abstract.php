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
}