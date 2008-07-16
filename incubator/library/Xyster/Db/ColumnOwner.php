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
 * @see Xyster_Db_Column
 */
require_once 'Xyster/Db/Column.php';
/**
 * An abstract class for database objects that contain one or more columns
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_ColumnOwner
{
    /**
     * @var Xyster_Db_Column[]
     */
    protected $_columns = array();
    
    /**
     * @var array
     */
    protected $_columnNames = array();
    
    /**
     * @var string
     */
    protected $_name;
    
    /**
     * Adds a column to the object if it's not already contained
     *
     * @param Xyster_Db_Column $column The column
     * @return Xyster_Db_Constraint provides a fluent interface
     */
    public function addColumn( Xyster_Db_Column $column )
    {
        $name = strtolower($column->getName());
        if ( !in_array($name, $this->_columnNames) ) {
            $this->_columns[] = $column;
            $this->_columnNames[] = $name;
        }
        return $this;
    }
    
    /**
     * Whether this object contains the column specified
     *
     * @param Xyster_Db_Column $column The column to check
     * @return boolean
     */
    public function containsColumn( Xyster_Db_Column $column )
    {
        return in_array(strtolower($column->getName()), $this->_columnNames);
    }
    
    /**
     * Gets the column by index or null
     *
     * @param int $index
     * @return Xyster_Db_Column
     */
    public function getColumn( $index )
    {
        return ( array_key_exists($index, $this->_columns) ) ?
            $this->_columns[$index] : null;
    }
    
    /**
     * Gets the columns in the object
     *
     * @return array of {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        return array_values($this->_columns);
    }
    
    /**
     * Gets the number of columns in this object
     *
     * @return int
     */
    public function getColumnSpan()
    {
        return count($this->_columns);
    }
    
    /**
     * Gets the name of this object
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Sets the name of this object
     *
     * @param string $value
     * @return Xyster_Db_ColumnOwner provides a fluent interface
     */
    public function setName( $value )
    {
        $this->_name = $value;
        return $this;
    }
}