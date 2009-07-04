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
class Xyster_Db_Schema_Pdo_Mssql extends Xyster_Db_Schema_Abstract
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
     * Adds a column to a table
     *
     * @param Xyster_Db_Column $column The column to add
     * @param Xyster_Db_Table $table The table 
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addColumn( Xyster_Db_Column $column, Xyster_Db_Table $table )
    {
        $sql = "ALTER TABLE " . $this->_tableName($table) . " ADD " .
            $this->getAdapter()->quoteIdentifier($column->getName()) . ' ' .
            $this->toSqlType($column);
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
        // They added the DDL for a fulltext index starting in SQL Server 9 (aka 2005)
        $version = $this->_getVersion();
        $fulltext = intval(substr($version,0,strpos($version,'.'))) > 8;
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
        $version = $this->_getVersion();
        $oldSyntax = intval(substr($version,0,strpos($version,'.'))) < 9;
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

        $sql = 'SELECT c.constraint_schema, c.constraint_name as keyname,' .
                " update_rule as onupdate, delete_rule as ondelete, " .
                " k.table_schema as schemaname, k.table_name as tablename, k.column_name as colname," .
        		" rk.table_name AS reftablename, rk.column_name AS refcolname";
        $sql .= " FROM information_schema.referential_constraints c INNER JOIN" .
                " information_schema.key_column_usage k ON" .
                " c.constraint_name = k.constraint_name AND" .
                " c.constraint_schema = k.constraint_schema INNER JOIN" .
                " information_schema.key_column_usage rk ON" .
                " c.unique_constraint_name = rk.constraint_name AND" .
                " c.constraint_schema = rk.constraint_schema";
        if ( $schema !== null ) {
            $sql .= " and c.constraint_schema = '" . $schema . "'";
        }
        if ( $table !== null ) {
            $sql .= " and c.table_name = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $fks = array();
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $row['schemaname']);
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
    	$version = $this->_getVersion();
    	$oldSyntax = intval(substr($version,0,strpos($version,'.'))) < 9;
    	if ( $oldSyntax ) {
    		$sql = "select i.name as indexname, o.name as tablename, k.keyno," .
    		      " c.name as colname FROM sysindexes i INNER JOIN" .
    		      " sysobjects o on i.id = o.id inner join sysindexkeys k" .
    		      " on i.indid = k.indid and i.id = k.id inner join" .
    		      " syscolumns c on k.colid = c.colid and k.id = c.id" .
    		      " where i.indid not in (0,255) and o.xtype = 'U' and" .
    		      " (i.status & 2 = 0) and (i.status & 8388608 = 0)";
	        if ( $table !== null ) {
	            $sql .= " and o.name= '" . $table . "' ";
	        }
	        $sql .= " order by o.name, i.name, k.keyno";
    	} else {
    		$sql = "select i.name as indexname, o.name as tablename, k.key_ordinal," .
    		      " c.name as colname, s.name as schemaname FROM sys.indexes i INNER JOIN" .
    		      " sys.objects o on i.object_id = o.object_id inner join" .
    		      " sys.index_columns k on i.index_id = k.index_id and" .
    		      " i.object_id = k.object_id inner join sys.columns c on" .
    		      " k.column_id = c.column_id and k.object_id = c.object_id" .
    		      " inner join sys.schemas s on o.schema_id = s.schema_id" .
    		      " where i.is_unique = 0 and o.type = 'U'";
    		if ( $table !== null ) {
    			$sql .= " and o.name = '" . $table . "'";
    		}
    		$sql .= " order by o.name, i.name, k.key_ordinal";
    	}
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $indexes = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $oldSyntax?null:$row['schemaname']);
     
            $key = $oldSyntax ?
                $row['indexname'] : $row['schemaname'] . '.' . $row['indexname'];
            
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
                    ->setName($row['indexname'])
                    ->addColumn($column);
                $indexes[$key] = $index;
            }
        }
        
        return $indexes;
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
        $args = array($table);
        $sql = "select t.constraint_name, t.table_name, t.table_schema," .
                " c.column_name from" .
                " information_schema.table_constraints t inner join" .
                " information_schema.constraint_column_usage c on" .
                " t.constraint_schema = c.constraint_schema and" .
                " t.table_name = c.table_name and " .
                " t.constraint_name = c.constraint_name where" .
                " t.constraint_type = 'PRIMARY KEY' and" .
                " t.table_name = ?";
        if ( $schema !== null ) {
            $sql .= " and t.table_schema = ?";
            $args[] = $schema;
        }
        $statement = $this->getAdapter()->fetchAll($sql, $args);

        $primary = new Xyster_Db_PrimaryKey;
        $table = $this->_getLazyTable($table, $schema);
        $primary->setTable($table);
        
        foreach( $statement as $row ) {
            $primary->setName($row['constraint_name']);
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['column_name'] ) {
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
    	$args = array($table);
        $sql = "select t.constraint_name, t.table_name, t.table_schema," .
                " c.column_name from" .
                " information_schema.table_constraints t inner join" .
                " information_schema.constraint_column_usage c on" .
                " t.constraint_schema = c.constraint_schema and" .
                " t.table_name = c.table_name and " .
                " t.constraint_name = c.constraint_name where" .
                " t.constraint_type = 'UNIQUE' and" .
                " t.table_name = ?";
        if ( $schema !== null ) {
        	$sql .= " and t.table_schema = ?";
        	$args[] = $schema;
        }
        
        $statement = $this->getAdapter()->fetchAll($sql, $args);

        $table = $this->_getLazyTable($table, $schema);
        $uniques = array();
        
        foreach( $statement as $row ) {
            $key = $row['table_schema'] . '.' . $row['constraint_name'];
            
            $column = null;
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['column_name'] ) {
                    $column = $tcolumn;
                    break;
                }
            }

            if ( array_key_exists($key, $uniques) ) {
                $uniques[$key]->addColumn($column);
            } else {
                $unique = new Xyster_Db_UniqueKey;
                $unique->setTable($table)
                    ->setName($row['constraint_name'])
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
        $indexName = $index->getTable()->getName() . '.' . $index->getName();
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
    	$sql = "ALTER TABLE " . $this->_tableName($table);
        $sql .= " ADD CONSTRAINT df_" . $table->getName() . '_' .
            $column->getName() . " DEFAULT " . $this->getAdapter()->quote($default);
        $sql .= " FOR " . $this->_quote($column->getName());
        $this->getAdapter()->query($sql);
        $column->setDefaultValue($default);        
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
        $tableInfo = $this->getAdapter()->describeTable($table->getName(), $table->getSchema());
        $columnInfo = $tableInfo[$column->getName()];
        $sql .= $columnInfo['DATA_TYPE'];
        if ( $column->getType()->getName() == 'Varchar' ||
            $column->getType()->getName() == 'Char' ) {
            $columnInfo['LENGTH'] = $columnInfo['LENGTH']/2;
        }
        if ( ($column->getType()->getName() == 'Varchar' ||
            $column->getType()->getName() == 'Char') && $columnInfo['LENGTH'] ) {
            $sql .= "(" . $columnInfo['LENGTH'] . ")"; 
        } else if ( $column->getType()->getName() == 'Decimal' && $columnInfo['PRECISION'] ) {
            $sql .= "(" . $columnInfo['PRECISION'] . ',' . $columnInfo['SCALE'] . ')';
        }        
        $sql .= ( $null ) ? ' NULL' : ' NOT NULL';
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
            $this->_quote($column->getName()) . " " . $this->toSqlType($newcol);
        $tableInfo = $this->getAdapter()->describeTable($table->getName());
        $columnInfo = $tableInfo[$column->getName()]; 
        $sql .= ( $columnInfo['NULLABLE'] ) ? ' NULL' : ' NOT NULL';
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
     * Gets the SQL syntax for creating a table
     * 
     * This method does not process index/fulltext; indexes are not part of the
     * SQL standard.
     *
     * @param Xyster_Db_Table $table
     */
    protected function _getSqlForCreateTable( Xyster_Db_Table $table )
    {
        if ( !$table->getName() ) {
            require_once 'Xyster/Db/Schema/Exception.php';
            throw new Xyster_Db_Schema_Exception('Tables must have a name to be created');
        }
        
        $fullsql = 'CREATE TABLE ' . $this->_quote($table->getName()) . ' (';
        $tableElements = array();
        foreach( $table->getColumns() as $column ) {
            /* @var $column Xyster_Db_Column */
            if ( !$column->getName() || !$column->getType()  ) {
                require_once 'Xyster/Db/Schema/Exception.php';
                throw new Xyster_Db_Schema_Exception('Columns must have both a name and a type defined');
            }
            
            $sql = $this->_quote($column->getName()) . ' ' .
                $this->toSqlType($column);
            if ( $column->getType() !== Xyster_Db_DataType::Identity() ) {
                $sql .= !$column->isNullable() ? ' NOT NULL ' : ' NULL ';
            }
            if ( $column->getDefaultValue() !== null ) {
                $sql .= ' DEFAULT ' . $this->getAdapter()->quote($column->getDefaultValue());
            }
            if ( $column->isUnique() ) {
                $sql .= ' UNIQUE';
            }
            $tableElements[] = $sql;
        }
        if ( $table->getPrimaryKey() !== null ) {
            $pk = $table->getPrimaryKey();
            $sql = $this->_constraintName($pk);
            $sql .= ' PRIMARY KEY ' . $this->_quote($pk->getColumns());
            $tableElements[] = $sql;
        }
        foreach( $table->getUniqueKeys() as $unique ) {
            /* @var $unique Xyster_Db_UniqueKey */
            $sql = $this->_constraintName($unique);
            $sql .= ' UNIQUE ' .  $this->_quote($unique->getColumns());
            $tableElements[] = $sql;
        }
        foreach( $table->getForeignKeys() as $fk ) {
            /* @var $fk Xyster_Db_ForeignKey */
            $sql = $this->_constraintName($fk);
            $sql .= ' FOREIGN KEY ' . $this->_quote($fk->getColumns()) . 
                ' REFERENCES ' . $this->_quote($fk->getReferencedTable()) .
                ' ' . $this->_quote($fk->getReferencedColumns());
            if ( $fk->getOnDelete() !== null ) {
                $sql .= ' ON DELETE ' . $fk->getOnDelete()->getSql();
            }
            if ( $fk->getOnUpdate() !== null ) {
                $sql .= ' ON UPDATE ' . $fk->getOnUpdate()->getSql();
            }
            $tableElements[] = $sql;
        }
        $fullsql .= implode(",\n ", $tableElements) . ')';
        return $fullsql;
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