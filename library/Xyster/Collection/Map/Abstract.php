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
 * @see Xyster_Collection_Map_Interface
 */
require_once 'Xyster/Collection/Map/Interface.php';
/**
 * Abstract class for key-based collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Collection_Map_Abstract implements Xyster_Collection_Map_Interface
{
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
		foreach( $this->values() as $value ) {
			if ( $item === $value ) {
				return true;
			}
		}
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
	 * @return boolean Whether the map changed as a result of this method
	 */
	public function merge( Xyster_Collection_Map_Interface $map )
	{
		$changed = false;
		foreach( $map as $key=>$value ) {
		    if ( $value instanceof Xyster_Collection_Map_Entry ) {
		        if ( !$changed && $this->get($value->getKey()) !== $value->getValue() ) {
		            $changed = true;
		        }
		        $this->set($value->getKey(), $value->getValue());
		    } else {
    			if ( !$changed && $this->get($key) !== $value ) {
				    $changed = true;
			    }
			    $this->set($key, $value);
		    }
		}
		return $changed;
	}
	
	/**
	 * Removes the mapping for the specified key
	 * 
	 * This method is an alias to ArrayAccess::offsetUnset
	 *
	 * @param mixed $key
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
	 */
	public function set( $key, $value )
	{
		$this->offsetSet($key,$value);
	}
}