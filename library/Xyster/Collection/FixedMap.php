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
 * @version   $Id$
 */
namespace Xyster\Collection;
/**
 * A map that cannot be changed
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class FixedMap extends DelegateMap
{
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @throws UnmodifiableException Always
     */
    public function clear()
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }

    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param Xyster_Map_Interface $map
     * @return boolean Whether the map changed as a result of this method
     * @throws UnmodifiableException Always
     */
    public function merge( IMap $map )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
        
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param object $key The key to set
     * @param mixed $value The value to set
     * @throws UnmodifiableException Always
     */
    public function offsetSet( $key, $value )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param object $key The key to "unset"
     * @throws UnmodifiableException Always
     */
    public function offsetUnset( $key )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param mixed $key
     * @throws UnmodifiableException Always
     */
    public function remove( $key )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
    
    /**
     * This map cannot be changed, so this method always throws an exception
     *
     * @param mixed $key
     * @param mixed $value
     * @throws UnmodifiableException Always
     */
    public function set( $key, $value )
    {
        throw new UnmodifiableException("This collection cannot be changed");
    }
}