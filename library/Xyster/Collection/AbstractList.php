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
 * Abstract class for index-based collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractList extends AbstractCollection implements IList
{
    /**
     * Gets the value at a specified index
     * 
     * This method is an alias to ArrayAccess::offsetGet
     * 
     * The index must be greater than or equal to 0 and less than or equal to
     * the size of this collection.  In other words, an index is valid if  
     * <code>( $index < 0 || $index > $this->count() )</code> is false.
     *
     * @param int $index The index to get
     * @return mixed The value found at $index
     * @throws OutOfBoundsException if the index is invalid
     */
    public function get($index)
    {
        return $this->offsetGet($index);
    }

    /**
     * Returns the first index found for the specified value
     *
     * @param mixed $value
     * @return int The first index found, or false if the value isn't contained
     */
    public function indexOf($value)
    {
        return array_search($value, $this->_items, true);
    }

    /**
     * Inserts a value into the list at the specified index
     * 
     * The index must be greater than or equal to 0 and less than or equal to
     * the size of this collection.  In other words, an index is valid if  
     * <code>( $index < 0 || $index > $this->count() )</code> is false.  
     *
     * @param int $index The index at which to insert
     * @param mixed $value The value to insert
     * @throws OutOfBoundsException if the index is invalid
     */
    public function insert($index, $value)
    {
        if ($index < 0 || $index > $this->count()) {
            throw new \OutOfBoundsException("Invalid index given");
        }
        array_splice($this->_items, $index, 0, $value);
    }

    /**
     * Inserts the supplied values into the list at the specified index
     *
     * The index must be greater than or equal to 0 and less than or equal to
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index > $this->count() )</code> is false.
     *
     * @param int $index The index at which to insert
     * @param ICollection $values The value to insert
     * @throws OutOfBoundsException if the index is invalid
     */
    public function insertAll($index, ICollection $values)
    {
        if ($index < 0 || $index > $this->count()) {
            throw new \OutOfBoundsException("Invalid index given");
        }
        array_splice($this->_items, $index, 0, $values->toArray());
    }

    /**
     * Gets whether the specified index exists in the list
     *
     * @param int $index The index to test
     * @return boolean Whether the index is in the list
     */
    public function offsetExists($index)
    {
        return $index > -1 && $index < $this->count();
    }

    /**
     * Gets the value at a specified index
     *
     * The index must be greater than or equal to 0 and less than
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index >= $this->count() )</code> is false.
     *
     * @param int $index The index to get
     * @return mixed The value found at $index
     * @throws OutOfBoundsException if the index is invalid
     */
    public function offsetGet($index)
    {
        if (!$this->offsetExists($index)) {
            throw new \OutOfBoundsException("Invalid index given");
        }
        return $this->_items[$index];
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
    public function offsetSet($index, $value)
    {
        if ($index < 0 || $index > $this->count()) {
            throw new \OutOfBoundsException("Invalid index given");
        }
        $this->_items[$index] = $value;
    }

    /**
     * Removes a value at the specified index
     *
     * The index must be greater than or equal to 0 and less than
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index > $this->count() )</code> is false.
     *
     * @param int $index The index to "unset"
     * @throws OutOfBoundsException if the index is invalid
     */
    public function offsetUnset($index)
    {
        if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfBoundsException("Invalid index given");
        }
        unset($this->_items[$index]);
        $this->_items = array_values($this->_items);
    }

    /**
     * Removes the specified value from the collection
     *
     * @param mixed $item The value to remove
     * @return boolean If the value was in the collection
     */
    public function remove($item)
    {
        $removed = parent::remove($item);
        if ($removed) {
            $this->_items = array_values($this->_items);
        }
        return $removed;
    }

    /**
     * Removes all of the specified values from the collection
     *
     * @param ICollection $values The values to remove
     * @return boolean Whether the collection changed as a result of this method
     */
    public function removeAll(ICollection $values)
    {
        $removed = parent::removeAll($values);
        if ($removed) {
            $this->_items = array_values($this->_items);
        }
        return $removed;
    }

    /**
     * Removes a value at the specified index
     *
     * This method is an alias to ArrayAccess::offsetUnset
     *
     * The index must be greater than or equal to 0 and less than
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index > $this->count() )</code> is false.
     *
     * @param int $index The index to "unset"
     * @throws OutOfBoundsException if the index is invalid
     */
    public function removeAt($index)
    {
        return $this->offsetUnset($index);
    }

    /**
     * Removes all values from the collection except for the ones specified
     *
     * {@inherit}
     *
     * @param ICollection $values The values to keep
     * @return boolean Whether the collection changed as a result of this method
     */
    public function retainAll(ICollection $values)
    {
        $removed = parent::retainAll($values);
        if ($removed && $this->count()) {
            $this->_items = array_values($this->_items);
        }
        return $removed;
    }

    /**
     * Sets the value at a given index.
     *
     * This method is an alias to ArrayAccess::offsetSet.
     *
     * The index must be greater than or equal to 0 and less than
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index > $this->count() )</code> is false.
     *
     * @param int $index The index to set
     * @param mixed $value The value to set
     * @throws OutOfBoundsException if the index is invalid
     */
    public function set($index, $value)
    {
        $this->offsetSet($index, $value);
    }

    /**
     * Removes $count elements starting at $from 
     *
     * @param int $from The starting index
     * @param int $count The number of elements to remove
     * @throws OutOfBoundsException if $from is invalid
     */
    public function slice($from, $count)
    {
        if ($from < 0 || $from >= $this->count()) {
            throw new \OutOfBoundsException("Invalid index given");
        }
        for ($i = $from; $i < $count + $from; $i++) {
            if (isset($this->_items[$i]))
                unset($this->_items[$i]);
        }
        $this->_items = array_values($this->_items);
    }
}