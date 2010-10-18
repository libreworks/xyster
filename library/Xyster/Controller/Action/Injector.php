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
 * @package   Xyster_Controller
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Controller\Action;
/**
 * An injector that can be used for setter injection with a front controller
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Injector extends \Xyster\Container\Injector\Autowiring
{
    protected $_request;
    protected $_response;
    protected $_params;
    protected static $_ignoreFields = array('frontController', 'request', 'response');

    /**
     * Creates a new action controller injector
     *
     * @param \Xyster\Container\Definition the definition
     * @param Zend_Controller_Request_Abstract the request object
     * @param Zend_Controller_Response_Abstract the response object
     * @param array The invocation parameters
     */
    public function __construct(\Xyster\Container\Definition $def, \Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $params = array())
    {
        parent::__construct($def, \Xyster\Container\Autowire::ByType(), self::$_ignoreFields);
        $this->_request = $request;
        $this->_response = $response;
        $this->_params = $params;
    }

    /**
     * Instantiate an object with given parameters
     * 
     * @param \Xyster\Type\Type $type the class to construct
     * @return object the new object
     */
    protected function _newInstance(\Xyster\Type\Type $type, \Xyster\Container\IContainer $container)
    {
        $class = $type->getClass();
        $constructor = $class ? $class->getConstructor() : null;
        return $constructor ?
                $class->newInstanceArgs(
                        array($this->_request, $this->_response, $this->_params)) :
                $class->newInstance();
    }
}