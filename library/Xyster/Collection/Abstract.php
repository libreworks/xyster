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
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * Xyster_Collection_Interface
 */
require_once 'Xyster/Collection/Interface.php';
/**
 * Abstract class for collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Abstract implements Xyster_Collection_Interface
{
    /**
     * The actual collection entries
     *
     * @var array
     */
	protected $_items = array();

	/**
	 * Adds an item to the collection
	 *
	 * @param mixed $item The item to add
	 * @return boolean Whether the collection changed as a result of this method
	 */
	public function add( $item )
	{
		$this->_items[] = $item;
		return true;
	}
	
	/**
	 * Removes all items from the collection
	 */
	public function clear()
	{
		$this->_items = array();
	}
	
	/**
	 * Tests to see whether the collection contains the value supplied
	 * 
	 * If the supplied value is an object, the comparison will be done for 
	 * identity (===) and not for value (==).
	 *
	 * @param mixed $item The item to test
	 * @return boolean Whether the collection contains the supplied value   
	 */
	public function contains( $item )
	{
		return in_array($item, $this->_items, true);
	}
	
	/**
	 * Tests to see whether the collection contains all of the supplied values
	 *
	 * @param Xyster_Collection_Interface $values The values to test
	 * @return boolean Whether the collection contains all of the supplied values
	 */
	public function containsAll( Xyster_Collection_Interface $values )
	{
		foreach( $values as $v ) {
			if ( !$this->contains($v) ) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Tests to see whether the collection contains any of the supplied values
	 * 
	 * Basically, implementations can safely return true on the first item that
	 * is found.
	 * 
	 * @param Xyster_Collection_Interface $values The values to test
	 * @return boolean Whether the collection contains any of the supplied values
	 */
	public function containsAny( Xyster_Collection_Interface $values )
	{
		foreach( $values as $v ) {
			if ( $this->contains($v) ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Gets the number of items in the collection
	 * 
	 * @return int The number of items
	 */
	public function count()
	{
		return count($this->_items);
	}
	
	/**
	 * Gets an iterator for the values in the collection
	 *
	 * @return SeekableIterator
	 */
	public function getIterator()
	{
		return count($this->_items) ?
			new ArrayIterator(array_values($this->_items)) : new EmptyIterator;
	}
	
	/**
	 * Tests to see if the collection contains no elements
	 * 
	 * The return value from this method should be equivalent to 
	 * <code>( $collection->count() == 0 )</code>.
	 *
	 * @return boolean Whether this collection has no elements
	 */
	public function isEmpty()
	{
		return $this->count() == 0;
	}
	
	/**
	 * Merges the values from the supplied collection into this one
	 *
	 * @param Xyster_Collection_Interface $values
	 * @return boolean Whether the collection changed as a result of this method
	 */
	public function merge( Xyster_Collection_Interface $values )
	{
		$before = count($this);
		foreach( $values as $v ) {
			$this->add($v);
		}
		return $this->count() != $before;
	}
	
	/**
	 * Removes the specified value from the collection
	 *
	 * @param mixed $item The value to remove
	 * @return boolean If the value was in the collection
	 */
	public function remove( $item )
	{
		$before = $this->count();
		foreach( $this->_items as $key=>$value ) {
			if ( $value === $item ) {
				unset($this->_items[$key]);
			}
		}
		return $this->count() != $before;		
	}
	
	/**
	 * Removes all of the specified values from the collection
	 *
	 * @param Xyster_Collection_Interface $values The values to remove
	 * @return boolean Whether the collection changed as a result of this method
	 */
	public function removeAll( Xyster_Collection_Interface $values )
	{
		$before = $this->count();
		foreach( $this->_items as $key=>$value ) {
			if ( $values->contains($value) ) {
				unset($this->_items[$key]);
			}
		}
		return $this->count() != $before;
	}
	
	/**
	 * Removes all values from the collection except for the ones specified
	 * 
	 * If the collection doesn't contain any of the values supplied, it should
	 * simply be emptied.
	 *
	 * @param Xyster_Collection_Interface $values The values to keep
	 * @return boolean Whether the collection changed as a result of this method
	 */
	public function retainAll( Xyster_Collection_Interface $values )
	{
		$before = $this->count();
		if ( !$values->containsAny($this) ) {
			$this->clear();
			return true;
		}
		foreach( $this->_items as $key=>$value ) {
			if ( !$values->contains($value) ) {
				unset($this->_items[$key]);
			}
		}
		return $this->count() != $before;
	}
	
	/**
	 * Puts the items in this collection into an array
	 * 
	 * @return array The items in this collection
	 */
	public function toArray()
	{
		return array_values($this->_items);
	}
	
	/**
	 * Converts the collection into a string
	 *
	 * @magic
	 * @return string
	 */
	public function __toString()
	{
	    try {
	        return '[' . implode(',', $this->_items) . ']';
	    } catch ( Exception $e ) {
	        return '[???]';
	    }
	}
}