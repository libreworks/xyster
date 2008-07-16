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
 * @version   $Id$
 */
/**
 * @see Xyster_Collection_Abstract
 */
require_once 'Xyster/Collection/Abstract.php';
/**
 * Implementation of Xyster_Collection_Abstract with static helper methods
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection extends Xyster_Collection_Abstract
{
    /**
     * @var Xyster_Container_List_Empty
     */
    static private $_emptyList = null;

	/**
	 * Creates a new simple collection
	 *
	 * @param Xyster_Collection_Interface $collection
	 * @param boolean $immutable
	 */
	public function __construct( Xyster_Collection_Interface $collection = null )
    {
        if ( $collection ) {
            $this->merge($collection);
        }
	}

	/**
	 * Gets an immutable, empty list
	 *
	 * @return Xyster_Container_List_Interface
	 */
	static public function emptyList()
	{
		if ( self::$_emptyList === null ) {
			require_once 'Xyster/Collection/List/Empty.php';
			self::$_emptyList = new Xyster_Collection_List_Empty;
		}
		return self::$_emptyList;
	}
	
	/**
	 * Returns a new unchangable collection containing all the supplied values
	 *
	 * @param Xyster_Collection_Interface $collection
	 * @return Xyster_Collection_Interface
	 */
	static public function fixedCollection( Xyster_Collection_Interface $collection )
	{
	    require_once 'Xyster/Collection/Fixed.php';
		return new Xyster_Collection_Fixed($collection);
	}

	/**
	 * Returns a new unchangable list containing all the supplied values
	 *
	 * @param Xyster_Collection_List_Interface $list
	 * @return Xyster_Collection_List_Interface
	 */
	static public function fixedList( Xyster_Collection_List_Interface $list )
	{
	    require_once 'Xyster/Collection/List/Fixed.php';
		return new Xyster_Collection_List_Fixed($list);
	}

	/**
	 * Returns a new unchangable map containing all the supplied key/value pairs
	 *
	 * @param Xyster_Collection_Map_Interface $map
	 * @return Xyster_Collection_Map_Interface
	 */
	static public function fixedMap( Xyster_Collection_Map_Interface $map )
	{
	    require_once 'Xyster/Collection/Map/Fixed.php';
		return new Xyster_Collection_Map_Fixed($map);
	}

	/**
	 * Returns a new unchangable set containing all the supplied values
	 *
	 * @param Xyster_Collection_Set_Interface $set
	 * @return Xyster_Collection_Set_Interface
	 */
	static public function fixedSet( Xyster_Collection_Set_Interface $set )
	{
	    require_once 'Xyster/Collection/Set/Fixed.php';
		return new Xyster_Collection_Set_Fixed($set);
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
        $collection = new Xyster_Collection;
        $collection->_items = array_values($values);
        return $immutable ? self::fixedCollection($collection) : $collection;
    }
}