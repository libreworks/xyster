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
 * No-duplicate collection whose elements can be reordered
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Set_Sortable extends Xyster_Collection_Set_Abstract
{
	/**
	 * Creates a new set
	 *
	 * @param Xyster_Collection_Interface $set The values to add to this set
	 */
	public function __construct( Xyster_Collection_Interface $set = null )
	{
	    if ( $set ) {
		    $this->merge($set);
	    }
	}

	/**
	 * Sorts the collection according to the values in the elements
	 *
	 * @param Xyster_Collection_Comparator_Interface $comparator The comparator
	 */
    public function sort( Xyster_Collection_Comparator_Interface $comparator )
    {
    	usort( $this->_items, array($comparator,'compare') );
    }
}