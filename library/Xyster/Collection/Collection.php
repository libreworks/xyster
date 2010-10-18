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
 * Implementation of AbstractCollection with static helper methods
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Collection extends AbstractCollection
{
    /**
     * @var EmptyList
     */
    static private $_emptyList = null;

    /**
     * Creates a new simple collection
     *
     * @param ICollection $collection
     * @param boolean $immutable
     */
    public function __construct(ICollection $collection = null)
    {
        if ($collection) {
            $this->merge($collection);
        }
    }

    /**
     * Gets an immutable, empty list
     *
     * @return IList
     */
    static public function emptyList()
    {
        if (self::$_emptyList === null) {
            self::$_emptyList = new EmptyList;
        }
        return self::$_emptyList;
    }

    /**
     * Returns a new unchangable collection containing all the supplied values
     *
     * @param ICollection $collection
     * @return ICollection
     */
    static public function fixedCollection(ICollection $collection)
    {
        return new FixedCollection($collection);
    }

    /**
     * Returns a new unchangable list containing all the supplied values
     *
     * @param IList $list
     * @return IList
     */
    static public function fixedList(IList $list)
    {
        return new FixedList($list);
    }

    /**
     * Returns a new unchangable map containing all the supplied key/value pairs
     *
     * @param IMap $map
     * @return IMap
     */
    static public function fixedMap(IMap $map)
    {
        return new FixedMap($map);
    }

    /**
     * Returns a new unchangable set containing all the supplied values
     *
     * @param ISet $set
     * @return ISet
     */
    static public function fixedSet(ISet $set)
    {
        return new FixedSet($set);
    }

    /**
     * Creates a new collection containing the values
     *
     * @param array $values
     * @param boolean $immutable
     * @return ICollection
     */
    static public function using(array $values, $immutable = false)
    {
        $collection = new self;
        $collection->_items = array_values($values);
        return $immutable ? self::fixedCollection($collection) : $collection;
    }
}