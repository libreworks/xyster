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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Type_IType
 */
require_once 'Xyster/Orm/Type/IType.php';
/**
 * An abstract mapping between a database type and an internal PHP type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_AbstractType implements Xyster_Orm_Type_IType
{
    /**
     * Whether this type has a translation process.
     *
     * @return boolean
     */
    public function hasTranslate()
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
    public function isDirty($old, $current, array $checkable = array())
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
     * Compares the values supplied for persistence equality.
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
     * Compares the values supplied for persistence equality.
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
     * Translates the value pulled from the statement into the proper type.
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     */
    public function translateFrom(array $values, $owner, Xyster_Orm_ISession $sess)
    {
    }
    
    /**
     * (non-PHPdoc)
     * @see Xyster_Orm_Type_IType#translateTo($value, $owner, $sess)
     */
    public function translateTo($value, $owner, Xyster_Orm_ISession $sess)
    {
    }
}