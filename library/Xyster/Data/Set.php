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
	 * @param Xyster_Collection_Interface $values  Any traversable type
	 */
	public function __construct( Xyster_Collection_Interface $values=null )
	{
	    $this->_columns = new Xyster_Collection_Set();
	    if ( $values ) {
			if ( !count($this->_columns) ) {
			    foreach( $values as $value ) {
			        $first = $value;
			        break;
			    }
				if ( !is_array($first) && !is_object($first) ) {
			        require_once 'Xyster/Data/Set/Exception.php';
				    throw new Xyster_Data_Set_Exception('This set can only contain arrays or objects');
				}
				foreach( $first as $k=>$v ) { 
					$this->addColumn($k);
				}
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
	    return parent::add($item);
	}
	/**
	 * Adds a column to the set
	 *
	 * @param string $column  The name of the column
	 * @throws Xyster_Data_Set_Exception if the method is called after the set contains items
	 */
	public function addColumn( $column )
	{
		if ( count($this->_items) ) {
			require_once 'Xyster/Data/Set/Exception.php';
			throw new Xyster_Data_Set_Exception('Columns cannot be added once items have been added');
		}
		$this->_columns[$column] = Xyster_Data_Field::named($column);
	}
	/**
	 * Perform an aggregate function on a column
	 *
	 * @param Xyster_Data_Aggregate $function
	 * @param Xyster_Data_Column|string $field
	 * @return mixed
	 */
	public function aggregate( Xyster_Data_Aggregate $function, $field )
	{
		switch( strtoupper($function->getValue()) ) {
			case "MAX":
			    $value = max($this->fetchColumn($field)); 
				break;
			case "MIN":
			    $value = min($this->fetchColumn($field));
				break;
			case "COUNT":
				$value = count($this->_items);
				break;
			case "SUM":
			    $value = array_sum($this->fetchColumn($field));
				break;
			case "AVG":
				$value = array_sum($this->fetchColumn($field))/count($this->_items);
				break;
			default: break;
		}
		return $value;
	}
	/**
	 * Converts an entire column of values to an array
	 *
	 * @param string $name
	 * @return array
	 */
	public function fetchColumn( $name )
	{
		$column = Xyster_Data_Field::named($name);
		$values = array();
		foreach( $this->_items as $v )
			$values[] = $column->evaluate($v);
		return $values;
	}
	/**
	 * Returns the first column of the first row of a statement
	 *
	 * @return mixed  The value of the first column in the first row
	 */
	public function fetchOne()
	{
		$value = null;
		if ( $item = reset($this->_items) ) {
			foreach( $item as $k=>$v ) {
				$value = $v;
				break;
			}
		}
		return $value;
	}
	/**
	 * Converts 2 columns into an associative array
	 *
	 * @return array  The translated columns
	 */
	public function fetchPairs( $key, $value )
	{
		$column1 = Xyster_Data_Field::named($key);
		$column2 = Xyster_Data_Field::named($value);
		$pairs = array();
		foreach( $this->_items as $v ) 
			$pairs[ $column1->evaluate($v) ] = $column2->evaluate($v);
        return $pairs;
	}
	/**
	 * Removes elements in the collection based on a criteria
	 *
	 * @param Xyster_Data_Criterion $criteria  The criteria for filtering
	 */
	public function filter( Xyster_Data_Criterion $criteria )
	{
	    $this->_items = array_filter( $this->_items, array( $criteria, 'evaluate' ) );
	}
	/**
	 * Sorts the collection by one or more columns and directions
	 * 
	 * The $sorts parameter can either be an array of {@link Xyster_Data_Sort}
	 * objects, an array of strings containing column and direction, or a
	 * comma-delimited list of columns and directions.
	 * 
	 * @param array $sorts  The sorts to include 
	 */
	public function sortBy( $sorts )
	{
		if ( !is_array($sorts) ) {
			$sorts = Xyster_String::smartSplit(',',$sorts);
		}
		$values = array();
	    require_once 'Xyster/Data/Sort.php';
		foreach( $sorts as $v ) { 
			$values[] = ( $v instanceof Xyster_Data_Sort ) ?
			    $v : Xyster_Data_Sort::parse($v);
		}
		require_once 'Xyster/Data/Comparator.php';
		$this->sort( new Xyster_Data_Comparator($values) );
	}
}