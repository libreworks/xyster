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
 * @package   Xyster_Controller
 * @subpackage Plugins
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';
/**
 * @see Xyster_Controller_Request_Resource
 */
require_once 'Xyster/Controller/Request/Resource.php';
/**
 * Authorization plugin
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Plugins
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * The acl
     *
     * @var Zend_Acl
     */
    protected $_acl;
    
	/**
     * Module to use for errors; defaults to default module in dispatcher
     * @var string
     */
    protected $_errorModule;

    /**
     * Controller to use for errors; defaults to 'error'
     * @var string
     */
    protected $_errorController = 'error';

    /**
     * Action to use for errors; defaults to 'error'
     * @var string
     */
    protected $_errorAction = 'error';
    
    /**
     * Creates a new acl plugin
     *
     * Options may include:
     * - module
     * - controller
     * - action
     * 
     * @param Zend_Acl $acl
     * @param array $options
     */
    public function __construct( Zend_Acl $acl, array $options = array())
    {
        $this->_acl = $acl;
        $this->setAccessDenied($options);
    }
    
    /**
     * Allows access to an action by a role
     * 
     * Passing null for the role will allow all users to access the action.
     *   
     * Passing null for the module will allow the role access to all actions in
     * all controllers in all modules.  Specifying a module but leaving
     * controller and action null will allow access to all actions in all
     * controllers in the specified module.  Specifying a module and a
     * controller but leaving action null will allow access to all actions in
     * the specified controller.
     *
     * @param string $name The controller action name
     * @param Zend_Acl_Role_Interface|string $role
     * @return Xyster_Controller_Action_Helper_Acl provides a fluent interface
     */
    public function allow( $role, $module, $controller = null, $action = null )
    {
        $resource = $this->_getResource($module, $controller, $action);
        $this->_acl->allow($role, $resource);
        return $this;
    }

    /**
     * Retrieve the current acl plugin action
     *
     * @return string
     */
    public function getAccessDeniedAction()
    {
        return $this->_errorAction;
    }
    
    /**
     * Retrieve the current acl plugin controller
     *
     * @return string
     */
    public function getAccessDeniedController()
    {
        return $this->_errorController;
    }

    /**
     * Retrieve the current acl plugin module
     *
     * @return string
     */
    public function getAccessDeniedModule()
    {
        if (null === $this->_errorModule) {
            require_once 'Zend/Controller/Front.php';
            $this->_errorModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_errorModule;
    }
    
    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // they should be allowed access to the error display screen, duh
        $this->_acl->allow(null,
            $this->_getResource($this->getAccessDeniedModule(),
            $this->getAccessDeniedController(),
            $this->getAccessDeniedAction()));
            
        $request = $this->getRequest();
        $role = Zend_Auth::getInstance()->getIdentity();
        $resource = $this->_getResource($request->getModuleName(),
            $request->getControllerName(), $request->getActionName());
        
        try {
            if ( !$this->_acl->isAllowed($role, $resource) ) {
                $msg = 'Insufficient permissions: ';
        		$msg .= $role . ' -> ' . $resource->getResourceId();
        		require_once 'Zend/Acl/Exception.php';
                throw new Zend_Acl_Exception($msg);
            }
        } catch ( Zend_Acl_Exception $thrown ) {
            $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $error->exception = $thrown;
            $error->type = 'EXCEPTION_OTHER';
    
            // Keep a copy of the original request
            $error->request = clone $request;
    
            // Forward to the error handler
            $request->setParam('error_handler', $error)
                ->setModuleName($this->getAccessDeniedModule())
                ->setControllerName($this->getAccessDeniedController())
                ->setActionName($this->getAccessDeniedAction())
                ->setDispatched(false);
        }
    }

    /**
     * Setup the error handling options
     *
     * @param  array $options
     * @return Xyster_Controller_Plugin_Acl
     */
    public function setAccessDenied(array $options = array())
    {
        if (isset($options['module'])) {
            $this->setAccessDeniedModule($options['module']);
        }
        if (isset($options['controller'])) {
            $this->setAccessDeniedController($options['controller']);
        }
        if (isset($options['action'])) {
            $this->setAccessDeniedAction($options['action']);
        }
        return $this;
    }
    
    /**
     * Set the action name for the acl plugin
     *
     * @param  string $action
     * @return Xyster_Controller_Plugin_Acl
     */
    public function setAccessDeniedAction($action)
    {
        $this->_errorAction = (string) $action;
        return $this;
    }

    /**
     * Set the controller name for the acl plugin
     *
     * @param  string $controller
     * @return Xyster_Controller_Plugin_Acl
     */
    public function setAccessDeniedController($controller)
    {
        $this->_errorController = (string) $controller;
        return $this;
    }
    
    /**
     * Set the module name for the acl plugin
     *
     * @param  string $module
     * @return Xyster_Controller_Plugin_Acl
     */
    public function setAccessDeniedModule($module)
    {
        $this->_errorModule = (string) $module;
        return $this;
    }
    
    /**
     * Gets the resource object
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return Xyster_Controller_Request_Resource
     */
    protected function _getResource( $module, $controller, $action )
    {
        $resource = null;
        
        if ( $module ) {
            $moduleResource = new Xyster_Controller_Request_Resource($module);
            if ( !$this->_acl->has($moduleResource) ) {
                $this->_acl->add($moduleResource);
            }
            $resource = $moduleResource;
        }
        if ( $module && $controller ) {
            $controllerResource = new Xyster_Controller_Request_Resource($module, $controller);
            if ( !$this->_acl->has($controllerResource) ) {
                $this->_acl->add($controllerResource, $moduleResource);
            }
            $resource = $controllerResource;
        }
        if ( $module && $controller && $action ) {
            $actionResource = new Xyster_Controller_Request_Resource($module, $controller, $action);
            if ( !$this->_acl->has($actionResource) ) {
                $this->_acl->add($actionResource, $controllerResource);
            }
            $resource = $actionResource;
        }
        
        return $resource;
    }
}