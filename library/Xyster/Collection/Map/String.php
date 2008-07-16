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
 * @see Xyster_Collection_Map_Abstract
 */
require_once 'Xyster/Collection/Map/Abstract.php';
/**
 * A simple string key-based map
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Map_String extends Xyster_Collection_Map_Abstract
{
	/**
	 * The map array
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
	 * 
	 */
	public function clear()
	{
		$this->_items = array();
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
		return in_array($item, $this->_items, true);
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
	    require_once 'Xyster/Collection/Set.php';
	    $c = new Xyster_Collection_Set;
	    foreach( array_keys($this->_items) as $key ) {
	        $c->add($key);
	    }
	    return Xyster_Collection::fixedSet($c);
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
		return array_search($value, $this->_items, true);
	}
	
	/**
	 * Gets all keys for the value supplied
	 *
	 * @param mixed $value The value for which to search
	 * @return Xyster_Collection_Set_Interface
	 */
	public function keysFor( $value )
	{
        require_once 'Xyster/Collection/Set.php';
		$c = new Xyster_Collection_Set;
		foreach( array_keys($this->_items, $value, true) as $key ) {
		    $c->add($key);
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
		if ( !is_scalar($key) ) {
		    require_once 'Xyster/Collection/Exception.php';
			throw new Xyster_Collection_Exception("Only strings can be keys in this map");
		}
		return array_key_exists($key, $this->_items);
	}
	
	/**
	 * Gets the value at a specified key
	 *
	 * @param object $key The index to get
	 * @return mixed The value found at $key or null if none
	 */
	public function offsetGet( $key )
	{
		return $this->offsetExists($key) ? $this->_items[$key] : null;
	}
	
	/**
	 * Sets the value at a given key.
	 *
	 * @param object $key The key to set
	 * @param mixed $value The value to set
	 * @throws Xyster_Collection_Exception if the collection cannot contain the value
	 */
	public function offsetSet( $key, $value )
	{
		if ( !is_scalar($key) ) {
			require_once 'Xyster/Collection/Exception.php';
            throw new Xyster_Collection_Exception("Only strings can be keys in this map");
		}
		$this->_items[$key] = $value;
	}
	
	/**
	 * Removes a value at the specified key
	 *
	 * @param object $key The key to "unset"
	 */
	public function offsetUnset( $key )
	{
		if ( $this->offsetExists($key) ) {
			unset($this->_items[$key]);
		}
	}
	
	/**
	 * Puts the items in this collection into an array
	 * 
	 * @return array The items in this collection
	 */
	public function toArray()
	{
		return array() + $this->_items;
	}
	
	/**
	 * Gets the values contained in this map
	 *
	 * @return Xyster_Collection_Interface
	 */
	public function values()
	{
		return Xyster_Collection::using($this->_items, true);
	}
}