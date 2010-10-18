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
 * A comparison callback class
 * 
 * The results from the only method in this interface impose a total ordering on
 * some collection of objects. Comparators can be used to allow precise control
 * over the sort order of a collection.
 * 
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface IComparator
{
    /**
     * Compares two arguments for sorting
     * 
     * Returns a negative integer, zero, or a positive integer as the first
     * argument is less than, equal to, or greater than the second.
     *
     * @param mixed $a
     * @param mixed $b
     * @return int Either -1, 0, or 1
     */
    function compare($a, $b);
}