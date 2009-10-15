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
 * @see Xyster_Orm_Meta_IValue
 */
require_once 'Xyster/Orm/Meta/IValue.php';
/**
 * The abstract definition for a collection
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Meta_Value_Collection implements Xyster_Orm_Meta_IValue
{
    /**
     * @var Xyster_Orm_Meta_Entity
     */
    protected $_owner;
    
    /**
     * Creates a new Collection value
     * 
     * @param $owner The entity owner
     */
    public function __construct(Xyster_Orm_Meta_Entity $owner)
    {
        $this->_owner = $owner;
    }
    
    /**
     * Gets the columns in the type
     *
     * @return Traversable containing {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        return new EmptyIterator();
    }
    
    /**
     * Gets the number of columns in the value
     *
     * @return int
     */
    public function getColumnSpan()
    {
        return 0;
    }
    
    /**
     * Gets the table associated with this value
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        return $this->_owner->getTable();
    }
    
    /**
     * Gets the underlying ORM type
     *
     * @return Xyster_Orm_IType
     */
    public function getType()
    {
        return null;
    }
        
    /**
     * Gets whether this type is nullable
     *
     * @return boolean
     */
    public function isNullable()
    {
        return true;
    }
}