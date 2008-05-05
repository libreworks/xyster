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
 * @see Xyster_Db_Gateway_Builder
 */
require_once 'Xyster/Db/Gateway/Builder.php';
/**
 * @see Xyster_Data_Sort
 */
require_once 'Xyster/Data/Sort.php';
/**
 * A builder for indexes
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_IndexBuilder extends Xyster_Db_Gateway_Builder
{
    protected $_columns = array();
    
    protected $_fulltext = false;
    
    protected $_table;
    
    protected $_unique = false;
    
    /**
     * Executes the create table statement
     *
     */
    public function execute()
    {
        if ( !count($this->_columns) ) {
            require_once 'Xyster/Db/Gateway/Exception.php';
            throw new Xyster_Db_Gateway_Exception('This index must reference at least one column');
        }
        $this->_getGateway()->executeIndexBuilder($this);
    }
    
    /**
     * Sets the index to be full text
     * 
     * An index cannot be both full text and unique
     *
     * @param boolean $fulltext
     * @return Xyster_Db_Gateway_IndexBuilder provides a fluent interface
     */
    public function fulltext( $fulltext=true )
    {
        $this->_unique = false;
        $this->_fulltext = $fulltext;
        return $this;
    }
    
    /**
     * Gets the columns in the index
     *
     * @return array an array with {@link Xyster_Data_Sort} objects
     */
    public function getColumns()
    {
        return array() + $this->_columns;
    }
    
    /**
     * Gets the table name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     * Whether the index is full text
     *
     * @return boolean
     */
    public function isFulltext()
    {
        return $this->_fulltext;
    }
    
    /**
     * Whether the index is UNIQUE
     *
     * @return boolean
     */
    public function isUnique()
    {
        return $this->_unique;
    }

    /**
     * Assigns the index to reference a table and columns
     * 
     * The schema name should be specified in the IndexBuilder constructor.
     * 
     * The columns array should either be an array with column names as values
     * or an array with column names as keys and either "ASC" or "DESC" for 
     * each value (indicating the sort order of the index).  You can actually
     * mix and match:
     * 
     * <pre>array(
     *     'my_column',
     *     'another_column' => 'DESC',
     *     'foobar_column',
     *     'final_column' => 'ASC',
     * );
     * </pre>  
     *
     * @param string $table
     * @param array $columns
     * @return Xyster_Db_Gateway_IndexBuilder provides a fluent interface
     */
    public function on( $table, array $columns )
    {
        $this->_table = $table;
        $accepted = array('ASC', 'DESC');
        foreach( $columns as $key => $value ) {
            $column = ( in_array(strtoupper($value), $accepted) ) ? $key : $value;
            $this->_columns[] = ( strtoupper($value) == 'DESC' ) ?
                Xyster_Data_Sort::desc($column) : Xyster_Data_Sort::asc($column);
        }
        return $this;
    }
    
    /**
     * Sets the index to be UNIQUE
     * 
     * An index cannot be both full text and unique
     * 
     * @param boolean $unique
     * @return Xyster_Db_Gateway_IndexBuilder provides a fluent interface
     */
    public function unique( $unique=true )
    {
        $this->_fulltext = false;
        $this->_unique = $unique;
        return $this;
    }
}