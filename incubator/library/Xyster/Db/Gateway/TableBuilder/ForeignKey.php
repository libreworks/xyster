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
	/**
	 * Creates a new foreign key
	 *
	 * @param array $columns
	 * @param string $table
	 * @param array $foreignColumns
	 * @param mixed $onDelete
	 * @param mixed $onUpdate
	 */
	public function __construct( array $columns, $table, array $foreignColumns, $onDelete=null, $onUpdate=null )
	{
		
	}
	
	/**
	 * Gets the columns in the local table that reference the foreign one 
	 *
	 * @return array An array of string column names
	 */
	public function getColumns()
	{
	}
	
    /**
     * Gets the referenced columns in the foreign table 
     *
     * @return array An array of string column names
     */
    public function getForeignColumns()
    {
    }
    
    /**
     * Gets the onDelete behavior
     *
     * @return mixed
     */
    public function getOnDelete()
    {
    	
    }
    
    /**
     * Gets the onUpdate behavior
     *
     * @return mixed
     */
    public function getOnUpdate()
    {
    	
    }
    
    /**
     * Gets the table name of the foreign table
     *
     * @return string The table name
     */
    public function getTable()
    {
    	
    }
}