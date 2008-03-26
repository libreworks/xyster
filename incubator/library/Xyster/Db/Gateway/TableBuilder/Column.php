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
 * A column for the tablebuilder
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_TableBuilder_Column
{
	protected $_name;
	protected $_type;
	protected $_argument;
	protected $_default;
	protected $_null = true;
	/**
	 * @var Xyster_Db_Gateway_TableBuilder_ForeignKey
	 */
	protected $_foreign;
	protected $_primary = false;
	protected $_unique = false;
	
	/**
	 * Creates a new column
	 *
	 * @param string $name The column name
	 * @param Xyster_Db_Gateway_DataType $type The data type
	 * @param mixed $argument An optional argument for the data type
	 */
	public function __construct( $name, Xyster_Db_Gateway_DataType $type, $argument=null )
	{
		$this->_name = $name;
		$this->_type = $type;
		$this->_argument = $argument;
	}
	
	/**
	 * Gets the argument for the data type
	 *
	 * @return mixed
	 */
	public function getArgument()
	{
		return $this->_argument;
	}
	
	/**
	 * Gets the name of the column
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Gets the data type of the column
	 * 
	 * @return Xyster_Db_Gateway_DataType
	 */
	public function getDataType()
	{
		return $this->_type;
	}
	
	/**
	 * Gets the default value for the column
	 *
	 * @return mixed
	 */
	public function getDefault()
	{
		return $this->_default;
	}
	
	/**
	 * Gets the table name for the foreign key
	 * 
	 * @return string
	 */
	public function getForeignKeyTable()
	{
		return $this->isForeign() ? $this->_foreign->getTable() : null;
	}
	
	/**
	 * Gets the column name for the foreign key
	 *
	 * @return string
	 */
	public function getForeignKeyColumn()
	{
		return $this->isForeign() ? current($this->_foreign->getForeignColumns()) : null;
	}
	
	/**
	 * Gets the action to perform ondelete
	 *
	 * @return Xyster_Db_Gateway_ReferentialAction
	 */
	public function getForeignKeyOnDelete()
	{
		return $this->isForeign() ? $this->_foreign->getOnDelete() : null;
	}
	
	/**
	 * Gets the action to perform onupdate
	 *
	 * @return Xyster_Db_Gateway_ReferentialAction
	 */
	public function getForeignKeyOnUpdate()
	{
		return $this->isForeign() ? $this->_foreign->getOnUpdate() : null;
	}
	
	/**
	 * Returns true if the column is a foreign key
	 *
	 * @return boolean
	 */
	public function isForeign()
	{
		return $this->_foreign !== null;
	}
	
	/**
	 * Returns true if the column is a primary key
	 * 
	 * @return boolean
	 */
	public function isPrimary()
	{
		return $this->_primary;
	}
	
	/**
	 * Returns true if the column allows null values
	 *
	 * @return boolean
	 */
	public function isNull()
	{
		return $this->_null;
	}
	
	/**
	 * Returns true if the column is uniquely indexed
	 *
	 * @return boolean
	 */
	public function isUnique()
	{
		return $this->_unique;
	}
    
	/**
	 * Sets the default value for the column
	 *
	 * @param mixed $value
	 */
	public function defaultValue( $value )
	{
		$this->_default = $value;
	}
	
	/**
	 * Sets the column to be a foreign key
	 *
	 * @param string $table
	 * @param string $column
	 * @param Xyster_Db_Gateway_ReferentialAction $onDelete
	 * @param Xyster_Db_Gateway_ReferentialAction $onUpdate
	 */
    public function foreign( $table, $column, Xyster_Db_Gateway_ReferentialAction $onDelete=null, Xyster_Db_Gateway_ReferentialAction $onUpdate=null )
    {
    	require_once 'Xyster/Db/Gateway/TableBuilder/ForeignKey.php';
        $this->_foreign = new Xyster_Db_Gateway_TableBuilder_ForeignKey(array($this->_name),
            $table, array($column), $onDelete, $onUpdate);
    }
    
    /**
     * Sets whether the column can accept null
     *
     * @param boolean $allow True for NULL, false for NOT NULL
     */
    public function null( $allow=true )
    {
    	$this->_null = $allow;
    }
    	
	/**
	 * Sets the column to be a primary key
	 *
	 */
	public function primary()
	{
		$this->_primary = true;
	}
	
	/**
	 * Sets the column to be uniquely indexed
	 *
	 */
	public function unique()
	{
		$this->_unique = true;
	}
}