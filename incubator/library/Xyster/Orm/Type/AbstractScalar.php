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
 * @see Xyster_Orm_Type_AbstractType
 */
require_once 'Xyster/Orm/Type/AbstractType.php';
/**
 * Base type mapping for single-column types which can be set to null.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_AbstractScalar extends Xyster_Orm_Type_AbstractType
{
    /**
     * Compare two instances of this type.
     * 
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare($a, $b)
    {
        return strcmp((string)$a, (string)$b);
    }
    
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
     * Gets the fetch type for binding.
     *
     * This should be overridden if there is a specific fetch type that applies. 
     * 
     * @return int
     */
    public function getFetchType()
    {
        return null;
    }
        
    /**
     * Gets an array of fetch types for the columns
     *
     * @return array of int
     */
    public function getFetchTypes()
    {
        return array($this->getFetchType());
    }
        
    /**
     * Tests whether an object is dirty
     *
     * @param mixed $old The old value
     * @param mixed $current The current value
     * @param array $checkable Boolean for each column's updatability
     * @return boolean
     */
    public function isDirty($old, $current, array $checkable = array())
    {
        return (!count($checkable) ||
            ($checkable[0] && parent::isDirty($old, $current, $checkable)));
    }
    
    /**
     * Given an instance, return which columns would be null.
     *
     * @param mixed $value
     * @return array
     */
    public function toColumnNullness($value)
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