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
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_Gateway_Abstract
 */
require_once 'Xyster/Db/Gateway/Abstract.php';
/**
 * A gateway and abstraction layer for MySQL
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_Pdo_Mysql extends Xyster_Db_Gateway_Abstract
{
    /**
     * Creates a new MySQL DB gateway
     *
     * @param Zend_Db_Adapter_Pdo_Mysql $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Mysql $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Mysql $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Mysql $db )
    {
    	$this->_setAdapter($db);
    }
    
    /**
     * Gets the SQL statement to create an index
     * 
     * If the DBMS doesn't support FULLTEXT indexes, it's safe to ignore the
     * setting (an exception doesn't need to be thrown).
     *
     * @param string $name The name of the index
     * @param string $table The table name 
     * @param array $columns The columns in the index
     * @param boolean $fulltext Whether the index should be fulltext
     * @return string
     */
    protected function _getCreateIndexSql( $name, $table, array $columns, $fulltext=false )
    {
    	return "CREATE " . ( $fulltext ? 'FULLTEXT ' : '') . "INDEX " . 
    	   $this->_quote($name) . " ON " . $this->_quote($table) . " " . 
    	   $this->_quote($columns);
    }
    
    /**
     * Gets the SQL to create a table
     *
     * @param Xyster_Db_Gateway_TableBuilder $builder
     * @return string
     */
    protected function _getCreateTableSql( Xyster_Db_Gateway_TableBuilder $builder )
    {
    	$sql = parent::_getCreateTableSql($builder);
    	$create = substr($sql, 0, -1);
    	foreach( $builder->getIndexes() as $index ) {
    	    /* @var $index Xyster_Db_Gateway_TableBuilder_Index */
    	    $create .= ",\n";
    	    if ( $index->isFulltext() ) {
    	        $create .= "FULLTEXT ";
    	    }
    	    $create .= "INDEX " . $this->_quote($index->getColumns());
    	}
    	return $create . ')';
    }
    
    /**
     * Gets the SQL statement to drop a foreign key from a table
     *
     * MySQL Syntax for removal of a foreign key is different from SQL-92
     * 
     * @param string $table The table name
     * @param string $name The key name
     * @return string
     */
    protected function _getDropForeignSql( $table, $name )
    {
    	return "ALTER TABLE " . $this->_quote($table) . " DROP FOREIGN KEY " .
           $this->_quote($name);
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
    	return "ALTER TABLE " . $this->_quote($table) . " DROP INDEX " .
    	   $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to drop a primary key from a table
     *
     * @param string $table The table name
     * @return string
     */
    protected function _getDropPrimarySql( $table )
    {
    	return "ALTER TABLE " . $this->_quote($table) . " DROP PRIMARY KEY";
    }
    
    /**
     * Gets the SQL statement to list the indexes
     *
     * @return string
     */
    protected function _getListIndexesSql()
    {
        $config = $this->getAdapter()->getConfig();
        return "SELECT * FROM INFORMATION_SCHEMA.STATISTICS " . 
            "WHERE index_schema = '" . $config['dbname'] . "'";    	
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
     * @todo implement getRenameIndexSql method
     */
    protected function _getRenameIndexSql( $old, $new, $table=null )
    {
    	// possibly drop/create ?
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
    	return "RENAME TABLE " . $this->getAdapter()->quoteIdentifier($old) .
            " TO " . $this->getAdapter()->quoteIdentifier($new);
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
        $sql = "ALTER TABLE " . $this->_quote($table) . " CHANGE COLUMN " . 
           $this->_quote($old) . ' ' . $this->_quote($new);
        $tableInfo = $this->getAdapter()->describeTable($table);
        $columnInfo = $tableInfo[$old];
        $sql .= ' ' . $columnInfo['DATA_TYPE'];
        if ( $columnInfo['LENGTH'] ) {
            $sql .= "(" . $columnInfo['LENGTH'] . ")"; 
        }
        $sql .= ( $columnInfo['NULLABLE'] ) ? ' NULL' : ' NOT NULL';
        if ( $columnInfo['DEFAULT'] !== null ) {
            $sql .= " DEFAULT " . $this->getAdapter()->quote($columnInfo['DEFAULT']);
        }
        return $sql;
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
    	$sql = "ALTER TABLE " . $this->_quote($table) . " MODIFY COLUMN " . 
    	   $this->_quote($column) . ' ';
    	$tableInfo = $this->getAdapter()->describeTable($table);
    	$columnInfo = $tableInfo[$column];
    	$sql .= $columnInfo['DATA_TYPE'];
    	if ( $columnInfo['LENGTH'] ) {
    	    $sql .= "(" . $columnInfo['LENGTH'] . ")"; 
    	}
    	$sql .= ( $null ) ? ' NULL' : ' NOT NULL';
        if ( $columnInfo['DEFAULT'] !== null ) {
            $sql .= " DEFAULT " . $this->getAdapter()->quote($columnInfo['DEFAULT']); 
        }
    	return $sql;
    }
    
    /**
     * Gets the SQL statement to create a UNIQUE index for one or more columns
     *
     * It might be a better idea to use the "CREATE UNIQUE INDEX" syntax
     * 
     * @param string $table The table name
     * @param array $columns The columns in the unique index
     * @return string
     */
    protected function _getSetUniqueSql( $table, array $columns )
    {
        return "CREATE UNIQUE INDEX " . $this->_quote($name) . " ON " .
            $this->_quote($table) . " " . $this->_quote($columns);
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
    	$sql = "ALTER TABLE " . $this->_quote($table) . " MODIFY COLUMN " . 
    	   $this->_quote($column) . " " . $this->_translateType($type, $argument);
        $tableInfo = $this->getAdapter()->describeTable($table);
        $columnInfo = $tableInfo[$column]; 
        $sql .= ( $columnInfo['NULLABLE'] ) ? ' NULL' : ' NOT NULL';
        if ( $columnInfo['DEFAULT'] !== null ) {
            $sql .= " DEFAULT " . $this->getAdapter()->quote($columnInfo['DEFAULT']); 
        }
        return $sql;
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
        $sql = '';
        if ( $type === Xyster_Db_Gateway_DataType::Blob() ) {
            $sql = 'BLOB';
        } else if ( $type === Xyster_Db_Gateway_DataType::Boolean() ) {
            $sql = 'BOOLEAN';
        } else if ( $type === Xyster_Db_Gateway_DataType::Char()
            || $type === Xyster_Db_Gateway_DataType::Varchar() ) {
            $sql = strtoupper($type->getName()) . '(' . intval($argument) . ')';
        } else if ( $type === Xyster_Db_Gateway_DataType::Clob() ) {
            $sql = 'TEXT';
        } else if ( $type === Xyster_Db_Gateway_DataType::Date() ) {
            $sql = 'DATE';
        } else if ( $type === Xyster_Db_Gateway_DataType::Float() ) {
            $sql = 'FLOAT';
        } else if ( $type === Xyster_Db_Gateway_DataType::Identity() ) {
            $sql = 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT';
        } else if ( $type === Xyster_Db_Gateway_DataType::Integer() ) {
            $sql = 'INT';
        } else if ( $type === Xyster_Db_Gateway_DataType::Smallint() ) {
            $sql = 'SMALLINT';
        } else if ( $type === Xyster_Db_Gateway_DataType::Time() ) {
            $sql = 'TIME';
        } else if ( $type === Xyster_Db_Gateway_DataType::Timestamp() ) {
            $sql = 'DATETIME';
        }
        return $sql;
    }
}