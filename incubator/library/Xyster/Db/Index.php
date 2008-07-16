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
 * @see Xyster_Db_ColumnOwner
 */
require_once 'Xyster/Db/ColumnOwner.php';
/**
 * A relational database index
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Index extends Xyster_Db_ColumnOwner
{
    protected $_fulltext = false;
    
    protected $_sorts = array();
    
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
    /**
     * Adds a column to the object if it's not already contained
     *
     * @param Xyster_Db_Column $column The column
     * @return Xyster_Db_Constraint provides a fluent interface
     */
    public function addColumn( Xyster_Db_Column $column )
    {
        return $this->addSortedColumn($column->asc());
    }
    
    /**
     * Adds a sorted column to the index
     * 
     * The difference between this method and 'addColumn' is that columns added
     * with 'addColumn' are sorted ascending by default.
     *
     * @param Xyster_Data_Sort $sort
     * @return Xyster_Db_Index provides a fluent interface
     */
    public function addSortedColumn( Xyster_Data_Sort $sort )
    {
        parent::addColumn($sort->getField());
        $this->_sorts[] = $sort;
        return $this;
    }
    
    /**
     * Gets the sorted columns
     *
     * @return Xyster_Db_Index provides a fluent interface
     */
    public function getSortedColumns()
    {
        return $this->_sorts;
    }
    
    /**
     * Gets the table related to this constraint
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
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
    
    /**
     * Sets whether this index is fulltext
     *
     * @param boolean $value
     * @return Xyster_Db_Index
     */
    public function setFulltext( $value = true )
    {
        $this->_fulltext = $value;
        return $this;
    }
    
    /**
     * Sets the table related to this constraint 
     *
     * @param Xyster_Db_Table $table
     * @return Xyster_Db_Index provides a fluent interface
     */
    public function setTable( Xyster_Db_Table $table )
    {
        $this->_table = $table;
        return $this;
    }
}