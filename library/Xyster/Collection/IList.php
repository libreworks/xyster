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
 * Index-based collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface IList extends ICollection, \ArrayAccess
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
    function get($index);

    /**
     * Returns the first index found for the specified value
     *
     * @param mixed $value
     * @return int The first index found, or null if the value isn't contained
     */
    function indexOf($value);

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
     * @throws UnmodifiableException if the collection cannot be modified
     * @throws OutOfBoundsException if the index is invalid
     */
    function insert($index, $value);

    /**
     * Inserts the supplied values into the list at the specified index
     *
     * The index must be greater than or equal to 0 and less than or equal to
     * the size of this collection.  In other words, an index is valid if
     * <code>( $index < 0 || $index > count($list) )</code> is false.
     *
     * @param int $index The index at which to insert
     * @param ICollection $values The value to insert
     * @throws InvalidArgumentException if the collection cannot contain a value
     * @throws UnmodifiableException if the collection cannot be modified
     * @throws OutOfBoundsException if the index is invalid
     */
    function insertAll($index, ICollection $values);

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
     * @throws UnmodifiableException if the collection cannot be modified
     * @throws OutOfBoundsException if the index is invalid
     */
    function removeAt($index);

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
     * @throws UnmodifiableException if the collection cannot be modified
     * @throws OutOfBoundsException if the index is invalid
     */
    function set($index, $value);

    /**
     * Removes all elements between $from and $to, including $from.
     *
     * @param int $from The starting index
     * @param int $to The index to end before reaching
     * @throws UnmodifiableException if the collection cannot be modified
     * @throws OutOfBoundsException if either $from or $to is invalid
     */
    function slice($from, $to);
}