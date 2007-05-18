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
 * Xyster_Collection_Interface
 */
require_once 'Xyster/Collection/Interface.php';
/**
 * Index-based collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Collection_List_Interface extends Xyster_Collection_Interface, ArrayAccess
{
	/**
	 * Gets the value at a specified index
	 * 
	 * This method is an alias to ArrayAccess::offsetGet
	 * 
	 * The index must be greater than or equal to 0 and less than or equal to
	 * the size of this collection.  In other words, an index is valid if  
	 * <code>( $index < 0 || $index > count($list) )</code> is false.
	 *
	 * @param int $index The index to get
	 * @return mixed The value found at $index
	 * @throws OutOfBoundsException if the index is invalid
	 */
	function get( $index );
	/**
	 * Returns the first index found for the specified value
	 *
	 * @param mixed $value
	 * @return int The first index found, or null if the value isn't contained
	 */
	function indexOf( $value );
	/**
	 * Inserts a value into the list at the specified index
	 * 
	 * The index must be greater than or equal to 0 and less than or equal to
	 * the size of this collection.  In other words, an index is valid if  
	 * <code>( $index < 0 || $index > count($list) )</code> is false.  
	 *
	 * @param int $index The index at which to insert
	 * @param mixed $value The value to insert
	 * @throws InvalidArgumentException if the collection cannot contain the value
	 * @throws BadMethodCallException if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	function insert( $index, $value );
	/**
	 * Inserts the supplied values into the list at the specified index
	 * 
	 * The index must be greater than or equal to 0 and less than or equal to
	 * the size of this collection.  In other words, an index is valid if  
	 * <code>( $index < 0 || $index > count($list) )</code> is false.  
	 * 
	 * @param int $index The index at which to insert
	 * @param Xyster_Collection_Interface $values The value to insert
	 * @throws InvalidArgumentException if the collection cannot contain a value
	 * @throws BadMethodCallException if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	function insertAll( $index, Xyster_Collection_Interface $values );
	/**
	 * Removes a value at the specified index
	 * 
	 * This method is an alias to ArrayAccess::offsetUnset
	 * 
	 * The index must be greater than or equal to 0 and less than or equal to
	 * the size of this collection.  In other words, an index is valid if  
	 * <code>( $index < 0 || $index > count($list) )</code> is false.  
	 *
	 * @param int $index The index to "unset"
	 * @return mixed The value removed
	 * @throws BadMethodCallException if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	function removeAt( $index );
	/**
	 * Sets the value at a given index.
	 * 
	 * This method is an alias to ArrayAccess::offsetSet.
	 * 
	 * The index must be greater than or equal to 0 and less than or equal to
	 * the size of this collection.  In other words, an index is valid if  
	 * <code>( $index < 0 || $index > count($list) )</code> is false.
	 *
	 * @param int $index The index to set
	 * @param mixed $value The value to set
	 * @throws InvalidArgumentException if the collection cannot contain the value
	 * @throws BadMethodCallException if the collection cannot be modified
	 * @throws OutOfBoundsException if the index is invalid
	 */
	function set( $index, $value );
	/**
	 * Removes all elements between $from and $to, including $from.
	 *
	 * @param int $from The starting index
	 * @param int $to The index to end before reaching
	 * @throws BadMethodCallException if the collection cannot be modified
	 * @throws OutOfBoundsException if either $from or $to is invalid
	 */
	function slice( $from, $to );
}