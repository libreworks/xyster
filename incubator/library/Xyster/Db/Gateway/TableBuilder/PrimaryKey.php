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
 * A primary key for the tablebuilder
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_TableBuilder_PrimaryKey
{
	protected $_columns = array();
	
	/**
	 * Creates a new primary key
	 *
	 * @param array $columns
	 */
	public function __construct( array $columns )
	{
		$this->_columns = $columns;
	}
	
	/**
	 * Gets the columns in the primary key 
	 *
	 * @return array An array of string column names
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
}