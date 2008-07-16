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
 * @see Xyster_Collection_Set_Abstract
 */
require_once 'Xyster/Collection/Set/Abstract.php';
/**
 * Simple implementation of the no-duplicate collection
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Set extends Xyster_Collection_Set_Abstract
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
}