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
 */
/**
 * Xyster_Collection_Set_Abstract
 */
require_once 'Xyster/Collection/Set/Abstract.php';
/**
 * Simple implementation of the no-duplicate collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Set extends Xyster_Collection implements Xyster_Collection_Set_Interface
{
	/**
	 * Creates a new set
	 *
	 * @param Xyster_Collection_Set_Interface $set The values to add to this set
	 * @param boolean $immutable Whether the set can be changed
	 */
	public function __construct( Xyster_Collection_Set_Interface $set, $immutable = false )
	{
		parent::__construct($set,$immutable);
	}

	/**
	 * Adds an item to the set
	 * 
	 * This collection doesn't accept duplicate values, and will return false
	 * if the provided value is already in the collection.
	 *
	 * @param mixed $item The item to add
	 * @return boolean Whether the set changed as a result of this method
	 * @throws BadMethodCallException if the collection cannot be modified
	 */
	public function add( $item )
	{
		if ( !$this->contains($item) ) {
			return parent::add($item);
		} return false;
	}
}