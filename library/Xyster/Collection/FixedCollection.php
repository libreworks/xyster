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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace Xyster\Collection;
/**
 * A collection that cannot be changed
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class FixedCollection extends Delegate
{        
    /**
     * Adds an item to the collection
     *
     * @param mixed $item The item to add
     * @return boolean Whether the collection changed as a result of this method
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function add( $item )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * Removes all items from the collection
     *
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function clear()
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * Removes the specified value from the collection
     *
     * @param mixed $item The value to remove
     * @return boolean If the value was in the collection
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function remove( $item )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * Removes all of the specified values from the collection
     *
     * @param ICollection $values The values to remove
     * @return boolean Whether the collection changed as a result of this method
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function removeAll( ICollection $values )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }

    /**
     * Removes all values from the collection except for the ones specified
     *
     * @param ICollection $values The values to keep
     * @return boolean Whether the collection changed as a result of this method
     * @throws UnmodifiableException if the collection cannot be modified
     */
    public function retainAll( ICollection $values )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
}