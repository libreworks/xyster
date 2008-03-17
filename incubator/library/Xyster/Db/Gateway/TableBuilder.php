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
     * @param int $length
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addVarchar( $length )
    {
    	return $this;
    }
    
    /**
     * Adds a CHAR data type column
     *
     * @param int $length
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addChar( $length )
    {
        return $this;
    }
    
    /**
     * Adds an INTEGER/INT data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addInteger()
    {
    	return $this;
    }
    
    /**
     * Adds a SMALLINT data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addSmallint()
    {
    	return $this;
    }
    
    /**
     * Adds a FLOAT data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addFloat()
    {
    	return $this;
    }
    
    /**
     * Adds a TIMESTAMP data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addTimestamp()
    {
    	return $this;
    }
    
    /**
     * Adds a DATE data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addDate()
    {
    	return $this;
    }
    
    /**
     * Adds a TIME data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addTime()
    {
    	return $this;
    }
    
    /**
     * Adds a CLOB/TEXT data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addClob()
    {
    	return $this;
    }
    
    /**
     * Adds a BLOB/IMAGE data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addBlob()
    {
    	return $this;
    }
    
    /**
     * Adds a BOOLEAN data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addBoolean()
    {
    	return $this;
    }
    
    /**
     * Adds an IDENTITY/SERIAL data type column
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function addIdentity()
    {
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
    	return $this;
    }
    
    /**
     * Executes the create table statement
     *
     */
    public function execute()
    {
    	
    }
    
    /**
     * Sets the current column as being a foreign key
     *
     * @param string $table The foreign table name
     * @param string $column The foreign column name
     * @param mixed $onDelete
     * @param mixed $onUpdate
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function foreign( $table, $column, $onDelete, $onUpdate )
    {
    	return $this;
    }
    
    /**
     * Sets the table as having a compound foreign key
     *
     * @param array $columns The columns in the current table
     * @param string $table The foreign table name
     * @param array $foreignColumns The columns in the foreign table
     * @param mixed $onDelete
     * @param mixed $onUpdate
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function foreignMulti( array $columns, $table, array $foreignColumns, $onDelete, $onUpdate )
    {
    	return $this;
    }
    
    /**
     * Sets the current column as being indexed
     *
     * @param boolean $fulltext If the index is fulltext
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function index( $fulltext=false )
    {
    	return $this;
    }
    
    /**
     * Adds a compound index to the table
     *
     * @param array $columns The columns to include
     * @param boolean $fulltext If the index is fulltext
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function indexMulti( array $columns, $fulltext=false )
    {
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
    	return $this;
    }
    
    /**
     * Sets the current column as being indexed uniquely
     *
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function unique()
    {
    	return $this;
    }
    
    /**
     * Adds a compound unique index to the table
     *
     * @param array $columns The columns to include
     * @return Xyster_Db_Gateway_TableBuilder
     */
    public function uniqueMulti( array $columns )
    {
        return $this;
    }
}