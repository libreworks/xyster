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
 * Zend_Auth
 */
require_once 'Zend/Auth.php';
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
    protected $_deniedModule;

    /**
     * Controller to use for errors; defaults to 'error'
     * @var string
     */
    protected $_deniedController = 'error';

    /**
     * Action to use for errors; defaults to 'error'
     * @var string
     */
    protected $_deniedAction = 'error';
    
    /**
     * Action to use for login; defaults to 'index'
     * @var string
     */
    protected $_loginAction = 'index';
    
    /**
     * Controller to use for login; defaults to 'login'
     * @var string
     */
    protected $_loginController = 'login';
    
    /**
     * Module to use for login; defaults to default module in dispatcher
     * @var string
     */
    protected $_loginModule;
    
    /**
     * Creates a new acl plugin
     * 
     * @param Zend_Acl $acl
     */
    public function __construct( Zend_Acl $acl )
    {
        $this->_acl = $acl;
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
    public function allow( $role, $module = null, $controller = null, $action = null )
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
        return $this->_deniedAction;
    }
    
    /**
     * Retrieve the current acl plugin controller
     *
     * @return string
     */
    public function getAccessDeniedController()
    {
        return $this->_deniedController;
    }

    /**
     * Retrieve the current acl plugin module
     *
     * @return string
     */
    public function getAccessDeniedModule()
    {
        if (null === $this->_deniedModule) {
            require_once 'Zend/Controller/Front.php';
            $this->_deniedModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_deniedModule;
    }

    /**
     * Retrieve the current acl plugin action
     *
     * @return string
     */
    public function getLoginAction()
    {
        return $this->_loginAction;
    }
    
    /**
     * Retrieve the current acl plugin controller
     *
     * @return string
     */
    public function getLoginController()
    {
        return $this->_loginController;
    }

    /**
     * Retrieve the current acl plugin module
     *
     * @return string
     */
    public function getLoginModule()
    {
        if (null === $this->_loginModule) {
            require_once 'Zend/Controller/Front.php';
            $this->_loginModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_loginModule;
    }
        
    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {        
        $request = $this->getRequest();
        $auth = Zend_Auth::getInstance();
        $role = $auth->getIdentity();
        $resource = $this->_getResource($request->getModuleName(),
            $request->getControllerName(), $request->getActionName());

        $isAllowed = $this->_acl->isAllowed($role, $resource);
        
        if ( !$isAllowed && !$auth->hasIdentity() ) {
            // they should be allowed access to the login form
            $this->_acl->allow(null,
                $this->_getResource($this->getLoginModule(),
                $this->getLoginController(),
                $this->getLoginAction()));
                
            // Forward to the login form
            $request->setModuleName($this->getLoginModule())
                ->setControllerName($this->getLoginController())
                ->setActionName($this->getLoginAction())
                ->setDispatched(false);
            return;
        }
            
        try {
            if ( !$isAllowed ) {
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
            
            // they should be allowed access to the error display screen, duh
            $this->_acl->allow(null,
                $this->_getResource($this->getAccessDeniedModule(),
                $this->getAccessDeniedController(),
                $this->getAccessDeniedAction()));
            
            // Forward to the error handler
            $request->setParam('error_handler', $error)
                ->setModuleName($this->getAccessDeniedModule())
                ->setControllerName($this->getAccessDeniedController())
                ->setActionName($this->getAccessDeniedAction())
                ->setDispatched(false);
        }
    }

    /**
     * Setup the dispatch location for access denied errors
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return Xyster_Controller_Plugin_Acl provides a fluent interface
     */
    public function setAccessDenied( $module, $controller, $action )
    {
        $this->_deniedModule = (string) $module;
        $this->_deniedController = (string) $controller;
        $this->_deniedAction = (string) $action;
        
        return $this;
    }
    
    /**
     * Setup the dispatch location for unauthenticated users
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return Xyster_Controller_Plugin_Acl provides a fluent interface
     */
    public function setLogin( $module, $controller, $action )
    {
        $this->_loginModule = (string) $module;
        $this->_loginController = (string) $controller;
        $this->_loginAction = (string) $action;
                
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