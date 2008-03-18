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
	/**
	 * Creates a new column
	 *
	 * @param string $name
	 * @param Xyster_Db_Gateway_DataType $type
	 */
	public function __construct( $name, Xyster_Db_Gateway_DataType $type )
	{
		
	}
	
	/**
	 * Gets the argument for the data type
	 *
	 * @return mixed
	 */
	public function getArgument()
	{
		
	}
	
	/**
	 * Gets the name of the column
	 *
	 * @return string
	 */
	public function getName()
	{
		
	}
	
	/**
	 * Gets the data type of the column
	 * 
	 * @return Xyster_Db_Gateway_DataType
	 */
	public function getDataType()
	{
		
	}
	
	/**
	 * Gets the default value for the column
	 *
	 * @return mixed
	 */
	public function getDefault()
	{
		
	}
	
	/**
	 * Gets the table name for the foreign key
	 * 
	 * @return string
	 */
	public function getForeignKeyTable()
	{
		
	}
	
	/**
	 * Gets the column name for the foreign key
	 *
	 * @return string
	 */
	public function getForeignKeyColumn()
	{
		
	}
	
	/**
	 * Gets the action to perform ondelete
	 *
	 * @return mixed
	 */
	public function getForeignKeyOnDelete()
	{
		
	}
	
	/**
	 * Gets the action to perform onupdate
	 *
	 * @return mixed
	 */
	public function getForeignKeyOnUpdate()
	{
		
	}
	
	/**
	 * Returns true if the column is a foreign key
	 *
	 * @return boolean
	 */
	public function isForeign()
	{
		
	}
	
	/**
	 * Returns true if the column is fulltext indexed
	 *
	 * @return boolean
	 */
	public function isFulltext()
	{
		
	}
	
	/**
	 * Returns true if the column is a primary key
	 * 
	 * @return boolean
	 */
	public function isPrimary()
	{
		
	}
	
	/**
	 * Returns true if the column is indexed
	 * 
	 * @return boolean
	 */
	public function isIndexed()
	{
		
	}
	
	/**
	 * Returns true if the column is uniquely indexed
	 *
	 * @return boolean
	 */
	public function isUnique()
	{
		
	}
}