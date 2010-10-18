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
 * A simple DTO for errors
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Error
{
    /**
     * @var string
     */
    protected $_message;
    
    /**
     * @var string
     */
    protected $_field;
    
    /**
     * Creates a new error
     *
     * @param string $message
     * @param string $field
     * @param int $code
     */
    public function __construct( $message, $field = null )
    {
        $this->_message = $message;
        $this->_field = $field;
    }
    
    /**
     * Gets the message associated with the error
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }
    
    /**
     * Gets the field of the error, usually a field or property name
     *
     */
    public function getField()
    {
        return $this->_field;
    }
    
    /**
     * Returns the string value of the object
     *
     * @return string
     */
    public function __toString()
    {
        return ( $this->_field ) ?
            $this->_field . ': ' . $this->_message : $this->_message;
    }
}