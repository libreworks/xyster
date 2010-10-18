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
 * @package   Xyster_Validate
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: Uri.php 190 2008-01-07 22:39:57Z doublecompile $
 */
namespace Xyster\Validate;
/**
 * An integer validator
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Int extends \Zend_Validate_Int
{
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid integer
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        $locale = localeconv();

        $valueFiltered = str_replace($locale['decimal_point'], '.', $valueString);
        $valueFiltered = str_replace($locale['thousands_sep'], '', $valueFiltered);

        if ( !preg_match( '/^([0-9]+)(\.[0-9]+)?$/', $valueFiltered ) ) {
            $this->_error(self::NOT_INT);
            return false;
        }

        return true;
    }
}