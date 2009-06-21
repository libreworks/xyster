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
 * An abstraction layer for schema manipulation in Oracle Database Server 
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
        /*if ( !strcasecmp($sqlType, 'BIT') ) {
            return Xyster_Db_DataType::Boolean();
        }*/
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