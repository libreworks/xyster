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
 * @see Xyster_Collection_Delegate
 */
require_once 'Xyster/Collection/Delegate.php';
/**
 * A collection that cannot be changed
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Collection_Fixed extends Xyster_Collection_Delegate
{        
    /**
     * Adds an item to the collection
     *
     * @param mixed $item The item to add
     * @return boolean Whether the collection changed as a result of this method
     * @throws Xyster_Collection_Exception if the collection cannot be modified
     */
    public function add( $item )
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
    
    /**
     * Removes all items from the collection
     *
     * @throws Xyster_Collection_Exception if the collection cannot be modified
     */
    public function clear()
    {
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
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
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
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
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
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
        require_once 'Xyster/Collection/Exception.php';
        throw new Xyster_Collection_Exception("This collection cannot be changed");
    }
}