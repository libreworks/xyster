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
 * Iterator for collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Iterator implements SeekableIterator, Countable
{
	/**
	 * The array over which to iterate
	 *
	 * @var array
	 */
	protected $_items;

	/**
	 * Creates a new iterator
	 *
	 * @param array $list  An array to iterate
	 */
	public function __construct( array $list )
	{
		$this->_items = $list;
	}

	/**
	 * Counts the number of elements
	 *
	 * @return int  Number of elements in collection
	 */
	public function count()
	{
		return count($this->_items);
	}
	
	/**
	 * Return the current element
	 *
	 * @return mixed  The current element
	 */
    public function current()
    {
        return current($this->_items);
    }
    
	/**
	 * Return the key of the current element
	 *
	 * @return mixed  Key of the current element
	 */
    public function key()
    {
        return key($this->_items);
    }
    
	/**
	 * Move pointer forward to next element
	 *
	 */
    public function next()
    {
        next($this->_items);
    }
    
	/**
	 * Rewind the pointer to the first element
	 *
	 */
    public function rewind()
    {
        reset($this->_items);
    }
    
	/**
	 * Check if there is a current element after calls to rewind() or next()
	 *
	 * @return boolean
	 */
    public function valid()
    {
        return ( $this->current() !== false );
    }
    
	/**
	 * Seek to an absolute position.
	 *
	 * @param int $index  Position to seek
	 * @throws Xyster_Collection_Exception  if the seek position is not found
	 */
 	public function seek( $index )
 	{
		$this->rewind();
		$position = 0;
		while( $position < $index && $this->valid() ) {
			$this->next();
			$position++;
		}
		if ( !$this->valid() ) {
		    require_once 'Xyster/Collection/Exception.php';
			throw new Xyster_Collection_Exception('Out of bounds');
		}
 	}
}