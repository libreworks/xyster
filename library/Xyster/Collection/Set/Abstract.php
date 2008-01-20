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
 * Xyster_Collection_Abstract
 */
require_once 'Xyster/Collection/Abstract.php';
/**
 * Xyster_Collection_Set_Interface
 */
require_once 'Xyster/Collection/Set/Interface.php';
/**
 * Abstract class for no-duplicate collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Set_Abstract extends Xyster_Collection_Abstract implements Xyster_Collection_Set_Interface
{
	/**
	 * Adds an item to the set
	 *
	 * @param mixed $item The item to add
	 * @return boolean Whether the set changed as a result of this method
	 */
	public function add( $item )
	{
		if ( !$this->contains($item) ) {
			return parent::add($item);
		} return false;
	}
}