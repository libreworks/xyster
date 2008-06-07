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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Validate_NotEmpty
 */
require_once 'Zend/Validate/NotEmpty.php';
/**
 * A null value validator 
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Validate_NotNull extends Zend_Validate_NotEmpty
{
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value is not null.
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if ($value === NULL) {
            $this->_error();
            return false;
        }

        return true;
    }
}