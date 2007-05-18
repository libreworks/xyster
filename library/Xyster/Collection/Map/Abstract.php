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
 * Xyster_Collection_Map_Interface
 */
require_once 'Xyster/Collection/Map/Interface.php';
/**
 * Abstract class for key-based collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Collection_Map_Abstract implements Xyster_Collection_Map_Interface
{
	/**
	 * Removes all items from the collection
	 * 
	 * This is a pretty basic implementation that should be overridden for 
	 * performance reasons.
	 *
	 * @throws BadMethodCallException if the collection cannot be modified
	 */
	public function clear()
	{
		foreach( $this->keys() as $key )
			$this->remove($key);
	}
	/**
	 * Tests to see whether the map contains the key supplied
	 * 
	 * If the supplied value is an object, and the map allows objects as keys,
	 * the comparison will be done for identity (===) and not for value (==).
	 * 
	 * This method is an alias to ArrayAccess::offsetExists
	 *
	 * @param mixed $key The key to test
	 * @return boolean Whether the map contains the supplied key
	 */
	public function containsKey( $key )
	{
		return $this->offsetExists($key);
	}
	/**
	 * Tests to see whether the map contains the value supplied
	 * 
	 * If the supplied value is an object, the comparison will be done for 
	 * identity (===) and not for value (==).
	 *
	 * @param mixed $item The item to test
	 * @return boolean Whether the map contains the supplied value   
	 */
	public function containsValue( $item )
	{
		foreach( $this->values() as $value )
			if ( $item === $value )
				return true;
		return false;
	}
	/**
	 * Gets the value corresponding to the supplied key
	 * 
	 * This method could return null if the value is null, or if the value was
	 * not found.  Use {@link containsValue} to check which is true.
	 *
	 * @param mixed $key
	 * @return mixed The value found, or null if none
	 */
	public function get( $key )
	{
		return $this->offsetGet($key);
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
	 * Combines this map with the supplied map
	 * 
	 * The values in the supplied map will take precedence if this map and the
	 * supplied map have duplicate keys.
	 *
	 * @param Xyster_Map_Interface $map
	 * @throws BadMethodCallException If the map cannot be modified
	 * @throws InvalidArgumentException if any of the keys or values are invalid for this map
	 * @return boolean Whether the map changed as a result of this method
	 */
	public function merge( Xyster_Map_Interface $map )
	{
		$changed = false;
		foreach( $map as $key=>$value ) {
			if ( !$changed && $this->get($key) !== $value )
				$changed = true;
			$this->set($key,$value);
		}
		return $changed;
	}
	/**
	 * Removes the mapping for the specified key
	 * 
	 * This method is an alias to ArrayAccess::offsetUnset
	 *
	 * @param mixed $key
	 * @throws BadMethodCallException If the map cannot be modified
	 */
	public function remove( $key )
	{
		$this->offsetUnset($key);
	}
	/**
	 * Sets a mapping for the key and value supplied
	 * 
	 * This method is an alias to ArrayAccess::offsetSet
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @throws BadMethodCallException If the map cannot be modified
	 */
	public function set( $key, $value )
	{
		$this->offsetSet($key,$value);
	}
	/**
	 * Puts the items in this collection into an array
	 * 
	 * For Maps that allow for scalar-only keys, this array should have the map
	 * keys as keys and values as values.  If the map allows complex types as 
	 * keys, this array should contain objects with the key and value available.
	 * 
	 * @return array The items in this collection
	 */
	public function toArray()
	{
		return iterator_to_array($this->getIterator());
	}
}