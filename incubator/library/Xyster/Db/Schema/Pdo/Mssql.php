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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_Schema_Abstract
 */
require_once 'Xyster/Db/Schema/Abstract.php';
/**
 * An abstraction layer for schema manipulation in MS SQL Server 
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Schema_Pdo_Pgsql extends Xyster_Db_Schema_Abstract
{
    /**
     * Creates a new SQL Server schema adapter
     *
     * @param Zend_Db_Adapter_Pdo_Mssql $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Mssql $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Mssql $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Mssql $db )
    {
        $this->_setAdapter($db);
    }
    
    /**
     * Creates a new index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createIndex( Xyster_Db_Index $index )
    {
        // They added the DDL for a fulltext index starting in SQL Server 9 (aka 2005)
        $fulltext = intval(substr($this->_getVersion(),0,1)) > 8;
        if ( $index->isFulltext() ) {
            throw new Zend_Db_Exception('Fulltext index not yet implemented');
            // $sql = 'CREATE FULLTEXT INDEX ON ';
        } else {
            $tableName = $this->_tableName($index->getTable());
            $sql = "CREATE INDEX " . $this->_quote($index->getName()) . " ON " .
            $tableName . " ";
            $columns = array();
            foreach( $index->getSortedColumns() as $sort ) {
                /* @var $sort Xyster_Data_Sort */
                $columns[] = $this->_quote($sort->getField()->getName()) . ' ' .
                    $sort->getDirection();
            }
            $sql .= "( " . implode(', ', $columns) . " )";
            $this->getAdapter()->query($sql);
        }
    }

    /**
     * Drops an index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropIndex( Xyster_Db_Index $index )
    {
        // DROP INDEX changed in in SQL Server 9 (aka 2005)
        $oldSyntax = intval(substr($this->_getVersion(),0,1)) < 9;
        $sql = "DROP INDEX ";
        if ( $oldSyntax ) {
            // also, no schemas prior to 2000
            $sql .= $this->_quote($index->getTable()->getName()) . '.';
            $sql .= $this->_quote($index->getName());
        } else {
            $sql .= $this->_quote($index->getName()) . ' ON ';
            $sql .= $this->_tableName($index->getTable());
        }
        $this->getAdapter()->query($sql);        
    }
    
    /**
     * Gets all foreign keys (optionally in a table and/or schema)
     * 
     * The method will return an array of {@link Xyster_Db_ForeignKey} objects.
     * 
     * If the table parameter is specified, this method should return all 
     * foreign keys for the table given.
     * 
     * If the schema is specified but not the table, the method will return the
     * foreign keys for all tables in the schema.
     * 
     * If both are specified, the method will return the foreign keys for the
     * table in the given schema.
     * 
     * If neither are specified, the list should return all foreign keys in the
     * database. 
     *
     * @param string $table Optional. The table name
     * @param string $schema Optional. The schema name
     * @return array of {@link Xyster_Db_ForeignKey} objects
     */
    public function getForeignKeys( $table = null, $schema = null )
    {
        $config = $this->getAdapter()->getConfig();
        
        $sql = "SELECT c.constraint_name as keyname, " .
                "c.table_name as tablename, k.column_name as colname, " . 
                "k.referenced_table_name as reftablename, " .
                "k.referenced_column_name as refcolname ".
            	", c.update_rule as onupdate, c.delete_rule as ondelete ";
        $sql .= ' FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS c ' .
            ' INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k ON ' . 
            'c.constraint_schema = k.constraint_schema ' .
            'AND c.constraint_name = k.constraint_name ' .
            'AND c.table_name = k.table_name ';
        $sql .= " and c.constraint_type = 'FOREIGN KEY'";
        if ( $table !== null ) {
            $sql .= " AND c.table_name = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $fks = array();
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename']);
            $refTable = $this->_getLazyTable($row['reftablename']);
            
            $name = $row['keyname'];
            if ( !array_key_exists($name, $fks) ) {
                $fk = new Xyster_Db_ForeignKey;
                $fk->setReferencedTable($refTable)
                    ->setTable($table)
                    ->setName($row['keyname']);
                if ( isset($row['ondelete']) && $row['ondelete'] ) {
                    $fk->setOnDelete(Xyster_Db_ReferentialAction::fromSql($row['ondelete']));
                }
                if ( isset($row['onupdate']) && $row['onupdate'] ) {
                    $fk->setOnUpdate(Xyster_Db_ReferentialAction::fromSql($row['onupdate']));
                }
                $fks[$name] = $fk;
            }
            foreach( $table->getColumns() as $column ) {
                if ( $column->getName() == $row['colname'] ) {
                    $fks[$name]->addColumn($column);
                }
            }
            foreach( $refTable->getColumns() as $column ) {
                if ( $column->getName() == $row['refcolname'] ) {
                    $fks[$name]->addReferencedColumn($column);
                }
            }
        }

        return $fks;
    }
    
    /**
     * Gets all indexes (optionally for a table and/or schema)
     * 
     * The method will return an array of {@link Xyster_Db_Index} objects.  It
     * should NOT return unique keys or primary keys, even if the RDBMS lists
     * them as indexes.
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
        
    }
    
    /**
     * Gets the primary keys for a specific table if one exists 
     *
     * @param string $table The table name
     * @param string $schema Optional. The schema name
     * @return Xyster_Db_PrimaryKey or null if one doesn't exist
     */
    public function getPrimaryKey( $table, $schema = null )
    {
        
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
        $sql = "EXEC sp_rename ?, ?, ?";
        $columnName = $table->getName() . '.' . $column->getName();
        if ( $table->getSchema() ) {
            $columnName = $table->getSchema() . '.' . $columnName;
        }
        $this->getAdapter()->query($sql, array($columnName, $newName, 'COLUMN'));
        $column->setName($newName);
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
        $sql = "EXEC sp_rename ?, ?, ?";
        $indexName = $table->getName() . '.' . $index->getName();
        if ( $index->getTable()->getSchema() ) {
            $indexName = $index->getTable()->getSchema() . '.' . $indexName;
        }
        $this->getAdapter()->query($sql, array($indexName, $newName, 'INDEX'));
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
        $sql = "EXEC sp_rename ?, ?";
        $tableName = $table->getName();
        if ( $table->getSchema() ) {
            $tableName = $table->getSchema() . '.' . $tableName;
        }
        $this->getAdapter()->query($sql, array($tableName, $newName));
        $table->setName($newName);
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
        
    }
    
    /**
     * Converts the database-reported data type into a DataType enum
     *
     * @param string $sqlType The database data type
     * @return Xyster_Db_DataType
     */
    public function toDataType( $sqlType )
    {
        if ( !strcasecmp($sqlType, 'BIT') ) {
            return Xyster_Db_DataType::Boolean();
        }
        return parent::toDataType($sqlType);
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
        if ( $type === Xyster_Db_DataType::Blob() ) {
            $sql = 'IMAGE';
        } else if ( $type === Xyster_Db_DataType::Boolean() ) {
            $sql = 'BIT';
        } else if ( $type === Xyster_Db_DataType::Clob() ) {
            $sql = 'NTEXT';
        } else if ( $type === Xyster_Db_DataType::Char() || $type === Xyster_Db_DataType::Varchar() ) {
            $sql = 'N' . strtoupper($type->getName()) . '(' . $column->getLength() . ')';
        } else if ( $type === Xyster_Db_DataType::Timestamp() ) {
            $sql = 'DATETIME';
        } else if ( $type === Xyster_Db_DataType::Float() ) {
            $sql = 'DOUBLE PRECISION';
        } else if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'INT NOT NULL PRIMARY KEY IDENTITY';
        } else {
            $sql = parent::toSqlType($column); 
        }
        return $sql;        
    }
    
    /**
     * Gets the version information of SQL Server
     *
     * @return mixed
     */
    private function _getVersion()
    {
        return $this->getAdapter()->fetchOne("SELECT SERVERPROPERTY('productversion')");
    }
}