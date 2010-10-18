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
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Data;
/**
 * Comparator for objects or associative arrays
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Comparator implements \Xyster\Collection\IComparator
{
    /**
     * The clause of {@link Symbol\Sort} objects
     *
     * @var Symbol\SortClause
     */
    protected $_sorts;

    /**
     * Create a new Data Comparator object
     *
     * If you pass a clause containing no objects, this comparator will always
     * return zero when comparing objects.
     *
     * @param Symbol\SortClause $sorts A clause of sort objects
     */
    public function __construct(Symbol\SortClause $sorts)
    {
        $this->_sorts = $sorts;
    }

    /**
     * Compares two arguments for sorting
     *
     * Returns a negative integer, zero, or a positive integer as the first
     * argument is less than, equal to, or greater than the second.
     *
     * @param mixed $a
     * @param mixed $b
     */
    public function compare($a, $b)
    {
        foreach ($this->_sorts as $sort) {
            $getter = new Symbol\Evaluator($sort->getField());
            $av = $getter->evaluate($a);
            $bv = $getter->evaluate($b);
            if ($av < $bv) {
                return ( $sort->isAscending() ) ? -1 : 1;
            } else if ($av > $bv) {
                return ( $sort->isAscending() ) ? 1 : -1;
            }
        }
        return 0;
    }
}