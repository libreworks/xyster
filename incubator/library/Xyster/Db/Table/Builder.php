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
 * @see Xyster_Db_DataType
 */
require_once 'Xyster/Db/DataType.php';
/**
 * @see Xyster_Db_Column
 */
require_once 'Xyster/Db/Column.php';
/**
 * @see Xyster_Db_Table
 */
require_once 'Xyster/Db/Table.php';
/**
 * @see Xyster_Db_Table_Lazy
 */
require_once 'Xyster/Db/Table/Lazy.php';
/**
 * A builder for table creation values
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Table_Builder
{
    /**
     * @var Xyster_Db_Schema_Abstract
     */
    private $_db;
    
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
    /**
     * @var Xyster_Db_Column
     */
    protected $_current;
    
    /**
     * Creates a new table builder
     *
     * @param Xyster_Db_Schema_Abstract $db
     * @param string $name The object name
     * @param string $schema Optional. The schema name of the object 
     */
    public function __construct( Xyster_Db_Schema_Abstract $db, $name, $schema=null )
    {
        $this->_db = $db;
        $this->_table = new Xyster_Db_Table($name);
        $this->_table->setSchema($schema);
    }
    
    /**
     * Gets the name of the object
     *
     * @return string
     */
    public function getName()
    {
        return $this->_table->getName();
    }
    
    /**
     * Gets the schema of the object
     * 
     * @return string 
     */
    public function getSchema()
    {
        return $this->_table->getSchema();
    }
    
    /**
     * Adds a VARCHAR data type column
     *
     * @param string $name The column name
     * @param int $length
     * @return Xyster_Db_Table_Builder
     */
    public function addVarchar( $name, $length )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Varchar())
            ->setLength($length);
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a CHAR data type column
     *
     * @param string $name The column name
     * @param int $length
     * @return Xyster_Db_Table_Builder
     */
    public function addChar( $name, $length )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Char())
            ->setLength($length);
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds an INTEGER/INT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addInteger( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Integer());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a SMALLINT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addSmallint( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Smallint());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds an 64-bit FLOAT data type column (aka DOUBLE)
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addFloat( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Float());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a TIMESTAMP data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addTimestamp( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Timestamp());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a DATE data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addDate( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Date());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a TIME data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addTime( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Time());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a CLOB/TEXT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addClob( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Clob());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a BLOB/IMAGE data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addBlob( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Blob());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a BOOLEAN data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addBoolean( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Boolean());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds an IDENTITY/SERIAL data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Table_Builder
     */
    public function addIdentity( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Identity());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a BIGINT data type column
     *
     * @param string $name
     * @return Xyster_Db_Table_Builder
     */
    public function addBigint( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Bigint());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a 32-bit FLOAT data type column (aka REAL)
     *
     * @param string $name
     * @return Xyster_Db_Table_Builder
     */
    public function addReal( $name )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Real());
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Adds a DECIMAL/NUMERIC data type column
     *
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return Xyster_Db_Table_Builder
     */
    public function addDecimal( $name, $precision = null, $scale = null )
    {
        $this->_current = new Xyster_Db_Column($name);
        $this->_current->setType(Xyster_Db_DataType::Decimal())
            ->setPrecision($precision)
            ->setScale($scale);
        $this->_table->addColumn($this->_current);
        return $this;
    }
    
    /**
     * Sets a default value for the current column
     *
     * @param mixed $default The default value
     * @return Xyster_Db_Table_Builder
     */
    public function defaultValue( $default )
    {
        $this->_checkColumnDefined();
        $this->_current->setDefaultValue($default);
        return $this;
    }
    
    /**
     * Executes the create table statement
     *
     */
    public function execute()
    {
        $this->_db->createTable($this->_table);
    }
    
    /**
     * Sets the current column as being a foreign key
     *
     * @param string $table The foreign table name
     * @param string $column The foreign column name
     * @param Xyster_Db_ReferentialAction $onDelete optional
     * @param Xyster_Db_ReferentialAction $onUpdate optional
     * @return Xyster_Db_Table_Builder
     */
    public function foreign( $table, $column, $name=null, Xyster_Db_ReferentialAction $onDelete=null, Xyster_Db_ReferentialAction $onUpdate=null )
    {
        $this->_checkColumnDefined();
        return $this->foreignMulti(array($this->_current->getName()), $table,
            array($column), $name, $onDelete, $onUpdate);
    }
    
    /**
     * Sets the table as having a compound foreign key
     *
     * @param array $columnNames The columns in the current table
     * @param string $table The foreign table name
     * @param array $foreignColumns The columns in the foreign table
     * @param string $name Optional. The key name
     * @param Xyster_Db_ReferentialAction $onDelete optional
     * @param Xyster_Db_ReferentialAction $onUpdate optional
     * @return Xyster_Db_Table_Builder
     */
    public function foreignMulti( array $columnNames, $table, array $foreignColumns, $name=null, Xyster_Db_ReferentialAction $onDelete=null, Xyster_Db_ReferentialAction $onUpdate=null )
    {
        require_once 'Xyster/Db/ForeignKey.php';
        $fk = new Xyster_Db_ForeignKey;
        $fk->setTable($this->_table)->setName($name);
        if ( $onDelete ) {
            $fk->setOnDelete($onDelete);
        }
        if ( $onUpdate ) {
            $fk->setOnUpdate($onUpdate);
        }
        $columns = $this->_findOrCreateColumns($columnNames, $this->_table->getColumns());
        foreach( $columns as $column ) {
            $fk->addColumn($column);    
        }
        require_once 'Xyster/Db/Table/Lazy.php';
        $refTable = new Xyster_Db_Table_Lazy($this->_db, $table, $this->getSchema());
        $fk->setReferencedTable($refTable);
        $columns = $this->_findOrCreateColumns($foreignColumns, $refTable->getColumns());
        foreach( $columns as $column ) {
            $fk->addReferencedColumn($column);
        }
        $this->_table->addForeignKey($fk);
        return $this;
    }
    
    /**
     * Gets the columns that have been defined in this table
     *
     * @return array An array of {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        return $this->_deepClone($this->_table->getColumns());
    }
    
    /**
     * Gets the foreign keys that have been defined at the table level
     * 
     * Only foreign keys that have been defined using the {@link foreignMulti}
     * method will be returned here.
     * 
     * @return array An array of {@link Xyster_Db_Table_Builder_ForeignKey} objects
     */
    public function getForeignKeys()
    {
        return $this->_deepClone($this->_table->getForeignKeys());
    }
    
    /**
     * Gets the indexes that have been defined at the table level
     * 
     * Only indexes that have been defined using the {@link indexMulti} method
     * will be returned here.
     * 
     * @return array An array of {@link Xyster_Db_Table_Builder_Index} objects
     */
    public function getIndexes()
    {
        return $this->_deepClone($this->_table->getIndexes());
    }
    
    /**
     * Gets the options set
     *
     * @return array
     */
    public function getOptions()
    {
        return array() + $this->_table->getOptions();
    }
    
    /**
     * Gets the primary keys that have been defined at the table level
     * 
     * Only primary keys that have been defined using the {@link primaryMulti}
     * method will be returned here.
     *
     * @return Xyster_Db_Table_Builder_PrimaryKey the composite key used
     */
    public function getPrimaryKey()
    {
        $pk = $this->_table->getPrimaryKey();
        return is_object($pk) ? clone $pk : $pk;
    }
    
    /**
     * Gets the unique indexes that have been defined at the table level
     * 
     * Only uniques that have been defined using the {@link uniqueMulti} method
     * will be returned here.
     *
     * @return array An array of {@link Xyster_Db_UniqueKey} objects
     */
    public function getUniques()
    {
        return $this->_deepClone($this->_table->getUniqueKeys());
    }
    
    /**
     * Sets the current column as being indexed
     *
     * @param string $name The name of the index
     * @param boolean $fulltext If the index is fulltext
     * @return Xyster_Db_Table_Builder
     */
    public function index( $name=null, $fulltext=false )
    {
        $this->_checkColumnDefined();
        return $this->indexMulti(array($this->_current->getName()), $name, $fulltext);
    }
    
    /**
     * Adds a compound index to the table
     *
     * @param array $columnNames The columns to include
     * @param string $name The name of the index
     * @param boolean $fulltext If the index is fulltext
     * @return Xyster_Db_Table_Builder
     */
    public function indexMulti( array $columnNames, $name=null, $fulltext=false )
    {
        require_once 'Xyster/Db/Index.php';
        $index = new Xyster_Db_Index;
        $index->setTable($this->_table)->setName($name)->setFulltext($fulltext);
        $columns = $this->_findOrCreateColumns($columnNames, $this->_table->getColumns());
        foreach( $columns as $column ) {
            $index->addColumn($column);
        }
        $this->_table->addIndex($index);
        return $this;
    }
    
    /**
     * Sets the current column as being able to accept null or not
     *
     * @param boolean $null True for NULL, false for NOT NULL
     * @return Xyster_Db_Table_Builder
     */
    public function null( $null=true )
    {
        $this->_checkColumnDefined();
        $this->_current->setNullable($null);
        return $this;
    }
    
    /**
     * Sets a database option
     *
     * @param string $name
     * @param mixed $value
     * @return Xyster_Db_Table_Builder
     */
    public function option( $name, $value )
    {
        $this->_table->setOption($name, $value);
        return $this;
    }
    
    /**
     * Sets the current column as being the primary key
     * 
     * This is unnecessary for columns of type IDENTITY
     *
     * @param string $name Optional. The name of the key
     * @return Xyster_Db_Table_Builder
     */
    public function primary( $name = null )
    {
        $this->_checkColumnDefined();
        return $this->primaryMulti(array($this->_current->getName()), $name);
    }
    
    /**
     * Sets the table as having a compound primary key
     *
     * @param array $columnNames The columns in the key
     * @param string $name Optional. The name of the key
     * @return Xyster_Db_Table_Builder
     */
    public function primaryMulti( array $columnNames, $name = null )
    {
        require_once 'Xyster/Db/PrimaryKey.php';
        $pk = new Xyster_Db_PrimaryKey;
        $pk->setTable($this->_table)->setName($name);
        $columns = $this->_findOrCreateColumns($columnNames, $this->_table->getColumns());
        foreach( $columns as $column ) {
            $pk->addColumn($column);
        }
        $this->_table->setPrimaryKey($pk);
        return $this;
    }
    
    /**
     * Sets the current column as being indexed uniquely
     *
     * @param string $name Optional. The key name
     * @return Xyster_Db_Table_Builder provides a fluent interface
     */
    public function unique( $name = null )
    {
        $this->_checkColumnDefined();
        $this->_current->setUnique();
        return $this;
    }
    
    /**
     * Adds a compound unique index to the table
     *
     * @param array $columnNames The column names to include
     * @param string $name Optional. The key name
     * @return Xyster_Db_Table_Builder provides a fluent interface
     */
    public function uniqueMulti( array $columnNames, $name = null )
    {
        require_once 'Xyster/Db/UniqueKey.php';
        $uk = new Xyster_Db_UniqueKey($columns);
        $uk->setName($name)
            ->setTable($this->_table);
        $columns = $this->_findOrCreateColumns($columnNames, $this->_table->getColumns());
        foreach( $columns as $column ) {
            $uk->addColumn($column);
        }
        $this->_table->addUniqueKey($uk);
        return $this;
    }
    
    /**
     * Checks that a column has been defined
     *
     * @throws Xyster_Db_Exception if one isn't
     */
    protected function _checkColumnDefined()
    {
        if ( $this->_current === null ) {
            require_once 'Xyster/Db/Exception.php';
            throw new Xyster_Db_Exception('A column must be defined before this method can be used');
        }
    }
    
    /**
     * Deeply clones an array
     *
     * @param array $things
     * @return array
     */
    protected function _deepClone( array $things )
    {
        $new = array();
        foreach( $things as $object ) {
            $new[] = clone $object;
        }
        return $new;
    }
    
    /**
     * Goes through an array of columns and retrieves them by name, or creates them
     *
     * @param array $columnNames
     * @param array $columns
     */
    protected function _findOrCreateColumns( array $columnNames, array $columns )
    {
        $found = array();
        foreach( $columns as $column ) {
            if ( in_array($column->getName(), $columnNames) ) {
                $found[$column->getName()] = $column;
            }
        }
        if ( count($found) < count($columnNames) ) {
            foreach( $columnNames as $name ) {
                if ( !array_key_exists($name, array_keys($found)) ) {
                    $found[$name] = new Xyster_Db_Column($name);
                }
            }
        }
        return $found;
    }
}