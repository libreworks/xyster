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
 * @see Xyster_Orm_Type_Abstract
 */
require_once 'Xyster/Orm/Type/Abstract.php';
/**
 * @see Xyster_Db_DataType
 */
require_once 'Xyster/Db/DataType.php';
/**
 * Zend_Db
 */
require_once 'Zend/Db.php';
/**
 * Base type mapping for single-column types which can be set to null
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_Nullable extends Xyster_Orm_Type_Abstract
{
    /**
     * Gets how many columns are used to persist this type
     *
     * @return int
     */
    public function getColumnSpan()
    {
        return 1;
    }
    
    /**
     * Gets an array of Xyster_Db_DataType objects for the columns in this type
     *
     * @return array of {@link Xyster_Db_DataType} objects
     */
    public function getDataTypes()
    {
        return array($this->getDataType());
    }
    
    /**
     * Tests whether an object is dirty
     *
     * @param mixed $old The old value
     * @param mixed $current The current value
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $checkable Boolean for each column's updatability
     * @return boolean
     */
    public function isDirty( $old, $current, Xyster_Orm_Session_Interface $sess, array $checkable = array() )
    {
        return (!count($checkable) ||
            ($checkable[0] && parent::isDirty($old, $current, $sess)));
    }
    
    /**
     * Given an instance, return which columns would be null
     *
     * @param mixed $value
     * @return array
     */
    public function toColumnNullness( $value )
    {
        return $value === null ? array(false) : array(true);
    }
    
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    abstract public function getDataType();
}