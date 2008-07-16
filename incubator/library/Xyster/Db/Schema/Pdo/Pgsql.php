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
 * An abstraction layer for schema manipulation in PostgreSQL 
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Schema_Pdo_Pgsql extends Xyster_Db_Schema_Abstract
{
    /**
     * Creates a new PostgreSQL schema adapter
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
     * Whether the database supports sequences
     *
     * @return boolean
     */
    public function supportsSequences()
    {
        return true;
    }    

    /**
     * Creates a new index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createIndex( Xyster_Db_Index $index )
    {
        $tableName = $this->_tableName($index->getTable());
        $sql = "CREATE INDEX " . $this->_quote($index->getName()) . " ON " .
            $tableName . " ";
        $columns = array();
        /* @todo Fulltext indexing */
        foreach( $index->getSortedColumns() as $sort ) {
            /* @var $sort Xyster_Data_Sort */
            // Postgres doesn't support index ordering until 8.3
            $vdir = ( $this->_getVersion() >= 8.3 ) ?
                ' ' . $sort->getDirection() : ''; 
            $columns[] = $this->_quote($sort->getField()->getName()) . $vdir;
        }
        $sql .= "( " . implode(', ', $columns) . " )";
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
        $sql = "DROP INDEX ";
        if ( $index->getTable()->getSchema() ) {
            $sql .= $this->_quote($index->getTable()->getSchema()) . '.';
        }
        $sql .= $this->_quote($index->getName());
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
        if ( $schema !== null ) {
            $sql .= " and r.constraint_schema = '" . $schema . "'";
        }
        if ( $table !== null ) {
            $sql .= " and r.table_name = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $fks = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['table_name'], $row['constraint_schema']);
            $refTable = $this->_getLazyTable($row['referenced_table_name'], $row['constraint_schema']);
            
            $name = $row['constraint_schema'] . '.' . $row['constraint_name'];
            if ( !array_key_exists($name, $fks) ) {
                $fk = new Xyster_Db_ForeignKey;
                $fk->setReferencedTable($refTable)
                    ->setTable($table)
                    ->setName($row['constraint_name']);
                if ( $row['delete_rule'] ) {
                    $fk->setOnDelete(Xyster_Db_ReferentialAction::fromSql($row['delete_rule']));
                }
                if ( $row['update_rule'] ) {
                    $fk->setOnUpdate(Xyster_Db_ReferentialAction::fromSql($row['update_rule']));
                }
                $fks[$name] = $fk;
            }
            foreach( $table->getColumns() as $column ) {
                if ( $column->getName() == $row['column_name'] ) {
                    $fks[$name]->addColumn($column);
                }
            }
            foreach( $refTable->getColumns() as $column ) {
                if ( $column->getName() == $row['referenced_column_name'] ) {
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
        $sql = 'select c.relname as "indexname", c2.relname as "tablename", ' . 
            't.schemaname as "schemaname", a.attname as "colname", o.opcname ' .
            'from pg_catalog.pg_index i ' . 
            'inner join pg_catalog.pg_class c on i.indexrelid = c.oid ' . 
            'inner join pg_catalog.pg_class c2 on i.indrelid = c2.oid ' .
            'inner join pg_catalog.pg_tables t on c2.relname = t.tablename ' .
            'inner join pg_catalog.pg_attribute a on c2.oid = a.attrelid and a.attnum = any(i.indkey) ' .
            'left join pg_catalog.pg_opclass o on o.oid = all(i.indclass) ' . 
            "where i.indisprimary = FALSE and i.indisunique = FALSE ";
        if ( $table !== null ) {
            $sql .= " and c2.relname = '" . $table . "' ";
        }
        $schemaName = ($schema !== null) ?
            " = '" . $schema . "'" : " <> 'pg_catalog'";
        $sql .= " and t.schemaname " . $schemaName;
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $indexes = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $row['schemaname']);
     
            $key = $row['schemaname'] . '.' . $row['indexname'];
            
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
                    ->setFulltext(strpos($row['opcname'], 'tsvector') !== false)
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
        $sql = 'select c.relname as "indexname", c2.relname as "tablename", ' . 
            't.schemaname as "schemaname", a.attname as "colname" ' . 
            'from pg_catalog.pg_index i ' . 
            'inner join pg_catalog.pg_class c on i.indexrelid = c.oid ' . 
            'inner join pg_catalog.pg_class c2 on i.indrelid = c2.oid ' .
            'inner join pg_catalog.pg_tables t on c2.relname = t.tablename ' .
            'inner join pg_catalog.pg_attribute a on c2.oid = a.attrelid and a.attnum = any(i.indkey) ' . 
            "where i.indisprimary = TRUE and c2.relname = '" . $table . "'";
        $schemaName = ($schema !== null) ?
            " = '" . $schema . "'" : " <> 'pg_catalog'"; 
        $sql .= " and t.schemaname " . $schemaName;
        $statement = $this->getAdapter()->fetchAll($sql);

        $primary = new Xyster_Db_PrimaryKey;
        $table = $this->_getLazyTable($table, $schema);
        $primary->setTable($table);
        
        foreach( $statement as $row ) {
            $primary->setName($row['indexname']);
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
        $sql = 'select c.relname as "indexname", c2.relname as "tablename", ' . 
            't.schemaname as "schemaname", a.attname as "colname" ' .
            'from pg_catalog.pg_index i ' . 
            'inner join pg_catalog.pg_class c on i.indexrelid = c.oid ' . 
            'inner join pg_catalog.pg_class c2 on i.indrelid = c2.oid ' .
            'inner join pg_catalog.pg_tables t on c2.relname = t.tablename ' .
            'inner join pg_catalog.pg_attribute a on c2.oid = a.attrelid and a.attnum = any(i.indkey) ' . 
            "where i.indisprimary = FALSE and i.indisunique = TRUE and c2.relname = '" . $table . "'";
        $schemaName = ($schema !== null) ?
            " = '" . $schema . "'" : " <> 'pg_catalog'";
        $sql .= " and t.schemaname " . $schemaName;
        $statement = $this->getAdapter()->fetchAll($sql);

        $table = $this->_getLazyTable($table, $schema);
        $uniques = array();
        
        foreach( $statement as $row ) {
            $key = $row['schemaname'] . '.' . $row['indexname'];
            
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
     * Lists the sequence names in the given database (or schema)
     *
     * @param string $schema Optional. The schema used to locate sequences.
     * @return array of sequence names
     */
    public function listSequences( $schema = null )
    {
        $sql = "SELECT " . $this->_quote('relname') . ' FROM ' .
            $this->_quote('pg_catalog') . '.' .
            $this->_quote('pg_statio_all_sequences');
        if ( $schema !== null ) {
            $sql .= " WHERE schemaname = '" . $schema . "'"; 
        }
        return $this->getAdapter()->fetchCol($sql);
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " RENAME COLUMN " . 
           $this->_quote($column->getName()) . ' TO ' . $this->_quote($newName);
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
        $sql = 'ALTER INDEX ';
        if ( $index->getTable()->getSchema() ) {
            $sql .= $this->_quote($index->getTable()->getSchema()) . '.';
        }
        $sql .= $this->_quote($index->getName()) . ' RENAME TO ' .
            $this->_quote($newName);
        $this->getAdapter()->query($sql);
        $index->setName($newName);
    }
    
    /**
     * Renames a sequence
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     * @param string $schema The schema name
     * @throws Xyster_Db_Schema_Exception if the database doesn't support sequences
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameSequence( $old, $new, $schema = null )
    {
        $sql = "ALTER TABLE ";
        if ( $schema ) {
            $sql .= $this->_quote($schema) . '.';
        }
        $sql .= $this->_quote($old) . " RENAME TO " . $this->_quote($new);
        $this->getAdapter()->query($sql);
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " ALTER COLUMN " . 
           $this->_quote($column->getName()) . ' ';
        $sql .= ( $null ) ? ' DROP NOT NULL' : ' SET NOT NULL';
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " ALTER COLUMN " . 
            $this->_quote($column->getName()) . " TYPE " .
            $this->toSqlType($newcol);
        $this->getAdapter()->query($sql);
        $column->setType($type)->setLength($length)->setPrecision($precision)->setScale($scale);
    }
    
    /**
     * Converts the database-reported data type into a DataType enum
     *
     * @param string $sqlType The database data type
     * @return Xyster_Db_DataType
     */
    public function toDataType( $sqlType )
    {
        if ( !strcasecmp($sqlType, 'BPCHAR') ) {
            return Xyster_Db_DataType::Char();
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
            $sql = 'BYTEA';
        } else if ( $type === Xyster_Db_DataType::Clob() ) {
            $sql = 'TEXT';
        } else if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'SERIAL PRIMARY KEY';
        } else {
            $sql = parent::toSqlType($column); 
        }
        return $sql;
    }
    
    /**
     * Gets the version information of PostgreSQL
     *
     * @return mixed
     */
    private function _getVersion()
    {
        $version = $this->getAdapter()->fetchOne('SELECT version()');
        $matches = array();
        return preg_match('/^PostgreSQL ([0-9.]+)/', $version, $matches) ? $matches[1] : 0;
    }
}