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
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace Xyster\Data;
/**
 * A set that holds rows and columns
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Set extends \Xyster\Collection\SortableSet
{
    /**
     * @var Xyster_Collection_Set
     */
    protected $_columns;

    /**
     * Creates a new data set
     *
     * @param \Xyster\Collection\ICollection $values The values to add
     */
    public function __construct( \Xyster\Collection\ICollection $values = null )
    {
        $this->_columns = new \Xyster\Collection\Set();
        if ( $values instanceof \Xyster\Collection\ICollection ) {
            if ( !count($this->_columns) && count($values) ) {
                $first = $values->getIterator()->current();
                if ( !is_array($first) && !is_object($first) ) {
                    throw new \Xyster\Data\DataException('This set can only contain arrays or objects');
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
     * \Xyster\Data\DataException.
     *
     * @param mixed $item The item to add
     * @return boolean Whether the set changed as a result of this method
     * @throws \Xyster\Data\DataException if the collection cannot contain the value
     */
    public function add( $item )
    {
        if ( !is_array($item) && !is_object($item) ) {
            throw new \Xyster\Data\DataException('This set can only contain arrays or objects');
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
     * @throws \Xyster\Data\DataException if the method is called after the set contains items
     */
    public function addColumn( $column )
    {
        if ( count($this->_items) ) {
            throw new \Xyster\Data\DataException('Columns cannot be added once items have been added');
        }
        $this->_columns->add(Symbol\Field::named($column));
    }
    /**
     * Perform an aggregate function on a column
     *
     * @param Symbol\AggregateField $field
     * @return mixed
     */
    public function aggregate( Symbol\AggregateField $field )
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
     * @param mixed $field A {@link Symbol\Field} or the name of the field to fetch
     * @return array The fetched values from the column
     */
    public function fetchColumn( $field )
    {
        $getter = new Symbol\Evaluator($field);
        $values = array();
        foreach( $this->_items as $v ) {
            $values[] = $getter->evaluate($v);
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
            $value = current((array) $item);
        }
        return $value;
    }
    /**
     * Converts 2 fields into an associative array
     *
     * @param mixed $key A {@link Symbol\Field} or the name of the field to fetch
     * @param mixed $value A {@link Symbol\Field} or the name of the field to fetch
     * @return array The translated fields
     */
    public function fetchPairs( $key, $value )
    {
        $getter1 = new Symbol\Evaluator($key);
        $getter2 = new Symbol\Evaluator($value);
        $pairs = array();
        foreach( $this->_items as $v ) {
            $pairs[ $getter1->evaluate($v) ] = $getter2->evaluate($v);
        }
        return $pairs;
    }
    /**
     * Removes elements in the collection based on a criteria
     *
     * @param Symbol\Criterion $criteria The criteria for filtering
     */
    public function filter( Symbol\Criterion $criteria )
    {
        $this->_items = array_values(array_filter($this->_items, array($criteria, 'evaluate')));
    }
    /**
     * Gets the columns that have been added to the set
     *
     * @return Xyster_Collection_Set
     */
    public function getColumns()
    {
        return \Xyster\Collection\Collection::fixedSet($this->_columns);
    }
    /**
     * Sorts the collection by one or more columns and directions
     * 
     * The $sorts parameter can either be a single {@link Xyster\Data\Symbol\Sort} or
     * an array containing multiple.
     * 
     * @param mixed $sorts The sorts to include 
     */
    public function sortBy( $sorts )
    {
    	$param = ( $sorts instanceof Symbol\ISymbol ) ? $sorts : null;
    	
        $clause = new Symbol\SortClause($param);
        
        if ( $param === null ) {
	        if ( !is_array($sorts) && ! $sorts instanceof Traversable ) {
	        	$sorts = array($sorts);
	        }
	        if ( is_array($sorts) || $sorts instanceof Traversable ) {
	        	foreach( $sorts as $sort ) {
	        		if ( ! $sort instanceof Symbol\Sort ) {
	        			throw new \Xyster\Data\DataException('Only Xyster\Data\Symbol\Sort objects can be used');
	        		}
	        		$clause->add($sort);
	        	}
	        }
        }

        $this->sort(new Comparator($clause));
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