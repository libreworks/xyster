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
 * A gateway and abstraction layer for database administration
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_Gateway_Abstract
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db = null;
    
    /**
     * Creates a new DB gateway
     * 
     * This constructor should be overloaded with one that has the concrete
     * type hint.
     *
     * @param Zend_Db_Adapter_Abstract $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Abstract $db = null )
    {
    	$this->_db = $db;
    }
    
    /**
     * Gets the database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
    	return $this->_db;
    }

    /**
     * Adds a column to a table
     *
     * @param string $table The table name
     * @param string $name The name of the new column
     * @param Xyster_Db_Gateway_DataType $type The data type
     * @param mixed $argument An optional argument for the data type
     */
    public function addColumn( $table, $name, Xyster_Db_Gateway_DataType $type, $argument=null )
    {
        
    }
    
    /**
     * Adds a foreign key to a table
     *
     * @param string $table The table name
     * @param mixed $cols The string column name or an array of column names in the source table
     * @param string $foreignTable The foreign table name
     * @param mixed $foreignCols The string column name or an array of column names in the foreign table
     * @param Xyster_Db_Gateway_ReferentialAction $onDelete optional
     * @param Xyster_Db_Gateway_ReferentialAction $onUpdate optional
     */
    public function addForeign( $table, $cols, $foreignTable, $foreignCols, Xyster_Db_Gateway_ReferentialAction $onDelete=null, Xyster_Db_Gateway_ReferentialAction $onUpdate=null )
    {
        
    }
    
    /**
     * Adds an index to a table
     *
     * @param string $table The table name
     * @param array $cols The string column name or an array of column names
     * @param string $name An optional name of the index
     */
    public function addIndex( $table, $cols, $name=null )
    {
        
    }
    
    /**
     * Adds a primary key to a table
     *
     * @param string $table The table name
     * @param mixed $cols The string column name or an array of column names
     */
    public function addPrimary( $table, $cols )
    {
        
    }
    
    /**
     * Creates an index
     * 
     * This method does the same thing as {@link addIndex}
     *
     * @param string $name The name of the index
     * @param string $table The table name
     * @param mixed $cols The string column name or an array of column names
     */
    public function createIndex( $name, $table, $cols )
    {
        
    }
    
    /**
     * Creates a sequence
     *
     * @param string $name The name of the sequence
     * @param int $inc The amount the sequence increments
     * @param int $start The starting number for the sequence
     * @param int $min The sequence minimum
     * @param int $max The sequence maximum
     */
    public function createSequence( $name, $inc=null, $start=null, $min=null, $max=null )
    {
        $this->_checkSequenceSupport();
    }
    
    /**
     * Creates a table builder
     *
     * @param string $name The name of the table
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function createTable( $name )
    {
    	require_once 'Xyster/Db/Gateway/TableBuilder.php';
        return new Xyster_Db_Gateway_TableBuilder($name, $this);
    }
    
    /**
     * Removes a column from a table
     *
     * @param string $table The table name
     * @param string $name The column name
     */
    public function dropColumn( $table, $name )
    {
        
    }
    
    /**
     * Drops a foreign key from a table
     *
     * @param string $table The table name
     * @param string $name The foreign key name
     */
    public function dropForeign( $table, $name )
    {
        
    }
    
    /**
     * Removes an index
     *
     * @param string $name The index name
     * @param string $table The table name (not required for all databases)
     */
    public function dropIndex( $name, $table=null )
    {
    	
    }
    
    /**
     * Removes a primary key from a table
     *
     * @param string $table The table name
     */
    public function dropPrimary( $table )
    {
        
    }
    
    /**
     * Drops a sequence
     *
     * @param string $name The sequence name
     */
    public function dropSequence( $name )
    {
        $this->_checkSequenceSupport();
    }
    
    /**
     * Drops a table
     *
     * @param string $name The table name
     */
    public function dropTable( $name )
    {
        
    }
    
    /**
     * Lists all indexes
     *
     * @return array An array of string index names
     */
    public function listIndexes()
    {
        
    }
    
    /**
     * Lists all sequences
     * 
     * @return array An array of string sequence names
     */
    public function listSequences()
    {
        $this->_checkSequenceSupport();
    }
    
    /**
     * Renames an index
     *
     * @param string $old The current index name
     * @param string $new The new index name
     * @param string $table The table name (not required on all databases)
     */
    public function renameIndex( $old, $new, $table=null )
    {
    	
    }
    
    /**
     * Renames a table
     *
     * @param string $old The current table name
     * @param string $new The new table name
     */
    public function renameTable( $old, $new )
    {
    	
    }
    
    /**
     * Renames a sequence
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     */
    public function renameSequence( $old, $new )
    {
        $this->_checkSequenceSupport();
    }
    
    /**
     * Renames a column
     *
     * @param string $table The table name
     * @param string $old The current column name
     * @param string $new The new column name
     */
    public function renameColumn( $table, $old, $new )
    {
    	
    }
    
    /**
     * Sets the default value for a column
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param mixed $default The new column default value
     */
    public function setDefault( $table, $column, $default )
    {
        
    }
    
    /**
     * Sets whether or not the column will accept null
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param boolean $null True for NULL, false for NOT NULL
     */
    public function setNull( $table, $column, $null=true )
    {
    	
    }
    
    /**
     * Creates a unique index on a column or columns
     *
     * @param string $table The table name
     * @param mixed $cols The string column name or an array of column names 
     */
    public function setUnique( $table, $cols )
    {
    	
    }
    
    /**
     * Changes the type of a column
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param Xyster_Db_Gateway_DataType $type The new data type
     * @param mixed $argument An optional argument for the data type
     */
    public function setType( $table, $column, Xyster_Db_Gateway_DataType $type, $argument=null )
    { 
    	
    }
    
    /**
     * Whether the database supports sequences
     *
     * @return boolean
     */
    public function supportsSequences()
    {
    	return false;
    }
    
    /**
     * Quotes an identifier or a group of identifiers
     *
     * @param mixed $thing Either a string identifier or an array of them
     * @return string
     */
    protected function _quote( $thing )
    {
        $sql = '';
        if ( is_array($thing) ) {
            $sql = '(';
            foreach( $thing as $key => $item ) {
                if ( $key != 0 ) {
                    $sql .= ',';
                }
                $sql .= $this->getAdapter()->quoteIdentifier($item); 
            }
            $sql .= ')';
        } else {
            $sql = $this->getAdapter()->quoteIdentifier($thing);
        }
        return $sql;
    }
    
    /**
     * Sets the database adapter
     * 
     * Each class should implement a public setAdapter method with a type hint
     * for the exact type of adapter expected.  For instance, the
     * Xyster_Db_Gateway_Pdo_Pgsql class will want a Zend_Db_Adapter_Pdo_Pgsql
     * for its adapter. 
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @return Xyster_Db_Gateway_Abstract provides a fluent interface
     */
    protected function _setAdapter( Zend_Db_Adapter_Abstract $db )
    {
    	$this->_db = $db;
    	return $this;
    }
    
    /**
     * Checks that the database system supports sequences
     *
     * @throws Xyster_Db_Gateway_Exception if it doesn't support sequences
     */
    private function _checkSequenceSupport()
    {
        if ( !$this->supportsSequences() ) {
            require_once 'Xyster/Db/Gateway/Exception.php';
            throw new Xyster_Db_Gateway_Exception('This database does not support sequences');
        }
    }
    
    /**
     * Gets the SQL statement to add a column
     *
     * @param string $table The table name 
     * @param string $name The column name
     * @param Xyster_Db_Gateway_DataType $type The data type
     * @param mixed $argument An argument for the data type
     * @return string
     */
    protected function _getAddColumnSql( $table, $name, Xyster_Db_Gateway_DataType $type, $argument=null )
    {
        return "ALTER TABLE " . $this->getAdapter()->quoteIdentifier($table) . 
           " ADD COLUMN " . $this->getAdapter()->quoteIdentifier($name) . 
           $this->_translateType($type, $argument);
    }
    
    /**
     * Gets the SQL statement to add a foreign key to a table
     *
     * @param string $table The table name
     * @param array $columns The columns in the key
     * @param string $foreignTable The foreign table name
     * @param array $foreignColumns The foreign columns referenced
     * @param Xyster_Db_Gateway_ReferentialAction $onDelete The ON DELETE action
     * @param Xyster_Db_Gateway_ReferentialAction $onUpdate The ON UPDATE action
     * @return string
     */
    protected function _getAddForeignSql( $table, array $columns, $foreignTable, array $foreignColumns, Xyster_Db_Gateway_ReferentialAction $onDelete=null, Xyster_Db_Gateway_ReferentialAction $onUpdate=null )
    {
        $sql = "ALTER TABLE " . $this->_quote($table) .
           " ADD FOREIGN KEY " . $this->_quote($columns) . " REFERENCES " . 
           $this->_quote($foreignTable) . " " . $this->_quote($foreignColumns);
        if ( $onDelete !== null ) {
            $sql .= ' ON DELETE ' . $onDelete->getSql();
        }
        if ( $onUpdate !== null ) {
            $sql .= ' ON UPDATE ' . $onDelete->getSql();
        }
        return $sql;
    }
    
    /**
     * Gets the SQL statement to add a primary key to a table
     *
     * @param string $table The table name 
     * @param array $columns The columns in the key
     * @return string
     */
    protected function _getAddPrimary( $table, array $columns )
    {
        return "ALTER TABLE " . $this->_quote($table) . " ADD PRIMARY KEY" . 
           $this->_quote($columns);
    }
    
    /**
     * Gets the SQL statement to create a sequence
     * 
     * If the DBMS doesn't support sequences, this method won't be called.
     * There is no need to throw an exception for this method, just leave an 
     * empty method body or return null.
     * 
     * This syntax is taken from SQL-2003
     *
     * @param string $name The name of the sequence
     * @param int $inc The increment
     * @param int $start The starting value
     * @param int $min The minimum value
     * @param int $max The maximum value
     * @return string
     */
    protected function _getCreateSequenceSql( $name, $inc=null, $start=null, $min=null, $max=null )
    {
        $sql = "CREATE SEQUENCE " . $this->_quote($name);
        if ( $inc > 0 ) {
            $sql .= " INCREMENT BY " . $this->getAdapter()->quote($inc);
        }
        if ( $start > 0 ) {
            $sql .= " START WITH " . $this->getAdapter()->quote($start);
        }
        if ( $min !== null && $min > -1 ) {
            $sql .= " MINVALUE " . $this->getAdapter()->quote($min);
        }
        if ( $max !== null && $max > $min ) {
            $sql .= " MAXVALUE " . $this->getAdapter()->quote($max);
        }
        return $sql;
    }
    
    /**
     * Gets the SQL statement to drop a column from a table
     *
     * @param string $table The table name 
     * @param string $name The column name
     * @return string
     */
    protected function _getDropColumnSql( $table, $name )
    {
        return "ALTER TABLE " . $this->_quote($table) . " DROP COLUMN " .
           $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to drop a foreign key from a table
     *
     * @param string $table The table name
     * @param string $name The key name
     * @return string
     */
    protected function _getDropForeignSql( $table, $name )
    {
        return "ALTER TABLE " . $this->_quote($table) . " DROP CONSTRAINT " .
           $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to drop a sequence
     * 
     * If the DBMS doesn't support sequences, this method won't be called.
     * There is no need to throw an exception for this method, just leave an 
     * empty method body or return null.
     * 
     * The syntax for this statement is taken from SQL-2003
     *
     * @param string $name The sequence name
     * @return string
     */
    protected function _getDropSequenceSql( $name )
    {
        return "DROP SEQUENCE " . $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to drop a table
     *
     * @param string $name The table name
     * @return string
     */
    protected function _getDropTableSql( $name )
    {
        return "DROP TABLE " . $this->_quote($name);
    }
    
    /**
     * Gets the SQL statement to rename a sequence
     * 
     * If the DBMS doesn't support sequences, this method won't be called.
     * There is no need to throw an exception for this method, just leave an 
     * empty method body or return null.
     * 
     * SQL-2003 doesn't have an option for renaming a sequence
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     * @return string
     */
    protected function _getRenameSequenceSql( $old, $new )
    {
        return null;
    }
        
    /**
     * Gets the SQL statement to set a default value on a column
     *
     * @param string $table The table name 
     * @param string $column The column name
     * @param mixed $default The default value
     * @return string
     */
    protected function _getSetDefaultSql( $table, $column, $default )
    {
        return "ALTER TABLE " . $this->_quote($table) . " ALTER COLUMN " .
           $this->_quote($column) . " SET DEFAULT " .
           $this->getAdapter()->quote($default);
    }
    
    /**
     * Gets the SQL statement to create a UNIQUE index for one or more columns
     *
     * @param string $table The table name
     * @param array $columns The columns in the unique index
     * @return string
     */
    protected function _getSetUniqueSql( $table, array $columns )
    {
        return "ALTER TABLE " . $this->_quote($table) . " ADD UNIQUE " . 
            $this->_quote($columns);
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
    abstract protected function _getCreateIndexSql( $name, $table, array $columns, $fulltext=false );
    
    /**
     * Gets the SQL to create a table
     *
     * This method does not process index/fulltext; indexes are not part of the
     * SQL standard.
     * 
     * @param Xyster_Db_Gateway_TableBuilder $builder
     * @return string
     */
    protected function _getCreateTableSql( Xyster_Db_Gateway_TableBuilder $builder )
    {
        $fullsql = 'CREATE TABLE ' . $this->_quote($builder->getName()) . ' (';
        $tableElements = array();
        foreach( $builder->getColumns() as $column ) {
            /* @var $column Xyster_Db_Gateway_TableBuilder_Column */
            $sql = '';
            $sql .= $this->_quote($column->getName()) . ' ' .
                $this->_translateType($column->getDataType(), $column->getArgument());
            if ( !$column->isNull() ) {
               $sql .= ' NOT NULL ';
            }
            $sql .= ' DEFAULT ' . (( $column->getDefault() === null ) ?
                'NULL' : $this->getAdapter()->quote($column->getDefault()));
            if ( $column->isPrimary() ) { 
                $sql .= ' PRIMARY KEY';
            } else if ( $column->isUnique() ) {
                $sql .= ' UNIQUE';
            }
            if ( $column->isForeign() ) {
                $sql .= ' REFRENCES ' . $this->_quote($column->getForeignKeyTable()) . 
                    ' ' . $this->_quote($column->getForeignKeyColumn());
                if ( $column->getForeignKeyOnDelete() !== null ) {
                    $sql .= ' ON DELETE ' . $column->getForeignKeyOnDelete()->getSql();
                }
                if ( $column->getForeignKeyOnUpdate() !== null ) {
                    $sql .= ' ON UPDATE ' . $column->getForeignKeyOnUpdate()->getSql();
                }
            }
            $tableElements[] = $sql;
        }
        if ( $builder->getPrimaryKey() !== null ) {
            $tableElements[] = ' PRIMARY KEY ' . $this->_quote($builder->getPrimaryKey()->getColumns());
        }
        foreach( $builder->getUniques() as $unique ) {
            /* @var $unique Xyster_Db_Gateway_TableBuilder_Unique */
            $tableElements[] = ' UNIQUE ' .  $this->_quote($unique->getColumns());
        }
        foreach( $builder->getForeignKeys() as $fk ) {
            /* @var $fk Xyster_Db_Gateway_TableBuilder_ForeignKey */
            $sql = ' FOREIGN KEY ' . $this->_quote($fk->getColumns()) . 
                ' REFERENCES ' . $this->_quote($fk->getTable()) .  ' ' .
                $this->_quote($fk->getForeignColumns());
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
     * Gets the SQL statement to drop an index
     *
     * @param string $name The index name
     * @param string $table The table (not all dbs require this)
     * @return string
     */
    abstract protected function _getDropIndexSql( $name, $table=null );
    
    /**
     * Gets the SQL statement to drop a primary key from a table
     *
     * @param string $table The table name
     * @return string
     */
    abstract protected function _getDropPrimarySql( $table );
    
    /**
     * Gets the SQL statement to list the indexes
     *
     * @return string
     */
    abstract protected function _getListIndexesSql();
    
    /**
     * Gets the SQL statement to list the sequences
     *
     * If the DBMS doesn't support sequences, this method won't be called.
     * There is no need to throw an exception for this method, just leave an 
     * empty method body or return null.
     * 
     * @return string
     */
    abstract protected function _getListSequencesSql();
    
    /**
     * Gets the SQL statement to rename an index
     *
     * @param string $old The current index name
     * @param string $new The new index name
     * @param string $table The table name (not all dbs require this)
     * @return string
     */
    abstract protected function _getRenameIndexSql( $old, $new, $table=null );
    
    /**
     * Gets the SQL statement to rename a table
     *
     * @param string $old The current table name
     * @param string $new The new table name
     * @return string
     */
    abstract protected function _getRenameTableSql( $old, $new );
    
    /**
     * Gets the SQL statement to rename a column in a table
     *
     * @param string $table The table name 
     * @param string $old The current column name
     * @param string $new The new column name
     * @return string
     */
    abstract protected function _getRenameColumnSql( $table, $old, $new );
        
    /**
     * Gets the SQL statement to set a column's NULL status
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param boolean $null True for NULL, false for NOT NULL
     * @return string
     */
    abstract protected function _getSetNullSql( $table, $column, $null=true );
    
    /**
     * Gets the SQL statement to set the data type of a column
     *
     * @param string $table The table name
     * @param string $column The column name
     * @param Xyster_Db_Gateway_DataType $type The data type
     * @param mixed $argument An argument for the data type
     * @return string
     */
    abstract protected function _getSetTypeSql( $table, $column, Xyster_Db_Gateway_DataType $type, $argument=null );
    
    /**
     * Translates a DataType enum into the correct SQL syntax
     *
     * @param Xyster_Db_Gateway_DataType $type
     * @param mixed $argument
     * @return string
     */
    abstract protected function _translateType( Xyster_Db_Gateway_DataType $type, $argument=null );
}