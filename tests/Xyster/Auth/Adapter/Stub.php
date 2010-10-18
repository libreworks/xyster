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
 * @package   Xyster_Auth
 * @subpackage   UnitTests
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Auth\Adapter;
/**
 * Just a simple stub object
 *
 */
class Stub implements \Zend_Auth_Adapter_Interface
{
    protected $_identity;
    
    /**
     * Creates a new auth adapter stub
     *
     * @param string $identity
     */
    public function __construct( $identity )
    {
        $this->_identity = $identity;
    }
    
    /**
     * Returns the authenticate result
     *
     * @return \Zend_Auth_Result
     */
    public function authenticate()
    {
        return new \Zend_Auth_Result(1, $this->_identity);
    }
}
