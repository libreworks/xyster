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
 * An abstraction layer for schema manipulation in MySQL 
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_Schema_AbstractMysql extends Xyster_Db_Schema_Abstract
{
    /**
     * Creates a new index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createIndex( Xyster_Db_Index $index )
    {
        $sql = "CREATE ";
        if ( $index->isFulltext() ) {
            $sql .= 'FULLTEXT ';
        }
        $sql .= "INDEX " . $this->_quote($index->getName()) . " ON " .
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
     * Drops a foreign key constraint
     *
     * @param Xyster_Db_ForeignKey $fk
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropForeign( Xyster_Db_ForeignKey $fk )
    {
        $sql = "ALTER TABLE " . $this->_tableName($fk->getTable()) .
            " DROP FOREIGN KEY " . $this->_quote($fk->getName());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Drops an index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropIndex( Xyster_Db_Index $index )
    {
        $sql = "ALTER TABLE " . $this->_tableName($index->getTable()) .
            " DROP INDEX " . $this->_quote($index->getName());
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
        $sql = 'ALTER TABLE ' . $this->_tableName($pk->getTable()) .
            ' DROP PRIMARY KEY';
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
        $version = $this->_getVersion();
        // MySQL 5.1.10+ has support the REFERENTIAL_CONSTRAINTS table
        $hasRefConstr = $version >= '5.1.10';
        
        $sql = "SELECT c.constraint_name as keyname, " .
                "c.table_name as tablename, k.column_name as colname, " . 
                "k.referenced_table_name as reftablename, " .
                "k.referenced_column_name as refcolname ";
        if ( $hasRefConstr ) {
            $sql .= ", c.update_rule as onupdate, c.delete_rule as ondelete ";
        }
        $tableName = $hasRefConstr ? 'REFERENTIAL_CONSTRAINTS' : 'TABLE_CONSTRAINTS';
        $sql .= ' FROM INFORMATION_SCHEMA.' . $tableName . ' c ' .
            ' INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k ON ' . 
            'c.constraint_schema = k.constraint_schema ' .
            'AND c.constraint_name = k.constraint_name ' .
            'AND c.table_name = k.table_name ' .
            "WHERE c.constraint_schema = '" . $config['dbname'] . "' ";
        if ( !$hasRefConstr ) {
            $sql .= " and c.constraint_type = 'FOREIGN KEY'";
        }
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
        $config = $this->getAdapter()->getConfig();
        $sql = "SELECT TABLE_NAME as tablename, INDEX_NAME as indexname, " . 
            "COLUMN_NAME as colname, NON_UNIQUE as nonunique, " . 
            "INDEX_TYPE as indextype " . 
            "FROM INFORMATION_SCHEMA.STATISTICS " . 
            "WHERE NON_UNIQUE = 1 and index_schema = '" . $config['dbname'] . "'";
        if ( $table !== null ) {
            $sql .= " and TABLE_NAME = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $indexes = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename']);
     
            $key = $row['indexname'];
            
            $column = null;
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $column = $tcolumn;
                    break;
                }
            }

            if ( array_key_exists($key, $indexes) ) {
                $indexes[$key]->addColumn($column);
            } else {
                $index = new Xyster_Db_Index;
                $index->setTable($table)
                    ->setFulltext($row['indextype'] == 'FULLTEXT')
                    ->setName($row['indexname'])
                    ->addColumn($column);
                $indexes[$key] = $index;
            }
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
        $config = $this->getAdapter()->getConfig();
        $sql = "SELECT TABLE_NAME as tablename, INDEX_NAME as indexname, " . 
            "COLUMN_NAME as colname, NON_UNIQUE as nonunique, " . 
            "INDEX_TYPE as indextype " . 
            "FROM INFORMATION_SCHEMA.STATISTICS " . 
            "WHERE INDEX_NAME = 'PRIMARY' and " . 
            "index_schema = '" . $config['dbname'] . "'" .
            " and TABLE_NAME = '" . $table . "'";
        $statement = $this->getAdapter()->fetchAll($sql);

        $primary = new Xyster_Db_PrimaryKey;
        $table = $this->_getLazyTable($table, $schema);
        $primary->setTable($table);
        
        foreach( $statement as $row ) {
            // MySQL doesn't keep names for Primary Keys... weird.
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $primary->addColumn($tcolumn);
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
        $config = $this->getAdapter()->getConfig();
        $sql = "SELECT TABLE_NAME as tablename, INDEX_NAME as indexname, " . 
            "COLUMN_NAME as colname, NON_UNIQUE as nonunique " . 
            "FROM INFORMATION_SCHEMA.STATISTICS " . 
            "WHERE NON_UNIQUE = 0 and index_name <> 'PRIMARY' " .
            " and index_schema = '" . $config['dbname'] . "'" .
            " and TABLE_NAME = '" . $table . "'";
        $statement = $this->getAdapter()->fetchAll($sql);

        $table = $this->_getLazyTable($table);
        $uniques = array();
        
        foreach( $statement as $row ) {
            $key = $row['indexname'];
            
            $column = null;
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $column = $tcolumn;
                    break;
                }
            }

            if ( array_key_exists($key, $uniques) ) {
                $uniques[$key]->addColumn($column);
            } else {
                $unique = new Xyster_Db_UniqueKey;
                $unique->setTable($table)
                    ->setName($row['indexname'])
                    ->addColumn($column);
                $uniques[$key] = $unique;
            }
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " CHANGE COLUMN " . 
           $this->_quote($column->getName()) . ' ' . $this->_quote($newName);
        $tableInfo = $this->getAdapter()->describeTable($table->getName());
        $columnInfo = $tableInfo[$column->getName()];
        $sql .= ' ' . $columnInfo['DATA_TYPE'];
        if ( $columnInfo['LENGTH'] ) {
            $sql .= "(" . $columnInfo['LENGTH'] . ")"; 
        }
        $sql .= ( $columnInfo['NULLABLE'] ) ? ' NULL' : ' NOT NULL';
        if ( $columnInfo['DEFAULT'] !== null ) {
            $sql .= " DEFAULT " . $this->getAdapter()->quote($columnInfo['DEFAULT']);
        }
        $this->getAdapter()->query($sql);
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
        $sql = "RENAME TABLE " . $this->_tableName($table) .
            " TO " . $this->_quote($newName);
        $this->getAdapter()->query($sql);
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " MODIFY COLUMN " . 
           $this->_quote($column->getName()) . ' ';
        $tableInfo = $this->getAdapter()->describeTable($table->getName());
        $columnInfo = $tableInfo[$column->getName()];
        $sql .= $columnInfo['DATA_TYPE'];
        if ( $columnInfo['LENGTH'] ) {
            $sql .= "(" . $columnInfo['LENGTH'] . ")"; 
        }
        $sql .= ( $null ) ? ' NULL' : ' NOT NULL';
        if ( $columnInfo['DEFAULT'] !== null ) {
            $sql .= " DEFAULT " . $this->getAdapter()->quote($columnInfo['DEFAULT']); 
        }
        $this->getAdapter()->query($sql);
        $column->setNullable($null);
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
        $newcol = new Xyster_Db_Column($column->getName());
        $newcol->setLength($length)->setPrecision($precision)->setScale($scale)
            ->setType($type);
            
        $sql = "ALTER TABLE " . $this->_tableName($table) . " MODIFY COLUMN " . 
            $this->_quote($column->getName()) . " " . $this->toSqlType($newcol);
        $tableInfo = $this->getAdapter()->describeTable($table->getName());
        $columnInfo = $tableInfo[$column->getName()]; 
        $sql .= ( $columnInfo['NULLABLE'] ) ? ' NULL' : ' NOT NULL';
        if ( $columnInfo['DEFAULT'] !== null ) {
            $sql .= " DEFAULT " . $this->getAdapter()->quote($columnInfo['DEFAULT']); 
        }
        $this->getAdapter()->query($sql);
        $column->setType($type)->setLength($length)->setPrecision($precision)->setScale($scale);
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
        if ( $type === Xyster_Db_DataType::Clob() ) {
            $sql = 'TEXT';
        } else if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT';
        } else if ( $type === Xyster_Db_DataType::Integer() ) {
            $sql = 'INT';
        } else if ( $type === Xyster_Db_DataType::Real() ) {
            $sql = 'FLOAT';
        } else if ( $type === Xyster_Db_DataType::Float() ) {
            $sql = 'DOUBLE';
        } else {
            $sql = parent::toSqlType($column); 
        }
        return $sql;
    }

    /**
     * Gets the SQL syntax for creating a table
     * 
     * This method does not process index/fulltext; indexes are not part of the
     * SQL standard.
     *
     * @param Xyster_Db_Table $table
     * @return string
     */
    protected function _getSqlForCreateTable( Xyster_Db_Table $table )
    {
        $sql = parent::_getSqlForCreateTable($table);
        foreach( $table->getOptions() as $key => $value ) {
            $sql .= ' ' . $key . ' ' . $value;
        }
        return $sql;
    }
    
    /**
     * Gets the version information of MySQL
     *
     * @return mixed
     */
    private function _getVersion()
    {
        return $this->getAdapter()->fetchOne('SELECT version()');
    }   
}