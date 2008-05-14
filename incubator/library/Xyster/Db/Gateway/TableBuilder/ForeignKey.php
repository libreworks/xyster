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
 * A foreign key for the tablebuilder
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_TableBuilder_ForeignKey
{
	protected $_columns = array();
	protected $_table;
	protected $_fcolumns = array();
	protected $_onDelete;
	protected $_onUpdate;
	
	/**
	 * Creates a new foreign key
	 *
	 * @param array $columns
	 * @param string $table
	 * @param array $foreignColumns
	 * @param Xyster_Db_ReferentialAction $onDelete (optional)
	 * @param Xyster_Db_ReferentialAction $onUpdate (optional)
	 */
	public function __construct( array $columns, $table, array $foreignColumns, Xyster_Db_ReferentialAction $onDelete=null, Xyster_Db_ReferentialAction $onUpdate=null )
	{
		$this->_columns = $columns;
		$this->_table = $table;
		$this->_fcolumns = $foreignColumns;
		$this->_onDelete = $onDelete;
		$this->_onUpdate = $onUpdate;
	}
	
	/**
	 * Gets the columns in the local table that reference the foreign one 
	 *
	 * @return array An array of string column names
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
	
    /**
     * Gets the referenced columns in the foreign table 
     *
     * @return array An array of string column names
     */
    public function getForeignColumns()
    {
    	return $this->_fcolumns;
    }
    
    /**
     * Gets the onDelete behavior
     *
     * @return Xyster_Db_ReferentialAction
     */
    public function getOnDelete()
    {
    	return $this->_onDelete;
    }
    
    /**
     * Gets the onUpdate behavior
     *
     * @return Xyster_Db_ReferentialAction
     */
    public function getOnUpdate()
    {
    	return $this->_onUpdate;
    }
    
    /**
     * Gets the table name of the foreign table
     *
     * @return string The table name
     */
    public function getTable()
    {
    	return $this->_table;
    }
}