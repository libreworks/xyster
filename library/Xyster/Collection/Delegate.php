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
 * A collection that delegates its methods to an internal collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Delegate implements ICollection
{
    /**
     * @var ICollection
     */
    private $_delegate;
    
    /**
     * Creates a new delegate collection
     *
     * @param ICollection $delegate
     */
    public function __construct( ICollection $delegate )
    {
        $this->_delegate = $delegate;
    }
    
    /**
     * Adds an item to the collection
     * 
     * Some collections don't accept duplicate values, and should return false
     * if the provided value is already in the collection.  If the collection is
     * not allowed to contain the supplied value, an InvalidArgumentException
     * should be thrown.
     *
     * @param mixed $item The item to add
     * @return boolean Whether the collection changed as a result of this method
     * @throws InvalidArgumentException if the collection cannot contain the value
     * @throws \Xyster\Collection\Exception if the collection cannot be modified
     */
    function add( $item )
    {
        return $this->_delegate->add($item);
    }
    
    /**
     * Removes all items from the collection
     *
     * @throws \Xyster\Collection\Exception if the collection cannot be modified
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
     * Gets an iterator for the values in the collection
     *
     * @return SeekableIterator
     */
    public function getIterator()
    {
        return $this->_delegate->getIterator();
    }
    
    /**
     * Tests to see whether the collection contains the value supplied
     * 
     * If the supplied value is an object, the comparison will be done for 
     * identity (===) and not for value (==).
     *
     * @param mixed $item The item to test
     * @return boolean Whether the collection contains the supplied value   
     */
    public function contains( $item )
    {
        return $this->_delegate->contains($item);
    }
    
    /**
     * Tests to see whether the collection contains all of the supplied values
     *
     * @param ICollection $values The values to test
     * @return boolean Whether the collection contains all of the supplied values
     */
    public function containsAll( ICollection $values )
    {
        return $this->_delegate->containsAll($values);
    }
    
    /**
     * Tests to see whether the collection contains any of the supplied values
     * 
     * Basically, implementations can safely return true on the first item that
     * is found.
     * 
     * @param ICollection $values The values to test
     * @return boolean Whether the collection contains any of the supplied values
     */
    public function containsAny( ICollection $values )
    {
        return $this->_delegate->containsAny($values);
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
     * Merges the values from the supplied collection into this one
     * 
     * If the implementing collection is not allowed to contain the same value 
     * twice, it should only add ones in $values not present in $this.  If the 
     * implementing collection can contain duplicates, then the values can just
     * be appended.
     * 
     * If the collection is not allowed to contain the supplied value, an
     * InvalidArgumentException should be thrown.    
     *
     * @param ICollection $values
     * @return boolean Whether the collection changed as a result of this method
     * @throws InvalidArgumentException if the collection cannot contain the value
     * @throws \Xyster\Collection\Exception if the collection cannot be modified
     */
    public function merge( ICollection $values )
    {
        return $this->_delegate->merge($values);
    }
    
    /**
     * Removes the specified value from the collection
     *
     * @param mixed $item The value to remove
     * @return boolean If the value was in the collection
     * @throws \Xyster\Collection\Exception if the collection cannot be modified
     */
    public function remove( $item )
    {
        return $this->_delegate->remove($item);
    }
    
    /**
     * Removes all of the specified values from the collection
     *
     * @param ICollection $values The values to remove
     * @return boolean Whether the collection changed as a result of this method
     * @throws \Xyster\Collection\Exception if the collection cannot be modified
     */
    public function removeAll( ICollection $values )
    {
        return $this->_delegate->removeAll($values);
    }
    
    /**
     * Removes all values from the collection except for the ones specified
     * 
     * If the collection doesn't contain any of the values supplied, it should
     * simply be emptied.
     *
     * @param ICollection $values The values to keep
     * @return boolean Whether the collection changed as a result of this method
     * @throws \Xyster\Collection\Exception if the collection cannot be modified
     */
    public function retainAll( ICollection $values )
    {
        return $this->_delegate->retainAll($values);
    }
    
    /**
     * Puts the items in this collection into an array
     * 
     * @return array The items in this collection
     */
    public function toArray()
    {
        return $this->_delegate->toArray();
    }

    /**
     * Returns the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }
        
    /**
     * Gets the delegate collection
     *
     * @return ICollection
     */
    protected function _getDelegate()
    {
        return $this->_delegate;
    }
    
    /**
     * Sets the delegate collection
     *
     * @param ICollection $collection
     */
    protected function _setDelegate( ICollection $collection )
    {
        $this->_delegate = $collection;
    }
}