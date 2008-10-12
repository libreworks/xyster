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
 * Wrapper around low-level data type and column persistence
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Mapping_Value_Interface
{
    /**
     * Gets the columns in the type
     *
     * @return Iterator containing {@link Xyster_Db_Column} objects
     */
    function getColumnIterator();
    
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
     * @return Xyster_Orm_Type_Interface
     */
    function getType();
        
    /**
     * Gets whether this type is nullable
     *
     * @return boolean
     */
    function isNullable();

    /**
     * Tells whether this type is a Xyster_Orm_Mapping_Value_Simple
     *
     * @return boolean
     */
    function isSimple();
    
    /**
     * Sets the Type of this value
     *
     * @param Xyster_Orm_Type_Interface $type
     */
    function setType( Xyster_Orm_Type_Interface $type );
}