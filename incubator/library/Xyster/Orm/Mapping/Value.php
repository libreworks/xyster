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
 * @see Xyster_Orm_Mapping_Value_Interface
 */
require_once 'Xyster/Orm/Mapping/Value/Interface.php';
/**
 * A simple value
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapping_Value implements Xyster_Orm_Mapping_Value_Interface
{
    /**
     * @var array
     */
    protected $_columns = array();
    
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
    /**
     * @var Xyster_Orm_Type_Interface
     */
    protected $_type;
    
    /**
     * Creates a new simple value
     *
     * @param Xyster_Db_Table $table
     */
    public function __construct( Xyster_Db_Table $table = null )
    {
        if ( $table !== null ) {
            $this->_table = $table;
        }
    }
    
    /**
     * Adds a column to the value
     *
     * @param Xyster_Db_Column $column
     * @return Xyster_Orm_Mapping_Value provides a fluent interface
     */
    public function addColumn( Xyster_Db_Column $column )
    {
        if ( !in_array($column, $this->_columns, true) ) {
            $this->_columns[] = $column;
        }
        return $this;
    }
    
    /**
     * Gets the columns in the type
     *
     * @return array containing {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        return array() + $this->_columns;
    }
    
    /**
     * Gets the number of columns in the value
     *
     * @return int
     */
    public function getColumnSpan()
    {
        return count($this->_columns);
    }
    
    /**
     * Gets the table associated with this value
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     * Gets the underlying ORM type
     *
     * @return Xyster_Orm_Type_Interface
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Gets whether this type is nullable
     *
     * @return boolean
     */
    public function isNullable()
    {
        $nullable = true;
        foreach( $this->_columns as $column ) {
            if ( !$column->isNullable() ) {
                $nullable = false;
                break;
            }
        }
        return $nullable;
    }

    /**
     * Tells whether this type is a Xyster_Orm_Mapping_Value_Simple
     *
     * @return boolean
     */
    public function isSimple()
    {
        return true;
    }
    
    /**
     * Sets the table for the value
     *
     * @param Xyster_Db_Table $table
     * @return Xyster_Orm_Mapping_Value provides a fluent interface
     */
    public function setTable( Xyster_Db_Table $table )
    {
        $this->_table = $table;
        return $this;
    }
    
    /**
     * Sets the type name
     *
     * @param Xyster_Orm_Type_Interface $type
     * @return Xyster_Orm_Mapping_Value provides a fluent interface
     */
    public function setType( Xyster_Orm_Type_Interface $type )
    {
        $this->_type = $type;
        return $this;
    }
}