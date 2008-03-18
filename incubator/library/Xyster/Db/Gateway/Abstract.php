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
        
    }
    
    /**
     * Creates a table builder
     *
     * @param string $name The name of the table
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function createTable( $name )
    {
         
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
     * @param string $name The optional foreign key name
     */
    public function dropForeign( $table, $name=null )
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
}