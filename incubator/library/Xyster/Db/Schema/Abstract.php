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
 * @subpackage Schema
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_DataType
 */
require_once 'Xyster/Db/DataType.php';
/**
 * An abstraction layer for database schema administration
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @subpackage Schema
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_Schema_Abstract
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db = null;
    
    /**
     * @var array
     */
    private $_lazyTables = array();
    
    /**
     * Creates a new DB schema utility
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
     * Adds a column to a table
     *
     * @param Xyster_Db_Column $column The column to add
     * @param Xyster_Db_Table $table The table 
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addColumn( Xyster_Db_Column $column, Xyster_Db_Table $table )
    {
        $sql = "ALTER TABLE " . $this->_tableName($table) . " ADD COLUMN " .
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
        $this->_checkForeignKeySupport();
        
        $sql = "ALTER TABLE " . $this->_tableName($fk->getTable()) .
            " ADD " . $this->_constraintName($fk) . ' FOREIGN KEY ' .
            $this->_quote($fk->getColumns()) . " REFERENCES " . 
            $this->_quote($fk->getReferencedTable()->getName()) . " " .
            $this->_quote($fk->getReferencedColumns());
        if ( $fk->getOnDelete() !== null ) {
            $sql .= ' ON DELETE ' . $fk->getOnDelete()->getSql();
        }
        if ( $fk->getOnUpdate() !== null ) {
            $sql .= ' ON UPDATE ' . $fk->getOnUpdate()->getSql();
        }
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Creates a primary key
     *
     * @param Xyster_Db_PrimaryKey $pk
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addPrimaryKey( Xyster_Db_PrimaryKey $pk )
    {
        $sql = "ALTER TABLE " . $this->_tableName($pk->getTable()) . " ADD " .
            $this->_constraintName($pk) . " PRIMARY KEY" . 
            $this->_quote($pk->getColumns());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Adds a unique key
     * 
     * @param Xyster_Db_UniqueKey $uk
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function addUniqueKey( Xyster_Db_UniqueKey $uk )
    {
        $sql = "ALTER TABLE " . $this->_tableName($uk->getTable()) .
            " ADD " . $this->_constraintName($uk) . ' UNIQUE ' . 
            $this->_quote($uk->getColumns());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Creates a sequence
     *
     * @param string $name The name of the sequence
     * @param string $schema The schema to create it in
     * @param int $inc The amount the sequence increments
     * @param int $start The starting number for the sequence
     * @param int $min The sequence minimum
     * @param int $max The sequence maximum
     * @throws Xyster_Db_Schema_Exception if the database doesn't support sequences
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createSequence( $name, $schema=null, $inc=null, $start=null, $min=null, $max=null )
    {
        $this->_checkSequenceSupport();
        
        $rname = ($schema !== null) ? $this->_quote($schema) . '.' : '';
        $rname .= $this->_quote($name);
        
        $sql = "CREATE SEQUENCE " . $rname;
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
        $this->getAdapter()->query($this->_getSqlForCreateTable($table));
        
        // create the indexes
        foreach( $table->getIndexes() as $index ) {
            /* @var $index Xyster_Db_Index */
            $this->createIndex($index);
        }
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " DROP COLUMN " .
           $this->_quote($column->getName());
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
        $this->_checkForeignKeySupport();
        
        $sql = "ALTER TABLE " . $this->_tableName($fk->getTable()) .
            " DROP CONSTRAINT " . $this->_quote($fk->getName());
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
            ' DROP CONSTRAINT ' . $this->_quote($pk->getName());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Drops a sequence
     *
     * @param string $name The name of the sequence to drop
     * @param string $schema Optional. The schema name
     * @throws Xyster_Db_Schema_Exception if the database doesn't support sequences
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropSequence( $name, $schema = null )
    {
        $this->_checkSequenceSupport();
        
        $rname = ($schema !== null) ? $this->_quote($schema) . '.' : '';
        $rname .= $this->_quote($name);
        
        $this->getAdapter()->query("DROP SEQUENCE " . $rname);
    }
    
    /**
     * Drops a table
     *
     * @param Xyster_Db_Table $table The table to drop
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropTable( Xyster_Db_Table $table )
    {
        $this->getAdapter()->query("DROP TABLE " . $this->_tableName($table));
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
     * Lists the sequence names in the given database (or schema)
     *
     * @param string $schema Optional. The schema used to locate sequences.
     * @return array of sequence names
     */
    public function listSequences( $schema = null )
    {
        $this->_checkSequenceSupport();
    }
        
    /**
     * Renames a sequence
     * 
     * The SQL-2003 standard doesn't allow for renaming sequences, not all
     * databases support this capability.
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     * @param string $schema The schema name
     * @throws Xyster_Db_Schema_Exception if the database doesn't support sequences
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameSequence( $old, $new, $schema = null )
    {
        $this->_checkSequenceSupport();
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
        $sql = "ALTER TABLE " . $this->_tableName($table) . " ALTER COLUMN " .
            $this->_quote($column->getName()) . " SET DEFAULT " .
            $this->getAdapter()->quote($default);
        $this->getAdapter()->query($sql);
        $column->setDefaultValue($default);
    }
    
    /**
     * Whether the database supports foreign keys
     *
     * @return boolean
     */
    public function supportsForeignKeys()
    {
        return true;
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
     * Converts the database-reported data type into a DataType enum
     *
     * @param string $sqlType The database data type
     * @return Xyster_Db_DataType
     */
    public function toDataType( $sqlType )
    {
        $sqlType = strtoupper($sqlType);
        
        $blobTypes = array('BINARY', 'BYTEA', 'IMAGE', 'VARBINARY', 'BLOB',
            'GRAPHIC', 'VARGRAPHIC', 'DBCLOB', 'BIT VARYING', 'VARBIT', 'RAW',
            'LONG RAW', 'BINARY LARGE OBJECT', 'LONG VARGRAPHIC');
        $clobTypes = array('TEXT', 'NTEXT', 'LONG', 'LONGVARCHAR',
            'LONG VARCHAR', 'CHARACTER LARGE OBJECT', 'CHAR LARGE OBJECT',
            'CLOB', 'NATIONAL CHARACTER LARGE OBJECT', 'NCHAR LARGE OBJECT',
            'NCLOB');
        // omitting 'BIT' - Mysql BIT = blob, MS SQL BIT = boolean.
        $booleanTypes = array('BOOLEAN', 'BOOL');
        $charRegex = '/^(N(ATIONAL )?)?CHAR(ACTER)?2?$/';
        $varcharRegex = '/^(N(ATIONAL )?)?(VARCHAR|CHAR(ACTER)? VARYING)2?$/';
        $smallIntTypes = array('SMALLINT', 'INT2', 'TINYINT');
        $intTypes = array('INT', 'INTEGER', 'INT4');
        $bigintTypes = array('BIGINT', 'INT8');
        $decimalTypes = array('DEC', 'DECIMAL', 'NUMERIC', 'NUMBER',
            'MONEY', 'SMALLMONEY'); // 'NUMBER'?  You are stupid, oracle
            
        /*
         * Note about REAL, FLOAT, and DOUBLE
         * 
         * Those that say 'FLOAT' means single-precision (32 bit) and 'REAL'
         *  means double-precision
         *      MySQL (REAL = FLOAT if REAL_AS_FLOAT is on)
         * 
         * Those that say 'FLOAT' means double-precision (64 bit) and 'REAL' 
         *  means single-precision:
         *      Pgsql
         *      MS SQL
         *      DB2
         *      Oracle
         *      SQLite (which has no 4-byte float)
         */
        $realTypes = array('REAL', 'FLOAT4', 'BINARY_FLOAT');
        $floatTypes = array('BINARY_DOUBLE', 'FLOAT', 'FLOAT8', 'DOUBLE',
            'DOUBLE PRECISION');
        
        if ( in_array($sqlType, $blobTypes) ) {
            return Xyster_Db_DataType::Blob();
        } else if ( in_array($sqlType, $clobTypes) ) {
            return Xyster_Db_DataType::Clob(); 
        } else if ( in_array($sqlType, $booleanTypes) ) {
            return Xyster_Db_DataType::Boolean();
        } else if ( preg_match($charRegex, $sqlType) ) {
            return Xyster_Db_DataType::Char();
        } else if ( preg_match($varcharRegex, $sqlType) ) {
            return Xyster_Db_DataType::Varchar();
        } else if ( in_array($sqlType, $smallIntTypes) ) {
            return Xyster_Db_DataType::Smallint();
        } else if ( in_array($sqlType, $intTypes) ) {
            return Xyster_Db_DataType::Integer();
        } else if ( in_array($sqlType, $bigintTypes) ) {
            return Xyster_Db_DataType::Bigint();
        } else if ( in_array($sqlType, $decimalTypes) ) {
            return Xyster_Db_DataType::Decimal();
        } else if ( in_array($sqlType, $realTypes) ) {
            return Xyster_Db_DataType::Real();
        } else if ( in_array($sqlType, $floatTypes) ) {
            return Xyster_Db_DataType::Float();
        } else if ( $sqlType == 'DATE' ) {
            return Xyster_Db_DataType::Date();
        } else if ( $sqlType == 'TIME' ) {
            return Xyster_Db_DataType::Time();
        } else if ( strpos($sqlType, 'DATETIME') !== false ||
            strpos($sqlType, 'TIMESTAMP') !== false ) {
            return Xyster_Db_DataType::Timestamp();
        } else if ( $sqlType == 'SERIAL' ) {
            return Xyster_Db_DataType::Identity();
        }
        
        require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('No data type could be determined for ' . $sqlType);
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
        $sql = strtoupper($type->getName());
        if ( $type === Xyster_Db_DataType::Char() || $type === Xyster_Db_DataType::Varchar() ) {
            $sql .= '(' . $column->getLength() . ')';
        } else if ( $type === Xyster_Db_DataType::Decimal() ) {
            $sql .= '(' . $column->getPrecision();
            if ( $column->getScale() ) {
                $sql .= ',' . $column->getScale();
            }
            $sql .= ')';
        }
        return $sql;
    }
    
    /**
     * Creates a new index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    abstract public function createIndex( Xyster_Db_Index $index );
        
    /**
     * Drops an index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    abstract public function dropIndex( Xyster_Db_Index $index );
    
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
    abstract public function getForeignKeys( $table = null, $schema = null );
    
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
    abstract public function getIndexes( $table = null, $schema = null );
    
    /**
     * Gets the primary keys for a specific table if one exists 
     *
     * @param string $table The table name
     * @param string $schema Optional. The schema name
     * @return Xyster_Db_PrimaryKey or null if one doesn't exist
     */
    abstract public function getPrimaryKey( $table, $schema = null );
    
    /**
     * Gets the unique keys for a specific table (and optionally schema)
     *
     * @param string $table The table name
     * @param string $schema
     * @return array of {@link Xyster_Db_UniqueKey} objects
     */
    abstract public function getUniqueKeys( $table, $schema = null );
    
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
    abstract public function renameColumn( Xyster_Db_Column $column, $newName, Xyster_Db_Table $table );
    
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
    abstract public function renameIndex( Xyster_Db_Index $index, $newName );
    
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
    abstract public function renameTable( Xyster_Db_Table $table, $newName );
    
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
    abstract public function setNull( Xyster_Db_Column $column, Xyster_Db_Table $table, $null = true );
    
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
    abstract public function setType( Xyster_Db_Column $column, Xyster_Db_Table $table, Xyster_Db_DataType $type, $length=null, $precision=null, $scale=null );

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
            if ( !$column->isNullable() ) {
               $sql .= ' NOT NULL ';
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
     * Gets the constraint name if it has one
     *
     * @param Xyster_Db_Constraint $constraint
     * @return string
     */
    protected function _constraintName( Xyster_Db_Constraint $constraint )
    {
        return $constraint->getName() ?
            ' CONSTRAINT ' . $this->_quote($constraint->getName()) : '';
    }
    
    /**
     * Gets a Lazy-loading table 
     *
     * @param string $name
     * @param string $schema
     * @return Xyster_Db_Table_Lazy
     */
    protected function _getLazyTable( $name, $schema = null )
    {
        $key = $schema . '.' . $name;
        if ( !array_key_exists($key, $this->_lazyTables) ) {
            require_once 'Xyster/Db/Table/Lazy.php';
            $this->_lazyTables[$key] = new Xyster_Db_Table_Lazy($this, $name, $schema);
        }
        return $this->_lazyTables[$key]; 
    }
    
    /**
     * Gets whether the foreign key is ready to be created
     *
     * @param Xyster_Db_ForeignKey $fk
     * @return boolean
     */
    protected function _isForeignKeyValid( Xyster_Db_ForeignKey $fk )
    {
        return $fk->getTable() !== null && $fk->getReferencedTable() !== null &&
            count($fk->getReferencedColumns()) > 0 &&
            count($fk->getColumnSpan()) > 0; 
    }
    
    /**
     * Gets the full schema-qualified name for a table
     *
     * @param Xyster_Db_Table $table
     * @return string
     */
    protected function _tableName( Xyster_Db_Table $table )
    {
        $name = $this->_quote($table->getName()); 
        if ( $table->getSchema() ) {
            $name = $this->_quote($table->getSchema()) . '.' . $name;
        }
        return $name;
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
                $sql .= $this->getAdapter()->quoteIdentifier((string)$item); 
            }
            $sql .= ')';
        } else {
            $sql = $this->getAdapter()->quoteIdentifier((string)$thing);
        }
        return $sql;
    }
        
    /**
     * Sets the database adapter
     * 
     * Each class should implement a public setAdapter method with a type hint
     * for the exact type of adapter expected.  For instance, the PostgreSQL
     * schema adapter will want a Zend_Db_Adapter_Pdo_Pgsql for its adapter. 
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @return Xyster_Db_Schema_Abstract provides a fluent interface
     */
    protected function _setAdapter( Zend_Db_Adapter_Abstract $db )
    {
        $this->_db = $db;
        return $this;
    }
   
    /**
     * Checks that the database system supports sequences
     *
     * @throws Xyster_Db_Schema_Exception if it doesn't support sequences
     */
    protected function _checkSequenceSupport()
    {
        if ( !$this->supportsSequences() ) {
            require_once 'Xyster/Db/Schema/Exception.php';
            throw new Xyster_Db_Schema_Exception('This database does not support sequences');
        }
    }
    
    /**
     * Checks that the database system supports foreign keys
     *
     * @throws Xyster_Db_Schema_Exception if it doesn't support foreign keys
     */
    protected function _checkForeignKeySupport()
    {
        if ( !$this->supportsForeignKeys() ) {
            require_once 'Xyster/Db/Schema/Exception.php';
            throw new Xyster_Db_Schema_Exception('This database does not support foreign keys');
        }
    }
}