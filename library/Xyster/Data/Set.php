<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * Xyster_Collection_Set_Sortable
 */
require_once 'Xyster/Collection/Set/Sortable.php';
/**
 * A set that holds rows and columns
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Set extends Xyster_Collection_Set_Sortable
{
    /**
     * @var Xyster_Collection_Set
     */
    protected $_columns;

    /**
     * Creates a new data set
     *
     * @param Xyster_Collection_Interface $values The values to add
     */
    public function __construct( Xyster_Collection_Interface $values = null )
    {
        $this->_columns = new Xyster_Collection_Set();

        if ( $values instanceof Xyster_Collection_Interface ) {
            if ( !count($this->_columns) && count($values) ) {
                foreach( $values as $value ) {
                    $first = $value;
                    break;
                }
                if ( !is_array($first) && !is_object($first) ) {
                    require_once 'Xyster/Data/Set/Exception.php';
                    throw new Xyster_Data_Set_Exception('This set can only contain arrays or objects');
                }
                $this->_createColumnsFromItem($first);
            }
            $this->merge($values);
        }
    }

    /**
     * Adds an item to the set
     * 
     * This collection doesn't accept duplicate values, and will return false
     * if the provided value is already in the collection.
     * 
     * It can only accept arrays or objects as items, otherwise it will throw a 
     * Xyster_Data_Set_Exception.
     *
     * @param mixed $item The item to add
     * @return boolean Whether the set changed as a result of this method
     * @throws Xyster_Data_Set_Exception if the collection cannot contain the value
     * @throws BadMethodCallException if the collection cannot be modified
     */
    public function add( $item )
    {
        if ( !is_array($item) && !is_object($item) ) {
            require_once 'Xyster/Data/Set/Exception.php';
            throw new Xyster_Data_Set_Exception('This set can only contain arrays or objects');
        }
        if ( !count($this->_columns) ) {
            $this->_createColumnsFromItem($item);
        }
        return parent::add($item);
    }
    /**
     * Adds a column to the set
     *
     * @param string $column The name of the column
     * @throws Xyster_Data_Set_Exception if the method is called after the set contains items
     */
    public function addColumn( $column )
    {
        if ( count($this->_items) ) {
            require_once 'Xyster/Data/Set/Exception.php';
            throw new Xyster_Data_Set_Exception('Columns cannot be added once items have been added');
        }
        $this->_columns->add(Xyster_Data_Field::named($column));
    }
    /**
     * Perform an aggregate function on a column
     *
     * @param Xyster_Data_Aggregate $function
     * @param Xyster_Data_Field|string $field
     * @return mixed
     */
    public function aggregate( Xyster_Data_Field_Aggregate $field )
    {
        $function = $field->getFunction();
        switch( strtoupper($function->getValue()) ) {
            case "MAX":
                $value = max($this->fetchColumn($field)); 
                break;
            case "MIN":
                $value = min($this->fetchColumn($field));
                break;
            case "SUM":
                $value = array_sum($this->fetchColumn($field));
                break;
            case "AVG":
                $value = array_sum($this->fetchColumn($field))/count($this->_items);
                break;
            case "COUNT":
            default:
                $value = count($this->_items);
        }
        return $value;
    }
    /**
     * Converts an entire column of values to an array
     *
     * @param mixed $field A {@link Xyster_Data_Field} or the name of the field to fetch
     * @return array The fetched values from the column
     */
    public function fetchColumn( $field )
    {
        if (! $field instanceof Xyster_Data_Field ) {
            $field = Xyster_Data_Field::named($field);
        }
        $values = array();
        foreach( $this->_items as $v ) {
            $values[] = $field->evaluate($v);
        }
        return $values;
    }
    /**
     * Returns the first column of the first row of a statement
     *
     * @return mixed The value of the first column in the first row
     */
    public function fetchOne()
    {
        $value = null;
        if ( $item = current($this->_items) ) {
            foreach( $item as $k=>$v ) {
                $value = $v;
                break;
            }
        }
        return $value;
    }
    /**
     * Converts 2 fields into an associative array
     *
     * @param mixed $key A {@link Xyster_Data_Field} or the name of the field to fetch
     * @param mixed $value A {@link Xyster_Data_Field} or the name of the field to fetch
     * @return array The translated fields
     */
    public function fetchPairs( $key, $value )
    {
        $field1 = (! $key instanceof Xyster_Data_Field) ?
            Xyster_Data_Field::named($key) : $key;
        $field2 = (! $value instanceof Xyster_Data_Field) ? 
            Xyster_Data_Field::named($value) : $value;
        $pairs = array();
        foreach( $this->_items as $v ) {
            $pairs[ $field1->evaluate($v) ] = $field2->evaluate($v);
        }
        return $pairs;
    }
    /**
     * Removes elements in the collection based on a criteria
     *
     * @param Xyster_Data_Criterion $criteria The criteria for filtering
     */
    public function filter( Xyster_Data_Criterion $criteria )
    {
        $this->_items = array_filter($this->_items, array($criteria, 'evaluate'));
    }
    /**
     * Gets the columns that have been added to the set
     *
     * @return Xyster_Collection_Set
     */
    public function getColumns()
    {
        return Xyster_Collection::fixedSet($this->_columns);
    }
    /**
     * Sorts the collection by one or more columns and directions
     * 
     * The $sorts parameter can either be a single {@link Xyster_Data_Sort} or
     * an array containing multiple.
     * 
     * @param mixed $sorts The sorts to include 
     */
    public function sortBy( $sorts )
    {
        if ( !is_array($sorts) ) {
            $sorts = array($sorts);
        }

        foreach( $sorts as $v ) {
            if (! $v instanceof Xyster_Data_Sort ) {
                require_once 'Xyster/Data/Set/Exception.php';
                throw new Xyster_Data_Set_Exception('The argument must be one or more Xyster_Data_Sort objects');
            }
        }

        require_once 'Xyster/Data/Comparator.php';
        $this->sort( new Xyster_Data_Comparator($sorts) );
    }

    /**
     * Creates the columns for this set from an array or object
     * 
     * @param mixed $item either an array or an object
     */
    protected function _createColumnsFromItem( $item )
    {
        foreach( $item as $k => $v ) { 
            $this->addColumn($k);
        }
    }
}