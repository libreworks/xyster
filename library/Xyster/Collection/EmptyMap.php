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
 */
namespace Xyster\Collection;
/**
 * An immutable empty list
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @since 02 Build 02
 */
class EmptyMap implements IMap
{
    /**
     * Removes all items from the collection
     *
     * @throws UnmodifiableException Always
     */
    public function clear()
    {
        throw new UnmodifiableException('This map is immutable');
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
    public function containsKey($key)
    {
        return false;
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
    public function containsValue($item)
    {
        return false;
    }

    /**
     * Gets the number of items in the collection
     *
     * @return int The number of items
     */
    public function count()
    {
    	return 0;
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
        return null;
    }

    /**
     * Gets an iterator for the values in the collection
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new \EmptyIterator;
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
        return true;
    }

    /**
     * Gets all keys contained in this map
     *
     * @return ISet The keys in this map
     */
    public function keys()
    {
        return Collection::emptyList();
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
    public function keyFor($value)
    {
        return null;
    }

    /**
     * Gets all keys for the value supplied
     *
     * @param mixed $value The value for which to search
     * @return ISet
     */
    public function keysFor($value)
    {
        return Collection::emptyList();
    }

    /**
     * Combines this map with the supplied map
     *
     * The values in the supplied map will take precedence if this map and the
     * supplied map have duplicate keys.
     *
     * @param IMap $map
     * @throws UnmodifiableException always
     * @return boolean Whether the map changed as a result of this method
     */
    public function merge( IMap $values )
    {
        throw new UnmodifiableException('This map is immutable');
    }

    /**
     * Gets whether the specified index exists in the list
     *
     * @param int $index The index to test
     * @return boolean Whether the index is in the list
     */
    public function offsetExists( $index )
    {
        return false;
    }

    /**
     * Gets the value at a specified index
     *
     * @param int $index The index to get
     * @return mixed The value found at $index
     * @throws UnmodifiableException if the index is invalid
     */
    public function offsetGet( $index )
    {
        return null;
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
     * @throws OutOfBoundsException if the index is invalid
     */
    public function offsetSet( $index, $value )
    {
        throw new UnmodifiableException('This map is immutable');
    }

    /**
     * Removes a value at the specified index
     *
     * The index must be greater than or equal to 0 and less than
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index > $this->count() )</code> is false.
     *
     * @param int $index The index to "unset"
     */
    public function offsetUnset( $index )
    {
        throw new UnmodifiableException('This map is immutable');
    }


    /**
     * Removes the mapping for the specified key
     *
     * This method is an alias to ArrayAccess::offsetUnset
     *
     * @param mixed $key
     * @throws UnmodifiableException always
     */
    public function remove( $key )
    {
        throw new UnmodifiableException('This map is immutable');
    }

    /**
     * Sets a mapping for the key and value supplied
     *
     * This method is an alias to ArrayAccess::offsetSet
     *
     * @param mixed $key
     * @param mixed $value
     * @throws UnmodifiableException always
     */
    public function set($key, $value)
    {
        throw new UnmodifiableException("This map cannot be modified");
    }

    /**
     * Puts the items in this collection into an array
     *
     * @return array The items in this collection
     */
    public function toArray()
    {
        return array();
    }

    /**
     * Gets the values contained in this map
     *
     * @return ICollection
     */
    public function values()
    {
        return Collection::emptyList();
    }
}
