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
 * An index for the tablebuilder
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_TableBuilder_Index
{
    protected $_name;
	protected $_columns = array();
	protected $_fulltext = false;
	
	/**
	 * Creates a new index
	 *
	 * @param array $columns
	 * @param string $name 
	 * @param boolean $fulltext
	 */
	public function __construct( array $columns, $name, $fulltext=false )
	{
		$this->_columns = $columns;
        $this->_name = $name;
		$this->_fulltext = $fulltext;
	}
	
	/**
	 * Gets an array of the column names in the index
	 *
	 * @return array 
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
	
	/**
	 * Gets the name of the index
	 *
	 * @return array
	 */
	public function getName()
	{
	    return $this->_name;
	}
	
	/**
	 * Returns true if the index is fulltext
	 *
	 * @return boolean
	 */
	public function isFulltext()
	{
		return $this->_fulltext;
	}
}