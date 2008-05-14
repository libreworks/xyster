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
 * A gateway and abstraction layer for SQLite
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_Pdo_Sqlite extends Xyster_Db_Gateway_Abstract
{
    /**
     * Creates a new MySQL DB gateway
     *
     * @param Zend_Db_Adapter_Pdo_Sqlite $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Sqlite $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Sqlite $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Sqlite $db )
    {
        $this->_setAdapter($db);
    }
    
    /**
     * Drops a column
     *
     * SQLite does not support dropping columns
     * 
     * @param string $table The table name
     * @param string $column The column name
     * @param mixed $default The new column default value
     * @throws Xyster_Db_Gateway_Exception always
     */
    public function dropColumn( $table, $column )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support dropping columns');
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
        return array();
    }
    
    /**
     * Lists all indexes
     *
     * The return value is an associative array keyed by the index name,
     * as returned by the RDBMS.  For now, we cannot easily calculate the
     * primary key status in SQLite.
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
        $sql = "SELECT name, tbl_name FROM sqlite_master WHERE type = 'index'";
        $statement = $this->getAdapter()->fetchAll($sql);
        $indexes = array();
        $tables = array();
        foreach( $statement as $row ) {
            $tables[$row['tbl_name']] = $row['tbl_name'];
            $indexes[$row['name']] = array(
                'INDEX_NAME' => $row['name'],
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => $row['tbl_name'],
                'COLUMNS' => array(),
                'PRIMARY' => null,
                'UNIQUE' => null
            );
        }
        // loop through and get index info for each
        foreach( $indexes as $name => $info ) {
            $statement = $this->getAdapter()->fetchAll('PRAGMA index_info(' . $name . ')');
            foreach( $statement as $row ) {
                $indexes[$name]['COLUMNS'][] = $row['name'];
            }
        }
        foreach( $tables as $tableName ) {
            $statement = $this->getAdapter()->fetchAll('PRAGMA index_list(' . $tableName . ')');
            foreach( $statement as $row ) {
                $indexes[$row['name']]['UNIQUE'] = $row['unique'] != 0;
            }
        }
        return $indexes;
    }
    
    /**
     * Renames a column
     * 
     * SQLite doesn't support renaming columns
     *
     * @param string $table The table name
     * @param string $old The current column name
     * @param string $new The new column name
     * @throws Xyster_Db_Gateway_Exception always
     */
    public function renameColumn( $table, $old, $new )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support renaming columns');
    }
    
    /**
     * Renames an index
     *
     * @param string $old The current index name
     * @param string $new The new index name
     * @param string $table The table name (not required on all databases)
     */
    public function renameIndex( $old, $new, $table = null )
    {
        $indexes = $this->listIndexes();
        foreach( $indexes as $name => $info ) {
            if ( $name == $old && !strcasecmp($info['TABLE_NAME'], $table) ) {
                $this->createIndex($new)->on($table, $info['COLUMNS'])
                    ->unique($info['UNIQUE'])
                    ->execute();
                break;
            }
        }
        $this->dropIndex($old, $table);
    }
    
    /**
     * Sets the default value for a column
     *
     * SQLite does not support setting column default values
     * 
     * @param string $table The table name
     * @param string $column The column name
     * @param mixed $default The new column default value
     * @throws Xyster_Db_Gateway_Exception always
     */
    public function setDefault( $table, $column, $default )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support setting default values');
    }
    
    /**
     * Creates a unique index on a column or columns
     * 
     * SQLite doesn't allow you to add unique indexes in this way after the
     * fact; you should instead use the {@link createIndex} method.  
     *
     * @param string $table The table name
     * @param mixed $cols The string column name or an array of column names 
     */
    public function setUnique( $table, $cols )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support adding a unique index, use createIndex instead');
    }
        
    /**
     * Whether the database supports foreign keys
     *
     * @return boolean
     */
    public function supportsForeignKeys()
    {
        return false;
    }
    
    /**
     * Gets the SQL statement to add a primary key to a table
     *
     * SQLite doesn't support adding primary keys after table creation
     * 
     * @param string $table The table name 
     * @param array $columns The columns in the key
     * @return string
     * @throws Xyster_Db_Gateway_Exception
     */
    protected function _getAddPrimarySql( $table, array $columns )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support adding primary keys');
    }
               
    /**
     * Gets the SQL statement to create an index
     *
     * @param Xyster_Db_Gateway_IndexBuilder $builder The index builder
     * @return string
     */
    protected function _getCreateIndexSql( Xyster_Db_Gateway_IndexBuilder $builder )
    {
        $tableName = $this->_quote($builder->getTable());
        $sql = "CREATE " . ( ( $builder->isUnique() ) ? 'UNIQUE ' : '' ) .
            "INDEX " . $this->_quote($builder->getName()) . " ON " . $tableName . " ";
        $columns = array();
        foreach( $builder->getColumns() as $sort ) {
            /* @var $sort Xyster_Data_Sort */
            $columns[] = $this->_quote($sort->getField()->getName()) .
                $sort->getDirection();
        }
        return $sql . "( " . implode(', ', $columns) . " )";
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
        return 'DROP INDEX ' . $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to drop a primary key from a table
     *
     * Dropping a primary key is not supported by SQLite
     * 
     * @param string $table The table name
     * @param string $name The index name (not all dbs require this)
     * @return string
     * @throws Xyster_Db_Gateway_Exception always
     */
    protected function _getDropPrimarySql( $table, $name=null )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support dropping primary keys');
    }
    
    /**
     * Gets the SQL statement to list the sequences
     * 
     * @return string
     */
    protected function _getListSequencesSql()
    {
    }
    
    /**
     * Gets the SQL statement to rename an index
     * 
     * This method is left empty because in this class we overwrite the
     * {@link renameIndex} method in the abstract gateway, which calls this one.
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
        return 'ALTER TABLE ' . $this->_quote($old) . ' RENAME TO ' .
            $this->_quote($new);
    }
    
    /**
     * Gets the SQL statement to rename a column in a table
     *
     * SQLite does not support renaming of columns; an exception will always be
     * thrown.
     * 
     * @param string $table The table name 
     * @param string $old The current column name
     * @param string $new The new column name
     * @return string
     * @throws Xyster_Db_Gateway_Exception always
     */
    protected function _getRenameColumnSql( $table, $old, $new )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support renaming of columns');
    }
        
    /**
     * Gets the SQL statement to set a column's NULL status
     *
     * SQLite does not support altering the nullability of a column.
     * 
     * @param string $table The table name
     * @param string $column The column name
     * @param boolean $null True for NULL, false for NOT NULL
     * @return string
     * @throws Xyster_Db_Gateway_Exception
     */
    protected function _getSetNullSql( $table, $column, $null=true )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support altering nullability');
    }
    
    /**
     * Gets the SQL statement to set the data type of a column
     *
     * SQLite does not support altering the type of a column
     * 
     * @param string $table The table name
     * @param string $column The column name
     * @param Xyster_Db_DataType $type The data type
     * @param mixed $argument An argument for the data type
     * @return string
     * @throws Xyster_Db_Gateway_Exception always
     */
    protected function _getSetTypeSql( $table, $column, Xyster_Db_DataType $type, $argument=null )
    {
        require_once 'Xyster/Db/Gateway/Exception.php';
        throw new Xyster_Db_Gateway_Exception('SQLite does not support altering column type');
    }
    
    /**
     * Translates a DataType enum into the correct SQL syntax
     * 
     * SQLite has very interesting data type implemetation.  As such, we have
     * tried to convert the expected SQL:2003 data types into SQLite's type
     * affinities.  Date times are part of the TEXT type, boolean part of
     * INTEGER.  
     *
     * @param Xyster_Db_DataType $type
     * @param mixed $argument
     * @return string
     */
    protected function _translateType( Xyster_Db_DataType $type, $argument=null )
    {
        $sql = '';
        if ( $type === Xyster_Db_DataType::Blob() ||
            $type === Xyster_Db_DataType::Clob() ) {
            $sql = 'BLOB';
        } else if ( $type === Xyster_Db_DataType::Char() ||
            $type === Xyster_Db_DataType::Varchar() ||
            $type === Xyster_Db_DataType::Date() ||
            $type === Xyster_Db_DataType::Time() ||
            $type === Xyster_Db_DataType::Timestamp() ) {
            $sql = 'TEXT';
        } else if ( $type === Xyster_Db_DataType::Float() ) {
            $sql = 'REAL';
        } else if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'INTEGER PRIMARY KEY AUTOINCREMENT';
        } else if ( $type === Xyster_Db_DataType::Boolean() ||
            $type === Xyster_Db_DataType::Integer() ||
            $type === Xyster_Db_DataType::Smallint() ) {
            $sql = 'INTEGER';
        }
        return $sql;        
    }
}