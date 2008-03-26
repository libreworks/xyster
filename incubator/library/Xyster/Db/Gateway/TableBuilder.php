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
 * @see Xyster_Db_Gateway_DataType
 */
require_once 'Xyster/Db/Gateway/DataType.php';
/**
 * @see Xyster_Db_Gateway_TableBuilder_Column
 */
require_once 'Xyster/Db/Gateway/TableBuilder/Column.php';
/**
 * A builder for table creation values
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_TableBuilder
{
	/**
	 * @var string
	 */
    protected $_name;
    
    /**
     * @var Xyster_Db_Gateway_Abstract
     */
    protected $_gateway;
    
    /**
     * @var Xyster_Db_Gateway_TableBuilder_Column
     */
    protected $_current;
    
    protected $_columns = array();
    
    protected $_foreign = array();
    
    protected $_indexes = array();
    
    protected $_primary;
    
    protected $_uniques = array();
    

    /**
     * Creates a new table builder
     *
     * @param string $name The table name
     * @param Xyster_Db_Gateway_Abstract $gateway
     */
    public function __construct( $name, Xyster_Db_Gateway_Abstract $gateway )
    {
    	$this->_name = $name;
    	$this->_gateway = $gateway;
    }
    
    /**
     * Adds a VARCHAR data type column
     *
     * @param string $name The column name
     * @param int $length
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addVarchar( $name, $length )
    {
    	$this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
    	   Xyster_Db_Gateway_DataType::Varchar(), $length);
    	$this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a CHAR data type column
     *
     * @param string $name The column name
     * @param int $length
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addChar( $name, $length )
    {
    	$this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Char(), $length);
        $this->_columns[] = $this->_current;
        return $this;
    }
    
    /**
     * Adds an INTEGER/INT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addInteger( $name )
    {
    	$this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Integer());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a SMALLINT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addSmallint( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Smallint());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a FLOAT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addFloat( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Float());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a TIMESTAMP data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addTimestamp( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Timestamp());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a DATE data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addDate( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Date());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a TIME data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addTime( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Time());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a CLOB/TEXT data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addClob( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Clob());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a BLOB/IMAGE data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addBlob( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Blob());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds a BOOLEAN data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addBoolean( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Boolean());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Adds an IDENTITY/SERIAL data type column
     *
     * @param string $name The column name
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addIdentity( $name )
    {
        $this->_current = new Xyster_Db_Gateway_TableBuilder_Column($name,
           Xyster_Db_Gateway_DataType::Identity());
        $this->_columns[] = $this->_current;
    	return $this;
    }
    
    /**
     * Sets a default value for the current column
     *
     * @param mixed $default The default value
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function defaultValue( $default )
    {
        $this->_checkColumnDefined();
        $this->_current->defaultValue($default);
    	return $this;
    }
    
    /**
     * Executes the create table statement
     *
     */
    public function execute()
    {
    	$this->_gateway->createTableExecute($this);
    }
    
    /**
     * Sets the current column as being a foreign key
     *
     * @param string $table The foreign table name
     * @param string $column The foreign column name
     * @param Xyster_Db_Gateway_ReferentialAction $onDelete optional
     * @param Xyster_Db_Gateway_ReferentialAction $onUpdate optional
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function foreign( $table, $column, Xyster_Db_Gateway_ReferentialAction $onDelete=null, Xyster_Db_Gateway_ReferentialAction $onUpdate=null )
    {
        $this->_checkColumnDefined();
        $this->_current->foreign($table, $column, $onDelete, $onUpdate);
    	return $this;
    }
    
    /**
     * Sets the table as having a compound foreign key
     *
     * @param array $columns The columns in the current table
     * @param string $table The foreign table name
     * @param array $foreignColumns The columns in the foreign table
     * @param Xyster_Db_Gateway_ReferentialAction $onDelete optional
     * @param Xyster_Db_Gateway_ReferentialAction $onUpdate optional
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function foreignMulti( array $columns, $table, array $foreignColumns, Xyster_Db_Gateway_ReferentialAction $onDelete=null, Xyster_Db_Gateway_ReferentialAction $onUpdate=null )
    {
    	require_once 'Xyster/Db/Gateway/TableBuilder/ForeignKey.php';
    	$this->_foreign[] = new Xyster_Db_Gateway_TableBuilder_ForeignKey($columns,
    	   $table, $foreignColumns, $onDelete, $onUpdate);
    	return $this;
    }
    
    /**
     * Gets the columns that have been defined in this table
     *
     * @return array An array of {@link Xyster_Db_Gateway_TableBuilder_Column} objects
     */
    public function getColumns()
    {
    	$columns = array();
    	foreach( $this->_columns as $column ) {
    		$columns[] = clone $column;
    	}
    	return $columns;
    }
    
    /**
     * Gets the foreign keys that have been defined at the table level
     * 
     * Only foreign keys that have been defined using the {@link foreignMulti}
     * method will be returned here.
     * 
     * @return array An array of {@link Xyster_Db_Gateway_TableBuilder_ForeignKey} objects
     */
    public function getForeignKeys()
    {
    	return array() + $this->_foreign;
    }
    
    /**
     * Gets the indexes that have been defined at the table level
     * 
     * Only indexes that have been defined using the {@link indexMulti} method
     * will be returned here.
     * 
     * @return array An array of {@link Xyster_Db_Gateway_TableBuilder_Index} objects
     */
    public function getIndexes()
    {
    	return array() + $this->_indexes;
    }
    
    /**
     * Gets the name of the table
     *
     * @return string
     */
    public function getName()
    {
    	return $this->_name;
    }
    
    /**
     * Gets the primary keys that have been defined at the table level
     * 
     * Only primary keys that have been defined using the {@link primaryMulti}
     * method will be returned here.
     *
     * @return Xyster_Db_Gateway_TableBuilder_PrimaryKey the composite key used
     */
    public function getPrimaryKey()
    {
    	return $this->_primary;
    }
    
    /**
     * Gets the unique indexes that have been defined at the table level
     * 
     * Only uniques that have been defined using the {@link uniqueMulti} method
     * will be returned here.
     *
     * @return array An array of {@link Xyster_Db_Gateway_TableBuilder_Unique} objects
     */
    public function getUniques()
    {
    	return array() + $this->_uniques;
    }
    
    /**
     * Sets the current column as being indexed
     *
     * @param string $name The name of the index
     * @param boolean $fulltext If the index is fulltext
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function index( $name=null, $fulltext=false )
    {
        $this->_checkColumnDefined();
        $this->indexMulti(array($this->_current->getName()), $name, $fulltext);
    	return $this;
    }
    
    /**
     * Adds a compound index to the table
     *
     * @param array $columns The columns to include
     * @param string $name The name of the index
     * @param boolean $fulltext If the index is fulltext
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function indexMulti( array $columns, $name=null, $fulltext=false )
    {
    	require_once 'Xyster/Db/Gateway/TableBuilder/Index.php';
    	$this->_indexes[] = new Xyster_Db_Gateway_TableBuilder_Index($columns, $name, $fulltext);
    	return $this;
    }
    
    /**
     * Sets the current column as being able to accept null or not
     *
     * @param boolean $null True for NULL, false for NOT NULL
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function null( $null=true )
    {
        $this->_checkColumnDefined();
        $this->_current->null($null);
    	return $this;
    }
    
    /**
     * Sets the current column as being the primary key
     * 
     * This is unnecessary for columns of type IDENTITY
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function primary()
    {
        $this->_checkColumnDefined();
        $this->_current->primary();
    	return $this;
    }
    
    /**
     * Sets the table as having a compound primary key
     *
     * @param array $columns The columns in the key
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function primaryMulti( array $columns )
    {
    	require_once 'Xyster/Db/Gateway/TableBuilder/PrimaryKey.php';
    	$this->_primary = new Xyster_Db_Gateway_TableBuilder_PrimaryKey($columns);
    	return $this;
    }
    
    /**
     * Sets the current column as being indexed uniquely
     *
     * @return Xyster_Db_Gateway_TableBuilder provides a fluent interface
     */
    public function unique()
    {
    	$this->_checkColumnDefined();
    	$this->_current->unique();
    	return $this;
    }
    
    /**
     * Adds a compound unique index to the table
     *
     * @param array $columns The columns to include
     * @return Xyster_Db_Gateway_TableBuilder provides a fluent interface
     */
    public function uniqueMulti( array $columns )
    {
    	require_once 'Xyster/Db/Gateway/TableBuilder/Unique.php';
    	$this->_uniques[] = new Xyster_Db_Gateway_TableBuilder_Unique($columns);
        return $this;
    }
    
    /**
     * Checks that a column has been defined
     *
     * @throws Xyster_Db_Gateway_TableBuilder_Exception if one isn't
     */
    protected function _checkColumnDefined()
    {
    	if ( $this->_current === null ) {
    		require_once 'Xyster/Db/Gateway/TableBuilder/Exception.php';
    		throw new Xyster_Db_Gateway_TableBuilder_Exception('A column must be defined before this method can be used');
    	}
    }
}