<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Xyster_Collection_Abstract
 */
require_once 'Xyster/Collection/Abstract.php';
/**
 * Implementation of Xyster_Collection_Abstract with static helper methods
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection extends Xyster_Collection_Abstract
{
    private $_immutable = false;

	/**
	 * Creates a new simple collection
	 *
	 * @param Xyster_Collection_Interface $collection
	 * @param boolean $immutable
	 */
	public function __construct( Xyster_Collection_Interface $collection = null, $immutable = false )
    {
        if ( $collection ) {
            $this->merge($collection);
        }
        $this->_immutable = $immutable;
	}

	/**
	 * Adds an item to the collection
	 *
	 * @param mixed $item The item to add
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws InvalidArgumentException if the collection cannot contain the value
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function add( $item )
	{
		$this->_failIfImmutable();
	    return parent::add($item);
	}
	
	/**
	 * Removes all items from the collection
	 *
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function clear()
	{
		$this->_failIfImmutable();
	    parent::clear();
	}
	
	/**
	 * Removes the specified value from the collection
	 *
	 * @param mixed $item The value to remove
	 * @return boolean If the value was in the collection
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function remove( $item )
	{
		$this->_failIfImmutable();
	    return parent::remove($item);
	}
	
	/**
	 * Removes all of the specified values from the collection
	 *
	 * @param Xyster_Collection_Interface $values The values to remove
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function removeAll( Xyster_Collection_Interface $values )
	{
		$this->_failIfImmutable();
		return parent::removeAll($values);
	}

	/**
	 * Removes all values from the collection except for the ones specified
	 *
	 * @param Xyster_Collection_Interface $values The values to keep
	 * @return boolean Whether the collection changed as a result of this method
	 * @throws Xyster_Collection_Exception if the collection cannot be modified
	 */
	public function retainAll( Xyster_Collection_Interface $values )
	{
		$this->_failIfImmutable();
		return parent::retainAll($values);
	}

	/**
	 * Creates a new collection containing the values
	 *
	 * @param array $values
	 * @param boolean $immutable
	 * @return Xyster_Collection_Interface
	 */
	static public function using( array $values, $immutable = false )
	{
		$collection = new Xyster_Collection(null,$immutable);
		$collection->_items = array_values($values);
		return $collection;
	}

	/**
	 * Returns a new unchangable collection containing all the supplied values
	 *
	 * @param Xyster_Collection_Interface $collection
	 * @return Xyster_Collection_Interface
	 */
	static public function fixedCollection( Xyster_Collection_Interface $collection )
	{
		return new Xyster_Collection( $collection, true );
	}

	/**
	 * Returns a new unchangable list containing all the supplied values
	 *
	 * @param Xyster_Collection_List_Interface $list
	 * @return Xyster_Collection_List_Interface
	 */
	static public function fixedList( Xyster_Collection_List_Interface $list )
	{
		return new Xyster_Collection_List( $list, true );
	}

	/**
	 * Returns a new unchangable map containing all the supplied key/value pairs
	 *
	 * @param Xyster_Collection_Map_Interface $map
	 * @return Xyster_Collection_Map_Interface
	 */
	static public function fixedMap( Xyster_Collection_Map_Interface $map )
	{
		return new Xyster_Collection_Map( $map, true );
	}

	/**
	 * Returns a new unchangable set containing all the supplied values
	 *
	 * @param Xyster_Collection_Set_Interface $set
	 * @return Xyster_Collection_Set_Interface
	 */
	static public function fixedSet( Xyster_Collection_Set_Interface $set )
	{
		return new Xyster_Collection_Set( $set, true );
	}
	
	/**
	 * A convenience method to fail on modification of immutable collection
	 *
	 * @throws Xyster_Collection_Exception if the collection is immutable
	 */
	private function _failIfImmutable()
	{
	    if ( $this->_immutable ) {
	        require_once 'Xyster/Collection/Exception';
			throw new Xyster_Collection_Exception("This collection cannot be changed");
		} 
	}
}