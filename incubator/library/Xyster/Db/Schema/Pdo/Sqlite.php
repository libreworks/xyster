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
 * @see Xyster_Db_Schema_Abstract
 */
require_once 'Xyster/Db/Schema/Abstract.php';
/**
 * An abstraction layer for schema manipulation in SQLite
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Schema_Pdo_Sqlite extends Xyster_Db_Schema_Abstract
{
    /**
     * Creates a new SQLite schema adapter
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
     * Creates a primary key
     *
     * @param Xyster_Db_PrimaryKey $pk
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addPrimaryKey( Xyster_Db_PrimaryKey $pk )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support adding primary keys');
    }
    
    /**
     * Adds a unique key
     * 
     * @param Xyster_Db_UniqueKey $uk
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addUniqueKey( Xyster_Db_UniqueKey $uk )
    {
        $sql = "CREATE UNIQUE INDEX " . $this->_quote($uk->getName()) . ' ON ' .
            $this->_tableName($uk->getTable()) . ' ' . 
            $this->_quote($uk->getColumns());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Creates a new index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createIndex( Xyster_Db_Index $index )
    {
        $sql = "CREATE INDEX " . $this->_quote($index->getName()) . " ON " .
            $this->_tableName($index->getTable()) . " ";
        $columns = array();
        foreach( $index->getSortedColumns() as $sort ) {
            /* @var $sort Xyster_Data_Sort */
            $columns[] = $this->_quote($sort->getField()->getName()) . ' ' .
                $sort->getDirection();
        }
        $sql .= "( " . implode(', ', $columns) . " )";
        $this->getAdapter()->query($sql);
    }

    /**
     * Drop a column
     *
     * @param Xyster_Db_Column $column
     * @param Xyster_Db_Table $table The table
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropColumn( Xyster_Db_Column $column, Xyster_Db_Table $table )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support dropping columns');
    }
    
    /**
     * Drops an index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropIndex( Xyster_Db_Index $index )
    {
        $sql = 'DROP INDEX ' . $this->_quote($index->getName());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Drops the primary key constraint from a table
     *
     * @param Xyster_Db_Table $table The table whose key to drop
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropPrimary( Xyster_Db_PrimaryKey $pk )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support dropping primary keys');
    }
        
    /**
     * Gets all foreign keys (optionally in a table and/or schema)
     * 
     * SQLite doesn't support foreign keys
     *
     * @param string $table Optional. The table name
     * @param string $schema Optional. The schema name
     * @return array of {@link Xyster_Db_ForeignKey} objects
     */
    public function getForeignKeys( $table = null, $schema = null )
    {
        $this->_checkForeignKeySupport();
    }
    
    /**
     * Gets all indexes (optionally for a table and/or schema)
     * 
     * The method will return an array of {@link Xyster_Db_Index} objects.  It
     * will NOT return unique keys or primary keys.
     * 
     * If the table parameter is specified, this method should return all 
     * indexes for the table given.
     * 
     * If the schema is specified but not the table, the method will return the
     * indexes for all tables in the schema.
     * 
     * If both are specified, the method will return the indexes for the table
     * in the given schema.
     * 
     * If neither are specified, the list should return all indexes in the
     * database. 
     *
     * @param string $table Optional. The table name
     * @param string $schema Optional. The schema name
     * @return array of {@link Xyster_Db_Index} objects
     */
    public function getIndexes( $table = null, $schema = null )
    {
        $indexes = array();
        
        $sql = "select name, tbl_name from sqlite_master where type = 'index'" .
             ' and "sql" is not null and "sql" not like \'%unique%\''; 
        if ( $table !== null ) {
            $sql .= " and tbl_name = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tbl_name']);
            
            $index = new Xyster_Db_Index;
            $index->setTable($table)
                ->setName($row['name']);
            $info = $this->getAdapter()->fetchAll('PRAGMA index_info(' . $row['name'] .')');
            $cols = array();
            foreach( $info as $infoRow ) {
                $cols[] = $infoRow['name'];
            }
            foreach( $table->getColumns() as $tcolumn ) {
                if ( in_array($tcolumn->getName(), $cols) ) {
                    $index->addColumn($tcolumn);
                    break;
                }
            }
            $indexes[] = $index;
        }
        return $indexes;
    }
    
    /**
     * Gets the primary key for a specific table if one exists 
     *
     * @param string $table The table name
     * @param string $schema Optional. The schema name
     * @return Xyster_Db_PrimaryKey or null if one doesn't exist
     */
    public function getPrimaryKey( $table, $schema = null )
    {
        $statement = $this->getAdapter()->describeTable($table);
        $columns = array();
        foreach( $statement as $row ) {
            if ( $row['PRIMARY'] ) {
                $columns[] = $row['COLUMN_NAME'];
            }
        }
        
        $primary = null;
        
        if ( count($columns) ) {
            $table = new Xyster_Db_Table_Lazy($this, $table);
            $primary = new Xyster_Db_PrimaryKey;
            $primary->setTable($table);
            foreach( $table->getColumns() as $column ) {
                if ( in_array($column->getName(), $columns) ) {
                    $primary->addColumn($column);
                }
            }
        }
        
        return $primary;
    }
    
    /**
     * Gets the unique keys for a specific table (and optionally schema)
     *
     * @param string $table The table name
     * @param string $schema
     * @return array of {@link Xyster_Db_UniqueKey} objects
     */
    public function getUniqueKeys( $table, $schema = null )
    {
        $uniques = array();
        
        $sql = "select name, tbl_name from sqlite_master where type = 'index'" .
             ' and "sql" is not null and "sql" like \'%unique%\''; 
        if ( $table !== null ) {
            $sql .= " and tbl_name = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tbl_name']);
            
            $unique = new Xyster_Db_UniqueKey;
            $unique->setTable($table)
                ->setName($row['name']);
            $info = $this->getAdapter()->fetchAll('PRAGMA index_info(' . $row['name'] .')');
            $cols = array();
            foreach( $info as $infoRow ) {
                $cols[] = $infoRow['name'];
            }
            foreach( $table->getColumns() as $tcolumn ) {
                if ( in_array($tcolumn->getName(), $cols) ) {
                    $unique->addColumn($tcolumn);
                    break;
                }
            }
            $uniques[] = $unique;
        }
        return $uniques;
    }
    
    /**
     * Renames a column
     * 
     * This method will call the {@link Xyster_Db_Column::setName} method with
     * the new name.  There is no need to call it separately.
     *
     * @param Xyster_Db_Column $column The column to rename
     * @param string $newName The new index name
     * @param Xyster_Db_Table $table The table
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameColumn( Xyster_Db_Column $column, $newName, Xyster_Db_Table $table )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support renaming of columns');
    }
    
    /**
     * Renames an index
     * 
     * This method will call the {@link Xyster_Db_Index::setName} method with
     * the new name.  There is no need to call it separately.
     *
     * @param Xyster_Db_Index $index The index to rename
     * @param string $newName The new index name
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameIndex( Xyster_Db_Index $index, $newName )
    {
        $indexes = $this->getIndexes($index->getTable()->getName(), $index->getTable()->getSchema());
        foreach( $indexes as $info ) {
            if ( $info->getName() == $index->getName() ) {
                $new = clone $index;
                $this->createIndex($new->setName($newName));
                break;
            }
        }
        $this->dropIndex($index);
        $index->setName($newName);
    }
        
    /**
     * Renames a table
     * 
     * This method will call the {@link Xyster_Db_Table::setName} method with
     * the new name.  There is no need to call it separately.
     *
     * @param Xyster_Db_Table $table The table to rename
     * @param string $newName Its new name
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameTable( Xyster_Db_Table $table, $newName )
    {
        $sql = "ALTER TABLE " . $this->_tableName($table) .
            " RENAME TO " . $this->_quote($newName);
        $this->getAdapter()->query($sql);
        $table->setName($newName);
    }
 
    /**
     * Sets the default value for a column
     * 
     * This method will call the {@link Xyster_Db_Column::setDefaultValue}
     * method.  There is no need to call this method separately.
     *
     * @param Xyster_Db_Column $column
     * @param mixed $default The new default value
     * @param Xyster_Db_Table $table The table
     * @throws Zend_Db_Exception if an error occurs
     */
    public function setDefaultValue( Xyster_Db_Column $column, $default, Xyster_Db_Table $table )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support setting default values');
    }
        
    /**
     * Sets whether or not the column will accept null
     * 
     * This method will call the {@link Xyster_Db_Column::setNull} method.
     * There is no need to call this method separately.
     *
     * @param Xyster_Db_Column $column
     * @param Xyster_Db_Table $table The table
     * @param boolean $null Optional. Defaults to true.
     * @throws Zend_Db_Exception if an error occurs
     */
    public function setNull( Xyster_Db_Column $column, Xyster_Db_Table $table, $null = true )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support altering nullability');
    }
    
    /**
     * Changes the type of a column
     * 
     * This method will call the {@link Xyster_Db_Column::setType} method (as
     * well as the setLength, setPrecision, and setScale methods if appropriate)
     * so there is no need to call these separately.
     *
     * @param Xyster_Db_Column $column
     * @param Xyster_Db_Table $table The table
     * @param Xyster_Db_DataType $type
     * @param int $length Optional. A length parameter
     * @param int $precision Optional. A precision parameter
     * @param int $scale Optional. A scale parameter 
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function setType( Xyster_Db_Column $column, Xyster_Db_Table $table, Xyster_Db_DataType $type, $length=null, $precision=null, $scale=null )
    {
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('SQLite does not support altering column type');
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
     * Translates a column into the SQL syntax for its data type
     * 
     * For example, a column with the VARCHAR type and a length of 255 would 
     * return "VARCHAR(255)". 
     *
     * @param Xyster_Db_Column $column
     * @return string
     */
    public function toSqlType( Xyster_Db_Column $column )
    {
        $type = $column->getType();
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
        } else if ( $type === Xyster_Db_DataType::Float() ||
            $type === Xyster_Db_DataType::Real() ||
            $type === Xyster_Db_DataType::Decimal() ) {
            $sql = 'REAL';
        } else if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'INTEGER PRIMARY KEY AUTOINCREMENT';
        } else if ( $type === Xyster_Db_DataType::Boolean() ||
            $type === Xyster_Db_DataType::Integer() ||
            $type === Xyster_Db_DataType::Smallint() || 
            $type === Xyster_Db_DataType::Bigint() ) {
            $sql = 'INTEGER';
        }
        return $sql;
    }
}