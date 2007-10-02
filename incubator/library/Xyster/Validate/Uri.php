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
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';
/**
 * Zend_Uri
 */
require_once 'Zend/Uri.php';
/**
 * A URI validator
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Validate_Uri extends Zend_Validate_Abstract
{
    /**
     * Validation failure message key for when the file is not an image
     *
     */
    const NOT_URI = 'notUri';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_URI => "'%value%' does not appear to be a correct URI",
    );
    
	/**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if dimensions of file specified by $value are between width
     * and height options, dimensions being exact if strict is true.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        
        if ( !Zend_Uri::check($value) ) {
            $this->_error();
            return false;
        }
        
        return true;
    }
}