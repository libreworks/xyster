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
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Dao\Doctrine2;
/**
 * A helper for working with Doctrine queries
 *
 * @category  Xyster
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class QueryHelper
{
    const QMARK = '?';

    /**
     * Takes an array of arguments and returns an array of parameter placeholders.
     * 
     * Example: if you pass in <code>array("a", "b", "c")</code> you will get
     * back <code>array("?1", "?2", "?3")</code>. 
     * 
     * @param array $args The arguments for which to make placeholders
     * @param int $seed The starting number
     * @return array The argument placeholders
     */
    public static function argumentsToParameters(array $args, $seed = 1)
    {
        $params = array();
        foreach($args as $v){
            $params[] = self::QMARK . $seed++;
        }
        return $params;
    }
}
