<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Filter
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';
/**
 * @see Xyster_String
 */
require_once 'Xyster/String.php';
/**
 * A filter for title case
 *
 * @category  Xyster
 * @package   Xyster_Filter
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Filter_TitleCase implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return string
     * @see Xyster_String::titleCase
     */
    public function filter($value)
    {
        return Xyster_String::titleCase($value);
    }
}