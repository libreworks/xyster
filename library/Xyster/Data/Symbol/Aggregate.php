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
 */
namespace Xyster\Data\Symbol;

use Xyster\Enum\Enum;

/**
 * Aggregate function enumerated type
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Aggregate extends Enum
{
    const Average = "AVG";
    const Count = "COUNT";
    const Maximum = "MAX";
    const Minimum = "MIN";
    const Sum = "SUM";

    /**
     * Uses the Average function
     *
     * @return Aggregate
     */
    static public function Average()
    {
        return Enum::_factory();
    }

    /**
     * Uses the Count function
     *
     * @return Aggregate
     */
    static public function Count()
    {
        return Enum::_factory();
    }

    /**
     * Uses the Maximum function
     *
     * @return Aggregate
     */
    static public function Maximum()
    {
        return Enum::_factory();
    }

    /**
     * Uses the Minimum function
     *
     * @return Aggregate
     */
    static public function Minimum()
    {
        return Enum::_factory();
    }

    /**
     * Uses the Sum function
     *
     * @return Aggregate
     */
    static public function Sum()
    {
        return Enum::_factory();
    }
}