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
 * @package   UnitTests
 * @subpackage Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_Gateway_Abstract
 */
require_once 'Xyster/Db/Gateway/Abstract.php';
/**
 * A stub gateway for unit testing
 *
 */
class Xyster_Db_Gateway_Stub extends Xyster_Db_Gateway_Abstract
{
    public $indexExecuted = false;
    
    public $tableExecuted = false;
    
    /**
     * Lists all foreign keys
     *
     * The return value is an associative array keyed by the key name, as
     * returned by the RDBMS.
     * 
     * The value of each array element is an associative array with the
     * following keys:
     * 
     * KEY_NAME    => string; foreign key name
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string; name of table
     * COLUMNS     => array; An array of the column names
     * ON_UPDATE   => string;
     * ON_DELETE   => string;
     */
    public function listForeignKeys()
    {
        return array();
    }
    
    /**
     * Lists all indexes
     *
     * The return value is an associative array keyed by the index name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     * 
     * INDEX_NAME  => string; index name
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string; name of table
     * COLUMNS     => array; An array of the column names
     * PRIMARY     => boolean; true if the index is a primary key
     * UNIQUE      => boolean; true if the index is a unique key
     * 
     * @return array An array of string index names
     */
    public function listIndexes()
    {
        return array();
    }
    
    public function setAdapter( $db )
    {
        return $this->_setAdapter($db);
    }
    
    /**
     * Gets the SQL statement to create an index
     *
     * @param Xyster_Db_Gateway_IndexBuilder $builder The index builder
     * @return string
     */
    protected function _getCreateIndexSql( Xyster_Db_Gateway_IndexBuilder $builder )
    {
        $this->indexExecuted = true;
    }
    
    /**
     * Gets the SQL statement to create a table
     *
     * @param Xyster_Db_Gateway_TableBuilder $builder The table builder
     * @return string
     */
    protected function _getCreateTableSql( Xyster_Db_Gateway_TableBuilder $builder )
    {
        $this->tableExecuted = true;
        return parent::_getCreateTableSql($builder);
    }
    
    /**
     * Gets the SQL statement to drop an index
     *
     * @param string $name The index name
     * @param string $table The table (not all dbs require this)
     * @return string
     */
    protected function _getDropIndexSql( $name, $table=null )
    {
    }
    
    /**
     * Gets the SQL statement to drop a primary key from a table
     *
     * @param string $table The table name
     * @return string
     */
    protected function _getDropPrimarySql( $table, $name=null )
    {
    }
    
    /**
     * Gets the SQL statement to list the indexes
     *
     * @return string
     */
    protected function _getListIndexesSql()
    {
    }
    
    /**
     * Gets the SQL statement to list the sequences
     *
     * If the DBMS doesn't support sequences, this method won't be called.
     * There is no need to throw an exception for this method, just leave an 
     * empty method body or return null.
     * 
     * @return string
     */
    protected function _getListSequencesSql()
    {
    }
    
    /**
     * Gets the SQL statement to rename an index
     *
     * @param string $old The current index name
     * @param string $new The new index name
     * @param string $table The table name (not all dbs require this)
     * @return string
     */
    protected function _getRenameIndexSql( $old, $new, $table=null )
    {
    }
    
    /**
     * Gets the SQL statement to rename a table
     *
     * @param string $old The current table name
     * @param string $new The new table name
     * @return string
     */
    protected function _getRenameTableSql( $old, $new )
    {
    }
    
    /**
     * Gets the SQL statement to rename a column in a table
     *
     * @param string $table The table name 
     * @param string $old The current column name
     * @param string $new The new column name
     * @return string
     */
    protected function _getRenameColumnSql( $table, $old, $new )
    {
    }
    
    /**
     * Gets the SQL statement to set a column's NULL status
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param boolean $null True for NULL, false for NOT NULL
     * @return string
     */
    protected function _getSetNullSql( $table, $column, $null=true )
    {
    }
    
    /**
     * Gets the SQL statement to set the data type of a column
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param Xyster_Db_Gateway_DataType $type The data type
     * @param mixed $argument An argument for the data type
     * @return string
     */
    protected function _getSetTypeSql( $table, $column, Xyster_Db_Gateway_DataType $type, $argument=null )
    {
    }
    
    /**
     * Translates a DataType enum into the correct SQL syntax
     *
     * @param Xyster_Db_Gateway_DataType $type
     * @param mixed $argument
     * @return string
     */
    protected function _translateType( Xyster_Db_Gateway_DataType $type, $argument=null )
    {
        return strtoupper($type->getName());
    }
}