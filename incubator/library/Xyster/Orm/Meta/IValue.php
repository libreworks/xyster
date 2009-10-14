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
 * A combination of an ORM Type and one or more Columns.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Meta_IValue
{
    /**
     * Gets the columns in the type
     *
     * @return Traversable containing {@link Xyster_Db_Column} objects
     */
    function getColumns();
    
    /**
     * Gets the number of columns in the value
     *
     * @return int
     */
    function getColumnSpan();
    
    /**
     * Gets the table associated with this value
     *
     * @return Xyster_Db_Table
     */
    function getTable();
    
    /**
     * Gets the underlying ORM type
     *
     * @return Xyster_Orm_IType
     */
    function getType();
        
    /**
     * Gets whether this type is nullable
     *
     * @return boolean
     */
    function isNullable();
}