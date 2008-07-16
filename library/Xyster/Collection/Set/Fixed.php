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
 * @see Xyster_Collection_Set_Interface
 */
require_once 'Xyster/Collection/Set/Interface.php';
/**
 * @see Xyster_Collection_Fixed
 */
require_once 'Xyster/Collection/Fixed.php';
/**
 * A set that cannot be changed
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Set_Fixed extends Xyster_Collection_Fixed implements Xyster_Collection_Set_Interface
{
    /**
     * Creates a new fixed set
     *
     * @param Xyster_Collection_Set_Interface $delegate
     */
    public function __construct( Xyster_Collection_Set_Interface $delegate )
    {
        $this->_setDelegate($delegate);
    }
}