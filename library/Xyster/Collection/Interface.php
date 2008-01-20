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
 * Interface for collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Collection_Interface extends Countable, IteratorAggregate
{
	/**
	 * Adds an item to the collection
	 * 
	 * Some collections don't accept duplicate values, and should return false
	 * if the provided value is already in the collection.  If the collection is
	 * not allowed to contain the supplied value, an InvalidArgumentException
	 * should be thrown.
	 *
	 * @param mixed $item The item to add
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws InvalidArgumentException if the collection cannot contain the value
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	function add( $item );
	/**
	 * Removes all items from the collection
	 *
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	function clear();
	/**
	 * Tests to see whether the collection contains the value supplied
	 * 
	 * If the supplied value is an object, the comparison will be done for 
	 * identity (===) and not for value (==).
	 *
	 * @param mixed $item The item to test
	 * @return boolean Whether the collection contains the supplied value   
	 */
	function contains( $item );
	/**
	 * Tests to see whether the collection contains all of the supplied values
	 *
	 * @param Xyster_Collection_Interface $values The values to test
	 * @return boolean Whether the collection contains all of the supplied values
	 */
	function containsAll( Xyster_Collection_Interface $values );
	/**
	 * Tests to see whether the collection contains any of the supplied values
	 * 
	 * Basically, implementations can safely return true on the first item that
	 * is found.
	 * 
	 * @param Xyster_Collection_Interface $values The values to test
	 * @return boolean Whether the collection contains any of the supplied values
	 */
	function containsAny( Xyster_Collection_Interface $values );
	/**
	 * Tests to see if the collection contains no elements
	 * 
	 * The return value from this method should be equivalent to 
	 * <code>( $collection->count() == 0 )</code>.
	 *
	 * @return boolean Whether this collection has no elements
	 */
	function isEmpty();
	/**
	 * Merges the values from the supplied collection into this one
	 * 
	 * If the implementing collection is not allowed to contain the same value 
	 * twice, it should only add ones in $values not present in $this.  If the 
	 * implementing collection can contain duplicates, then the values can just
	 * be appended.
	 * 
	 * If the collection is not allowed to contain the supplied value, an
	 * InvalidArgumentException should be thrown.    
	 *
	 * @param Xyster_Collection_Interface $values
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws InvalidArgumentException if the collection cannot contain the value
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	function merge( Xyster_Collection_Interface $values );
	/**
	 * Removes the specified value from the collection
	 *
	 * @param mixed $item The value to remove
	 * @return boolean If the value was in the collection
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	function remove( $item );
	/**
	 * Removes all of the specified values from the collection
	 *
	 * @param Xyster_Collection_Interface $values The values to remove
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	function removeAll( Xyster_Collection_Interface $values );
	/**
	 * Removes all values from the collection except for the ones specified
	 * 
	 * If the collection doesn't contain any of the values supplied, it should
	 * simply be emptied.
	 *
	 * @param Xyster_Collection_Interface $values The values to keep
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	function retainAll( Xyster_Collection_Interface $values );
	/**
	 * Puts the items in this collection into an array
	 * 
	 * @return array The items in this collection
	 */
	function toArray();
}