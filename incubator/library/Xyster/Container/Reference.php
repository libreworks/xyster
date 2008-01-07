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
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * A way to refer to objects stored in awkward places
 *  
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Reference
{
    /**
     * Retrieve an actual reference to the object
     * 
     * Returns null if the reference is not available or has not been populated
     * 
     * @return mixed an actual reference to the object.
     */
    function get();

    /**
     * Assign an object to the reference.
     * 
     * @param mixed $item the object to assign to the reference, May be null
     */
    function set( $item );
}