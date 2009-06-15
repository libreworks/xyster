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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injector_Autowiring
 */
require_once 'Xyster/Container/Injector/Autowiring.php';
/**
 * An injector that can be used for setter injection with a front controller
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Action_Injector extends Xyster_Container_Injector_Autowiring
{
    protected $_request;
    protected $_response;
    protected $_params;
    
    protected static $_ignoreFields = array('frontController', 'request', 'response');
    
    /**
     * Creates a new action controller injector
     * 
	 * @param Xyster_Container_Definition the definition
	 * @param Zend_Controller_Request_Abstract the request object
	 * @param Zend_Controller_Response_Abstract the response object
	 * @param array The invocation parameters
     */
    public function __construct(Xyster_Container_Definition $def, Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $params = array())
    {
        parent::__construct($def, Xyster_Container_Autowire::ByType(), self::$_ignoreFields);
        $this->_request = $request;
        $this->_response = $response;
        $this->_params = $params;
    }
    
	/**
     * Instantiate an object with given parameters
     * 
     * @param Xyster_Type $type the class to construct
     * @return object the new object
     */
    protected function _newInstance(Xyster_Type $type, Xyster_Container_IContainer $container)
    {
        $class = $type->getClass();
        $constructor = $class ? $class->getConstructor() : null;
        return $constructor ?
            $class->newInstanceArgs(
                array($this->_request, $this->_response, $this->_params)) :
            $class->newInstance();
    }    
}