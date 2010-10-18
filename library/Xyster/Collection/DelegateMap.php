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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Collection;
/**
 * A delegate for maps
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class DelegateMap implements IMap
{
    /**
     * @var IMap
     */
    private $_delegate;
    
    /**
     * Creates a new delegate collection
     *
     * @param IMap $delegate
     */
    public function __construct( IMap $delegate )
    {
        $this->_delegate = $delegate;
    }
    
    /**
     * Removes all items from the collection
     *
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function clear()
    {
        $this->_delegate->clear();
    }
    
    /**
     * Gets the number of items in the collection
     * 
     * @return int The number of items
     */
    public function count()
    {
        return $this->_delegate->count();
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
        return $this->_delegate->offsetExists($key);
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
        return $this->_delegate->containsValue($item);
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
        return $this->_delegate->get($key);
    }
    
    /**
     * Gets an iterator for the values in the collection
     *
     * @return \SeekableIterator
     */
    public function getIterator()
    {
        return $this->_delegate->getIterator();
    }
    
    /**
     * Gets all keys contained in this map
     * 
     * @return ISet The keys in this map
     */
    public function keys()
    {
        return $this->_delegate->keys();
    }
    
    /**
     * Gets the first key found for the value supplied
     * 
     * This method could return null if the key is null, or if the key was not
     * found.  Use {@link containsKey} to check which is true.
     *
     * @param mixed $value
     * @return mixed The key found, or null if none
     */
    public function keyFor( $value )
    {
        return $this->_delegate->keyFor($value);
    }
    
    /**
     * Gets all keys for the value supplied
     *
     * @param mixed $value The value for which to search
     * @return ISet
     */
    public function keysFor( $value )
    {
        return $this->_delegate->keysFor($value);    
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
        return $this->_delegate->isEmpty();
    }
    
    /**
     * Combines this map with the supplied map
     * 
     * The values in the supplied map will take precedence if this map and the
     * supplied map have duplicate keys.
     *
     * @param IMap $map
     * @throws UnmodifiableException If the map cannot be modified
     * @throws InvalidArgumentException if any of the keys or values are invalid for this map
     * @return boolean Whether the map changed as a result of this method
     */
    public function merge( IMap $map )
    {
        return $this->_delegate->merge($map);
    }

    /**
     * Gets whether the specified key exists in the map
     *
     * @param object $key The key to test
     * @return boolean Whether the key is in the map
     * @throws UnmodifiableException if the key type is incorrect
     */
    public function offsetExists( $key )
    {
        return $this->_delegate->offsetExists($key);
    }
    
    /**
     * Gets the value at a specified key
     *
     * @param object $key The index to get
     * @return mixed The value found at $key or null if none
     */
    public function offsetGet( $key )
    {
        return $this->_delegate->offsetGet($key);
    }
    
    /**
     * Sets the value at a given key.
     *
     * @param object $key The key to set
     * @param mixed $value The value to set
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function offsetSet( $key, $value )
    {
        $this->_delegate->offsetSet($key, $value);
    }
    
    /**
     * Removes a value at the specified key
     *
     * @param object $key The key to "unset"
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function offsetUnset( $key )
    {
        $this->_delegate->offsetUnset($key);
    }
    
    /**
     * Removes the mapping for the specified key
     * 
     * This method is an alias to ArrayAccess::offsetUnset
     *
     * @param mixed $key
     * @throws UnmodifiableException If the map cannot be modified
     */
    public function remove( $key )
    {
        $this->_delegate->offsetUnset($key);
    }
    
    /**
     * Sets a mapping for the key and value supplied
     * 
     * This method is an alias to ArrayAccess::offsetSet
     *
     * @param mixed $key
     * @param mixed $value
     * @throws UnmodifiableException If the map cannot be modified
     */
    public function set( $key, $value )
    {
        $this->_delegate->offsetSet($key, $value);
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
        return $this->_delegate->toArray();
    }
    
    /**
     * Gets the values contained in this map
     *
     * @return Xyster_Collection_Interface
     */
    public function values()
    {
        return $this->_delegate->values();
    }
        
    /**
     * Gets the delegate map
     *
     * @return IMap
     */
    protected function _getDelegate()
    {
        return $this->_delegate;
    }
    
    /**
     * Sets the delegate map
     *
     * @param IMap $map
     */
    protected function _setDelegate( IMap $map )
    {
        $this->_delegate = $map;
    }
}