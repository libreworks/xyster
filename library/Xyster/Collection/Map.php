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
 * Xyster_Collection_Map_Abstract
 */
require_once 'Xyster/Collection/Map/Abstract.php';
/**
 * Xyster_Collection_Map_Entry
 */
require_once 'Xyster/Collection/Map/Entry.php';
/**
 * Implementation of a key-based collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Map extends Xyster_Collection_Map_Abstract
{
	/**
	 * The container for the {@link Xyster_Collection_Map_Entry} objects
	 * 
	 * @var array
	 */
	protected $_items = array();
	
	/**
	 * Whether this map is immutable
	 * 
	 * @var boolean
	 */
	private $_immutable = false;

	/**
	 * Creates a new map with object keys
	 *
	 * @param Xyster_Collection_Map_Interface $map The values to add to this map
	 * @param boolean $immutable
	 */
	public function __construct( Xyster_Collection_Map_Interface $map = null, $immutable = false )
	{
	    if ( $map ) {
		    $this->merge($map);
	    }
		$this->_immutable = $immutable;
	}

	/**
	 * Removes all items from the map
	 *
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function clear()
	{
		$this->_failIfImmutable();
		$this->_items = array();
	}
	
	/**
	 * Gets the number of items in the map
	 * 
	 * @return int The number of items
	 */
	public function count()
	{
		return count($this->_items);
	}
	
	/**
	 * Gets an iterator for the keys and values in this set
	 * 
	 * @return Iterator
	 */
	public function getIterator()
	{
		return ( $this->count() ) ? 
			new Xyster_Collection_Iterator($this->_items) : new EmptyIterator();
	}
	
	/**
	 * Gets all keys contained in this map
	 * 
	 * @return Xyster_Collection_Set_Interface The keys in this map
	 */
	public function keys()
	{
	    $keys = new Xyster_Collection();
	    foreach( $this->_items as $entry ) {
	        $keys->add($entry->getKey());
	    }
	    return new Xyster_Collection_Set($keys, true);
	}
	
	/**
	 * Gets the first key found for the value supplied
	 * 
	 * This method could return null if the key is null, or if the key was not
	 * found.  Use {@link containsKey} to check which is true.
	 *
	 * @param mixed $value
	 * @return mixed The key found, or false if none
	 */
	public function keyFor( $value )
	{
		foreach( $this->_items as $entry ) {
			if ( $entry->getValue() === $value ) {
				return $entry->getKey();
			}
		}
		return false;
	}
	
	/**
	 * Gets all keys for the value supplied
	 *
	 * @param mixed $value The value for which to search
	 * @return Xyster_Collection_Set_Interface
	 */
	public function keysFor( $value )
	{
		$c = new Xyster_Collection();
	    foreach( $this->_items as $entry ) {
			if ( $entry->getValue() === $value ) {
				$c->add($entry->getKey());
			}
		}
		return new Xyster_Collection_Set($c, true);
	}
	
	/**
	 * Gets whether the specified key exists in the map
	 *
	 * @param object $key The key to test
	 * @return boolean Whether the key is in the map
	 */
	public function offsetExists( $key )
	{
		if ( !is_object($key) ) {
			throw new InvalidArgumentException("Only objects can be keys in this map");
		}
		return array_key_exists(spl_object_hash($key), $this->_items);
	}
	
	/**
	 * Gets the value at a specified key
	 *
	 * @param object $key The index to get
	 * @return mixed The value found at $key or null if none
	 */
	public function offsetGet( $key )
	{
		return $this->offsetExists($key) ? 
			$this->_items[spl_object_hash($key)]->getValue() : null;
	}
	
	/**
	 * Sets the value at a given key.
	 *
	 * @param object $key The key to set
	 * @param mixed $value The value to set
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function offsetSet( $key, $value )
	{
		$this->_failIfImmutable();
		if ( !$this->offsetExists($key) )  {
			$index = spl_object_hash($key);
			$entry = new Xyster_Collection_Map_Entry($key, $value);
			$this->_items[$index] = $entry;
		} else {
			$index = spl_object_hash($key);
			$this->_items[$index]->setValue($value);
		}
	}
	
	/**
	 * Removes a value at the specified key
	 *
	 * @param object $key The key to "unset"
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function offsetUnset( $key )
	{
		$this->_failIfImmutable();
		if ( $this->offsetExists($key) ) {
			unset($this->_items[spl_object_hash($key)]);
		}
	}
	
	/**
	 * Puts the items in this map into an array
	 * 
	 * This array contains objects with the key and value available.
	 * 
	 * @return array The items in this map
	 */
	public function toArray()
	{
		return array_values($this->_items);
	}
	
	/**
	 * Gets the values contained in this map
	 *
	 * @return Xyster_Collection_Interface
	 */
	public function values()
	{
		$values = array();
		foreach( $this->_items as $entry ) {
		    $values[] = $entry->getValue();
		}
		return Xyster_Collection::using($values, true);
	}
	
	/**
	 * A convenience method to fail on modification of immutable collection
	 *
	 * @throws Xyster_Collection_Exception if the collection is immutable
	 */
	private function _failIfImmutable()
	{
	    if ( $this->_immutable ) {
	        require_once 'Xyster/Collection/Exception';
			throw new Xyster_Collection_Exception("This collection cannot be changed");
		} 
	}
}