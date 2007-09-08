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
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * Xyster_Collection_List_Abstract
 */
require_once 'Xyster/Collection/List/Abstract.php';
/**
 * Simple implementation of an index-based collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_List extends Xyster_Collection_List_Abstract
{
    private $_immutable;

	/**
	 * Creates a new list
	 *
	 * @param Xyster_Collection_Interface $values Any values to add to this list
	 * @param boolean $immutable Whether the set can be changed
	 */
	public function __construct( Xyster_Collection_Interface $values = null, $immutable = null )
	{
	    if ( $values ) {
		    $this->merge($values);
	    }
		$this->_immutable = $immutable;
	}

	/**
	 * Adds an item to the collection
	 *
	 * @param mixed $item The item to add
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function add( $item )
	{
		$this->_failIfImmutable();
		return parent::add($item);
	}
	
	/**
	 * Removes all items from the collection
	 *
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function clear()
	{
		$this->_failIfImmutable();
		parent::clear();
	}
	
	/**
	 * Inserts a value into the list at the specified index
	 * 
	 * {@inherit} 
	 *
	 * @param int $index The index at which to insert
	 * @param mixed $value The value to insert
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	public function insert( $index, $value )
	{
		$this->_failIfImmutable();
		parent::insert($index, $value);
	}
	
	/**
	 * Inserts the supplied values into the list at the specified index
	 * 
	 * {@inherit} 
	 * 
	 * @param int $index The index at which to insert
	 * @param Xyster_Collection_Interface $values The value to insert
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	public function insertAll( $index, Xyster_Collection_Interface $values )
	{
		$this->_failIfImmutable();
		parent::insertAll($index, $values);
	}
	
	/**
	 * Sets the value at a given index.
	 * 
	 * The index must be greater than or equal to 0 and less than or equal to
	 * the size of this collection.  In other words, an index is valid if  
	 * <code>( $index < 0 || $index > $this->count() )</code> is false.
	 *
	 * @param int $index The index to set
	 * @param mixed $value The value to set
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	public function offsetSet( $index, $value )
	{
		$this->_failIfImmutable();
		parent::offsetSet($index, $value);
	}
	
	/**
	 * Removes a value at the specified index
	 * 
	 * {@inherit}
	 *
	 * @param int $index The index to "unset"
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	public function offsetUnset( $index )
	{
		$this->_failIfImmutable();
		parent::offsetUnset($index);
	}
	
	/**
	 * Removes the specified value from the collection
	 *
	 * @param mixed $item The value to remove
	 * @return boolean If the value was in the collection
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function remove( $item )
	{
		$this->_failIfImmutable();
		return parent::remove($item);
	}
	
	/**
	 * Removes all of the specified values from the collection
	 *
	 * @param Xyster_Collection_Interface $values The values to remove
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function removeAll( Xyster_Collection_Interface $values )
	{
		$this->_failIfImmutable();
		return parent::removeAll($values);
	}
	
	/**
	 * Removes all values from the collection except for the ones specified
	 *
	 * @param Xyster_Collection_Interface $values The values to keep
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function retainAll( Xyster_Collection_Interface $values )
	{
		$this->_failIfImmutable();
		return parent::retainAll($values);
	}
	
	/**
	 * Removes $count elements starting at $from 
	 *
	 * @param int $from The starting index
	 * @param int $count The number of elements to remove
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 * @throws OutOfBoundsException if $from is invalid
	 */
	public function slice( $from, $count )
	{
		$this->_failIfImmutable();
		parent::slice($from, $count);
	}
	
	/**
	 * A convenience method to fail on modification of immutable collection
	 *
	 * @throws Xyster_Collection_Exception if the collection is immutable
	 */
	private function _failIfImmutable()
	{
	    if ( $this->_immutable ) {
	        require_once 'Xyster/Collection/Exception.php';
			throw new Xyster_Collection_Exception("This collection cannot be changed");
		} 
	}
}