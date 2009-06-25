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
 * Zend_Db_Expr
 */
require_once 'Zend/Db/Expr.php';
/**
 * An abstraction layer for schema manipulation in Oracle Database Server 
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Schema_Pdo_Oci extends Xyster_Db_Schema_Abstract
{
    /**
     * Creates a new SQL Server schema adapter
     *
     * @param Zend_Db_Adapter_Pdo_Oci $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Oci $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Oci $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Oci $db )
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
     * Creates a foreign key
     *
     * @param Xyster_Db_ForeignKey $fk The foreign key to create
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addForeignKey( Xyster_Db_ForeignKey $fk )
    {
        $sql = "ALTER TABLE " . $this->_tableName($fk->getTable()) .
            " ADD " . $this->_constraintName($fk) . ' FOREIGN KEY ' .
            $this->_quote($fk->getColumns()) . " REFERENCES " . 
            $this->_quote($fk->getReferencedTable()->getName()) . " " .
            $this->_quote($fk->getReferencedColumns());
        if ( $fk->getOnDelete() !== null ) {
            $sql .= ' ON DELETE ' . $fk->getOnDelete()->getSql();
        }
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
        $tableName = $this->_tableName($index->getTable());
        $sql = "CREATE INDEX " . $this->_quote($index->getName()) . " ON " .
            $tableName . " ";
        $columns = array();
        if ( $index->isFulltext() ) {
            /* @todo Fulltext indexing */
        } else {
            foreach( $index->getSortedColumns() as $sort ) {
                /* @var $sort Xyster_Data_Sort */
                $columns[] = $this->_quote($sort->getField()->getName()) .
                	' ' . $sort->getDirection();
            }
            $sql .= "( " . implode(', ', $columns) . " )";
        }
        $this->getAdapter()->query($sql);
    }

    /**
     * Creates a table
     *
     * @param Xyster_Db_Table $table
     * @throws Xyster_Db_Schema_Exception if the table is invalid for creation.
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createTable( Xyster_Db_Table $table )
    {
        foreach( $table->getColumns() as $col ) {
            /* @var $col Xyster_Db_Column */
            if ( $col->getType() === Xyster_Db_DataType::Timestamp() &&
                $col->getDefaultValue() !== null ) {
                $col->setDefaultValue(new Zend_Db_Expr("TO_TIMESTAMP(" . $this->getAdapter()->quote($col->getDefaultValue()) . ", 'YYYY-MM-DD HH24:MI:SS')"));
            } else if ( $col->getType() == Xyster_Db_DataType::Date() &&
                $col->getDefaultValue() !== null ) {
                $col->setDefaultValue(new Zend_Db_Expr('TO_DATE(' . $this->getAdapter()->quote($col->getDefaultValue()) . ", 'YYYY-MM-DD')"));
            }
        }
        $this->getAdapter()->query($this->_getSqlForCreateTable($table));
        
        // create the indexes
        foreach( $table->getIndexes() as $index ) {
            /* @var $index Xyster_Db_Index */
            $this->createIndex($index);
        }
        
        $seqName = null;
        $colName = null;
        foreach( $table->getColumns() as $col ) {
            /* @var $col Xyster_Db_Column */
            if ( $col->getType() === Xyster_Db_DataType::Identity() ) {
                $colName = $col->getName();
                $seqName = $table->getName() . '_' . $colName . '_seq';
                $this->createSequence($seqName, $table->getSchema());
                break;
            }
        }
        // create the insert trigger
        if ( $seqName != null ) {
            $schSeqName = $this->_quote($seqName);
            $tname = $this->_quote($seqName . '_tr');
            if ( $table->getSchema() ) {
                $schSeqName = $this->_quote($table->getSchema()) . '.' . $schSeqName;
                $tname = $this->_quote($table->getSchema()) . '.' . $tname;
            }
            $this->getAdapter()->query('CREATE OR REPLACE TRIGGER ' . $tname . 
            	' BEFORE INSERT ON  ' . $this->_tableName($table) .
                " FOR EACH ROW WHEN (new." . $this->_quote($colName) . " IS NULL)\n" .
                "BEGIN\n" .
                "    SELECT " . $schSeqName . ".nextval INTO :new." .
                $this->_quote($colName) . " FROM dual;\n" .
                "END;");
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
    	$sql = 'select c.owner "constraint_schema", c.constraint_name' .
    	   ' "constraint_name", c.table_name "table_name", c.delete_rule "delete_rule",' .
    	   ' k.column_name "column_name", u.table_name' .
    	   ' "referenced_table_name", rc.column_name "referenced_column_name"' .
    	   ' from all_constraints c inner join all_cons_columns k on ' .
    	   ' c.owner = k.owner and c.constraint_name = k.constraint_name' .
    	   ' inner join all_constraints u on c.r_constraint_name = ' .
    	   ' u.constraint_name and c.r_owner = u.owner inner join ' .
    	   ' all_cons_columns rc on u.owner = rc.owner and u.constraint_name =' .
    	   " rc.constraint_name inner join all_tables t on c.table_name = t.table_name and " .
    	   " c.owner = t.owner where c.constraint_type = 'R' and" .
    	   " c.constraint_name not like 'BIN$%' and t.tablespace_name not in ('SYSTEM','SYSAUX')";
        if ( $schema !== null ) {
            $sql .= " and c.owner = '" . $schema . "'";
        }
        if ( $table !== null ) {
            $sql .= " and c.table_name = '" . $table . "'";
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
        $sql = 'SELECT i.index_name "indname", i.table_owner "schemaname", i.table_name "tablename",' .
            ' c.column_name "colname", c.column_position "colpos", c.descend "coldir" FROM user_indexes i' .
            " INNER JOIN user_ind_columns c ON i.index_name = c.index_name" .
            " AND i.table_name = c.table_name WHERE i.uniqueness = 'NONUNIQUE'";
    	if ( $table != null ) {
    		$sql .= " AND i.table_name = '" . $table . "'";
    	}
    	if ( $schema != null ) {
    		$sql .= " AND i.table_owner = '" . $schema . "'";
    	}
    	$sql .= ' ORDER BY i.table_name, c.column_position';
    	$statement = $this->getAdapter()->fetchAll($sql);
        
        $indexes = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $row['schemaname']);
     
            $key = $row['schemaname'] . '.' . $row['indname'];
            
            $column = null;
            /* @var $column Xyster_Db_Column */
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $column = $tcolumn;
                    break;
                }
            }

            $sortCol = $row['coldir'] == 'DESC' ? $column->desc() : $column->asc();
            if ( array_key_exists($key, $indexes) ) {
                $indexes[$key]->addSortedColumn($sortCol);
            } else {
                $index = new Xyster_Db_Index;
                $index->setTable($table)
                    ->setName($row['indname'])
                    ->addSortedColumn($sortCol);
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
        $sql = 'select c.owner "schemaname", c.constraint_name "indexname",' .
            ' k.column_name "colname", k.position "position" FROM' .
            ' all_constraints c INNER JOIN all_cons_columns k ON ' .
            ' c.owner = k.owner AND c.constraint_name = k.constraint_name' .
            " WHERE c.constraint_type = 'P' and c.constraint_name not like 'BIN$%'";
        if ( $schema != null ) {
            $sql .= " AND c.owner = '" . $schema . "'";
        }
        $sql .= " AND c.table_name = '" . $table . "' ORDER BY k.position";
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
        $sql = 'SELECT c.owner "schemaname", c.constraint_name "indexname",' .
            ' k.column_name "colname", k.position "position" FROM' .
            ' all_constraints c INNER JOIN all_cons_columns k ON' .
            ' c.owner = k.owner AND c.constraint_name = k.constraint_name' .
            " WHERE c.constraint_type = 'U' AND c.constraint_name NOT LIKE 'BIN$%'";
        if ( $schema != null ) {
        	$sql .= " AND c.owner = '" . $schema . "'";
        }
        $sql .= " AND c.table_name = '" . $table . "'";
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
        $sql = "SELECT " . $this->_quote('SEQUENCE_NAME') . ' FROM all_sequences';
        if ( $schema !== null ) {
            $sql .= " WHERE sequence_owner = '" . $schema . "'"; 
        }
        return $this->getAdapter()->fetchCol($sql);
    }
        
    /**
     * Renames a sequence
     * 
     * Oracle stipulates that the sequence must be in your own schema.
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     * @param string $schema The schema name
     * @throws Xyster_Db_Schema_Exception if the database doesn't support sequences
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameSequence( $old, $new, $schema = null )
    {
        $sql = "RENAME ";
        $sql .= $this->_quote($old) . " TO " . $this->_quote($new);
        $this->getAdapter()->query($sql);
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
    	$sql = "ALTER INDEX ";
        if ( $index->getTable()->getSchema() ) {
            $sql .= $this->_quote($index->getTable()->getSchema()) . '.';
        }
        $sql .= $this->_quote($index->getName()) . ' RENAME TO ' .
            $this->_quote($newName);
        $this->getAdapter()->query($sql);
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " MODIFY " .
            $this->_quote($column->getName()) . " DEFAULT " .
            $this->getAdapter()->quote($default);
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " MODIFY " . 
           $this->_quote($column->getName()) . ' ';
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " MODIFY " . 
            $this->_quote($column->getName()) . " " .
            $this->toSqlType($newcol);
        $this->getAdapter()->query($sql);
        $column->setType($type)->setLength($length)->setPrecision($precision)->setScale($scale);
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
     * Converts the database-reported data type into a DataType enum
     *
     * @param string $sqlType The database data type
     * @return Xyster_Db_DataType
     */
    public function toDataType( $sqlType )
    {
        if ( $sqlType == 'ROWID' ) {
            return Xyster_Db_DataType::Varchar();
        } else if ( strpos($sqlType, 'INTERVAL') === 0 ) {
        	return Xyster_Db_DataType::Decimal();
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
        if ( $type === Xyster_Db_DataType::Boolean() ) {
            $sql = 'NUMBER(1,0)';
        } else if ( $type === Xyster_Db_DataType::Bigint() ) {
            $sql = 'NUMBER(19,0)';
        } else if ( $type === Xyster_Db_DataType::Varchar() ) {
            $sql = 'VARCHAR2(' . $column->getLength() . ')';
        } else if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'INTEGER NOT NULL PRIMARY KEY';
        } else {
            $sql = parent::toSqlType($column);
        }
        return $sql;
    }
}