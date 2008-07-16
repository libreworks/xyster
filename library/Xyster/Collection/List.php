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
 * Xyster_Collection_List_Abstract
 */
require_once 'Xyster/Collection/List/Abstract.php';
/**
 * Simple implementation of an index-based collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_List extends Xyster_Collection_List_Abstract
{
	/**
	 * Creates a new list
	 *
	 * @param Xyster_Collection_Interface $values Any values to add to this list
	 */
	public function __construct( Xyster_Collection_Interface $values = null )
	{
	    if ( $values ) {
		    $this->merge($values);
	    }
	}
}