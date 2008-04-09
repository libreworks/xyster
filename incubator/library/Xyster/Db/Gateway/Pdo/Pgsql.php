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
class Xyster_Db_Gateway_Pdo_Pgsql extends Xyster_Db_Gateway_Abstract
{
    /**
     * Creates a new MySQL DB gateway
     *
     * @param Zend_Db_Adapter_Pdo_Pgsql $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Pgsql $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Pgsql $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Pgsql $db )
    {
    	$this->_setAdapter($db);
    }
    
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
        $config = $this->getAdapter()->getConfig();
        $sql = "SELECT r.constraint_schema, r.constraint_name, k.table_name, " .
            " k.column_name, kr.table_name as referenced_table_name, " .
            " kr.column_name as referenced_column_name, r.update_rule, " .
            "r.delete_rule from information_schema.referential_constraints r " .
            " inner join information_schema.key_column_usage k on " .
            " r.constraint_name = k.constraint_name and " . 
            " r.constraint_schema = k.constraint_schema inner join " .
            " information_schema.key_column_usage kr on " .
            " r.unique_constraint_catalog = kr.constraint_catalog and " . 
            " r.unique_constraint_schema = kr.constraint_schema and " . 
            " r.unique_constraint_name = kr.constraint_name where " . 
            " r.constraint_catalog = '" . $config['dbname'] . "'";
        $statement = $this->getAdapter()->fetchAll($sql);
        $fks = array();
        foreach( $statement as $row ) {
            $name = $row['constraint_name'];
            if ( array_key_exists($name, $fks) ) {
                $fks[$name]['COLUMNS'][] = $row['column_name'];
                $fks[$name]['REFERENCED_COLUMNS'][] = $row['referenced_column_name'];
            } else {
                $fks[$name] = array(
                    'KEY_NAME' => $row['constraint_name'],
                    'SCHEMA_NAME' => null,
                    'TABLE_NAME' => $row['table_name'],
                    'COLUMNS' => array($row['column_name']),
                    'REFERENCED_TABLE_NAME' => $row['referenced_table_name'],
                    'REFERENCED_COLUMNS' => array($row['referenced_column_name']),
                    'ON_UPDATE' => $row['update_rule'],
                    'ON_DELETE' => $row['delete_rule']
                );
            }
        }
        return $fks;
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
     * TABLE_NAME  => string;
     * COLUMNS     => array; An array of the column names
     * PRIMARY     => boolean; true if the index is a primary key
     * UNIQUE      => boolean; true if the index is a unique key
     *
     * @return array
     */
    public function listIndexes()
    {
        $sql = 'select c.relname as "indexname", c2.relname as "tablename", ' . 
            't.schemaname as "schemaname", a.attname as "colname", i.indisprimary, i.indisunique ' . 
            'from pg_catalog.pg_index i ' . 
            'inner join pg_catalog.pg_class c on i.indexrelid = c.oid ' . 
            'inner join pg_catalog.pg_class c2 on i.indrelid = c2.oid ' . 
            'inner join pg_catalog.pg_tables t on c2.relname = t.tablename ' .
            'inner join pg_catalog.pg_attribute a on c2.oid = a.attrelid and a.attnum = any(i.indkey) ' .
            "where t.schemaname <> 'pg_catalog' " . 
            'order by indexname';
        $statement = $this->getAdapter()->fetchAll($sql);
        $indexes = array();
        foreach( $statement as $row ) {
            if ( array_key_exists($row['indexname'], $indexes) ) {
                $indexes[$row['indexname']]['COLUMNS'][] = $row['colname'];
            } else {
                $indexes[$row['indexname']] = array(
                    'INDEX_NAME' => $row['indexname'],
                    'SCHEMA_NAME' => $row['schemaname'],
                    'TABLE_NAME' => $row['tablename'],
                    'COLUMNS' => array($row['colname']),
                    'PRIMARY' => $row['indisprimary'] == 't',
                    'UNIQUE' => $row['indisunique'] == 't'
                );
            }
        }
        return $indexes;
    }
    
    /**
     * Whether the database supports sequences
     *
     * @return boolean
     */
    public function supportsSequences()
    {
        return true;
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
    	return "CREATE INDEX " . $this->_quote($name) . " ON " .
    	   $this->_quote($table) . " " . $this->_quote($columns);
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
    	return "DROP INDEX " . $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to drop a primary key from a table
     *
     * @param string $table The table name
     * @param string $name The index name (not all dbs require this)
     * @return string
     */
    protected function _getDropPrimarySql( $table, $name=null )
    {
    	return "ALTER TABLE " . $this->_quote($table) . " DROP CONSTRAINT " .
    	   $this->_quote($name);
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
        return "SELECT " . $this->_quote('relname') . ' FROM ' .
            $this->_quote('pg_catalog') . '.' .
            $this->_quote('pg_statio_all_sequences'); 
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
    	return "ALTER INDEX " . $this->_quote($old) . " RENAME TO " .
    	   $this->_quote($new);
    }
    
    /**
     * Gets the SQL statement to rename a sequence
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     * @return string
     */
    protected function _getRenameSequenceSql( $old, $new )
    {
        return "ALTER TABLE " . $this->_quote($old) . " RENAME TO " .
            $this->_quote($new);
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
    	return "ALTER TABLE " . $this->getAdapter()->quoteIdentifier($old) .
            " RENAME TO " . $this->getAdapter()->quoteIdentifier($new);
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
        return "ALTER TABLE " . $this->_quote($table) . " RENAME COLUMN " . 
           $this->_quote($old) . ' TO ' . $this->_quote($new);
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
    	$sql = "ALTER TABLE " . $this->_quote($table) . " ALTER COLUMN " . 
    	   $this->_quote($column) . ' ';
    	$sql .= ( $null ) ? ' DROP NOT NULL' : ' SET NOT NULL';
    	return $sql;
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
    	return "ALTER TABLE " . $this->_quote($table) . " ALTER COLUMN " . 
    	   $this->_quote($column) . " TYPE " .
    	   $this->_translateType($type, $argument);
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
            $sql = 'BYTEA';
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
            $sql = 'SERIAL PRIMARY KEY';
        } else if ( $type === Xyster_Db_Gateway_DataType::Integer() ) {
            $sql = 'INTEGER';
        } else if ( $type === Xyster_Db_Gateway_DataType::Smallint() ) {
            $sql = 'SMALLINT';
        } else if ( $type === Xyster_Db_Gateway_DataType::Time() ) {
            $sql = 'TIME';
        } else if ( $type === Xyster_Db_Gateway_DataType::Timestamp() ) {
            $sql = 'TIMESTAMP';
        }
        return $sql;
    }
}