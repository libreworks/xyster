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
 * @version   $Id$
 */
namespace Xyster\Validate;
/**
 * A URI validator
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Uri extends \Zend_Validate_Abstract
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
        
        if ( !\Zend_Uri::check($value) ) {
            $this->_error(self::NOT_URI);
            return false;
        }
        
        return true;
    }
}