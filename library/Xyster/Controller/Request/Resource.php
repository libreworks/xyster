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
 * @package   Xyster_Acl
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Controller\Request;
/**
 * An ACL resource to represent an MVC dispatch location
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Resource implements \Zend_Acl_Resource_Interface
{
    /**
     * @var string
     */
    protected $_module;
    
    /**
     * @var string
     */
    protected $_controller;
    
    /**
     * @var string
     */
    protected $_action;

    /**
     * Creates a new request resource
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    public function __construct( $module, $controller=null, $action=null )
    {
        $this->_module = $module;
        $this->_controller = $controller;
        $this->_action = $action;
    }
    
    /**
     * Creates a new request resource based on the values in a request
     *
     * @param \Zend_Controller_Request_Abstract $request
     * @return \Xyster\Controller\Request\Resource
     */
    static public function create( \Zend_Controller_Request_Abstract $request )
    { 
        return new self($request->getModuleName(),
            $request->getControllerName(), $request->getActionName());
    }
    
    /**
     * Returns the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * Returns the controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }
    
    /**
     * Returns the module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }
    
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        $resource = 'MVC:' . $this->_module . '/';
        if ( $this->_controller ) {
            $resource .= $this->_controller . '/';
        }
        if ( $this->_controller && $this->_action ) {
            $resource .= $this->_action;
        }
        
        return $resource;
    }
    
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
}