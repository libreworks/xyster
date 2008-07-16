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
 * @see Xyster_Collection_Map_Delegate
 */
require_once 'Xyster/Collection/Map/Delegate.php';
/**
 * A map that cannot be changed
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Map_Fixed extends Xyster_Collection_Map_Delegate
{
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @throws Xyster_Collection_Exception Always
     */
    public function clear()
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }

    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param Xyster_Map_Interface $map
     * @return boolean Whether the map changed as a result of this method
     * @throws Xyster_Collection_Exception Always
     */
    public function merge( Xyster_Collection_Map_Interface $map )
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
        
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param object $key The key to set
     * @param mixed $value The value to set
     * @throws Xyster_Collection_Exception Always
     */
    public function offsetSet( $key, $value )
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
    
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param object $key The key to "unset"
     * @throws Xyster_Collection_Exception Always
     */
    public function offsetUnset( $key )
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
    
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param mixed $key
     * @throws Xyster_Collection_Exception Always
     */
    public function remove( $key )
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
    
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param mixed $key
     * @param mixed $value
     * @throws Xyster_Collection_Exception Always
     */
    public function set( $key, $value )
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
}