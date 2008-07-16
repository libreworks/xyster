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
 * @version   $Id$
 */
/**
 * @see Xyster_Collection
 */
require_once 'Xyster/Collection.php';
/**
 * @see Xyster_Collection_Map_Abstract
 */
require_once 'Xyster/Collection/Map/Abstract.php';
/**
 * @see Xyster_Collection_Map_Entry
 */
require_once 'Xyster/Collection/Map/Entry.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * @see Xyster_Collection_Iterator
 */
require_once 'Xyster/Collection/Iterator.php';
/**
 * Implementation of a key-based collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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
	 * Creates a new map with object keys
	 *
	 * @param Xyster_Collection_Map_Interface $map The values to add to this map
	 */
	public function __construct( Xyster_Collection_Map_Interface $map = null )
	{
	    if ( $map ) {
		    $this->merge($map);
	    }
	}

	/**
	 * Removes all items from the map
	 */
	public function clear()
	{
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
	    return ( !$this->isEmpty() ) ? 
			new Xyster_Collection_Iterator($this->_items) : new EmptyIterator();
	}
	
	/**
	 * Gets all keys contained in this map
	 * 
	 * @return Xyster_Collection_Set_Interface The keys in this map
	 */
	public function keys()
	{
	    require_once 'Xyster/Collection/Set.php';
	    $keys = new Xyster_Collection_Set();
	    foreach( $this->_items as $entry ) {
	        $keys->add($entry->getKey());
	    }
	    return Xyster_Collection::fixedSet($keys);
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
		$c = new Xyster_Collection_Set();
	    foreach( $this->_items as $entry ) {
			if ( $entry->getValue() === $value ) {
				$c->add($entry->getKey());
			}
		}
		return Xyster_Collection::fixedSet($c);
	}
	
	/**
	 * Gets whether the specified key exists in the map
	 *
	 * @param object $key The key to test
	 * @return boolean Whether the key is in the map
	 * @throws Xyster_Collection_Exception if the key type is incorrect
	 */
	public function offsetExists( $key )
	{
		return array_key_exists(Xyster_Type::hash($key), $this->_items);
	}
	
	/**
	 * Gets the value at a specified key
	 *
	 * @param object $key The index to get
	 * @return mixed The value found at $key or null if none
	 */
	public function offsetGet( $key )
	{
	    $hash = Xyster_Type::hash($key);
	    return array_key_exists($hash, $this->_items) ? 
			$this->_items[$hash]->getValue() : null;
	}
	
	/**
	 * Sets the value at a given key.
	 *
	 * @param object $key The key to set
	 * @param mixed $value The value to set
	 */
	public function offsetSet( $key, $value )
	{
	    $index = Xyster_Type::hash($key);
		if ( !array_key_exists($index, $this->_items) )  {
			$this->_items[$index] = new Xyster_Collection_Map_Entry($key, $value);
		} else {
			$this->_items[$index]->setValue($value);
		}
	}
	
	/**
	 * Removes a value at the specified key
	 *
	 * @param object $key The key to "unset"
	 */
	public function offsetUnset( $key )
	{
	    $hash = Xyster_Type::hash($key);
		if ( array_key_exists($hash, $this->_items) ) {
			unset($this->_items[$hash]);
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
		$values = new Xyster_Collection;
		foreach( $this->_items as $entry ) {
		    $values->add($entry->getValue());
		}
		return Xyster_Collection::fixedCollection($values);
	}
}