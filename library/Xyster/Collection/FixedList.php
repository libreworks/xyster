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
 * A list that cannot be changed
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class FixedList extends FixedCollection implements IList
{
    /**
     * Creates a new fixed list
     *
     * @param IList $list
     */
    public function __construct( IList $list )
    {
        $this->_setDelegate($list);
    }
    
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
    public function get( $index )
    {
        return $this->_getDelegate()->offsetGet($index);
    }
    
    /**
     * Returns the first index found for the specified value
     *
     * @param mixed $value
     * @return int The first index found, or null if the value isn't contained
     */
    public function indexOf( $value )
    {
        return $this->_getDelegate()->indexOf($value);
    }
        
    /**
     * This list is unmodifiable, so this method will always throw an exception
     *
     * @param int $index The index at which to insert
     * @param mixed $value The value to insert
     * @throws UnmodifiableException Always
     */
    public function insert( $index, $value )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This list is unmodifiable, so this method will always throw an exception
     * 
     * @param int $index The index at which to insert
     * @param Xyster_Collection_Interface $values The value to insert
     * @throws UnmodifiableException Always
     */
    public function insertAll( $index, ICollection $values )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * Gets whether the specified index exists in the list
     *
     * @param int $index The index to test
     * @return boolean Whether the index is in the list
     */
    public function offsetExists( $index )
    {
        return $this->_getDelegate()->offsetExists($index);
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
    public function offsetGet( $index )
    {
        return $this->_getDelegate()->offsetGet($index);
    }
    
    /**
     * This list is unmodifiable, so this method will always throw an exception
     *
     * @param int $index The index to set
     * @param mixed $value The value to set
     * @throws UnmodifiableException Always
     */
    public function offsetSet( $index, $value )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This list is unmodifiable, so this method will always throw an exception
     *
     * @param int $index The index to "unset"
     * @throws UnmodifiableException Always
     */
    public function offsetUnset( $index )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This list is unmodifiable, so this method will always throw an exception  
     *
     * @param int $index The index to "unset"
     * @return mixed The value removed
     * @throws UnmodifiableException Always
     */
    public function removeAt( $index )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This list is unmodifiable, so this method will always throw an exception
     *
     * @param int $index The index to set
     * @param mixed $value The value to set
     * @throws UnmodifiableException Always
     */
    public function set( $index, $value )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
        
    /**
     * This list is unmodifiable, so this method will always throw an exception
     *
     * @param int $from The starting index
     * @param int $count The number of elements to remove
     * @throws UnmodifiableException Always
     */
    public function slice( $from, $count )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }    
}