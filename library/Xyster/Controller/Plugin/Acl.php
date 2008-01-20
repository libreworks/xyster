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
 * @subpackage Plugins
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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
 * Zend_Acl
 */
require_once 'Zend/Acl.php';
/**
 * Authorization plugin
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Plugins
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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
     * Action to use for errors; defaults to 'error'
     * @var string
     */
    protected $_deniedAction = 'error';
    
    /**
     * Controller to use for errors; defaults to 'error'
     * @var string
     */
    protected $_deniedController = 'error';
    
    /**
     * Module to use for errors; defaults to default module in dispatcher
     * @var string
     */
    protected $_deniedModule;
    
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
     * Holds the rules passed to 'setRules' until dispatchLoopStartup 
     *
     * @var array
     */
    protected $_rules = array();
    
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
     * @param Zend_Acl_Role_Interface|string $role
     * @param string $module The module name
     * @param string $controller The controller name
     * @param string $action The action name
     * @return Xyster_Controller_Action_Helper_Acl provides a fluent interface
     */
    public function allow( $role, $module = null, $controller = null, $action = null )
    {
        $resource = $this->_getResource($module, $controller, $action);
        $this->_acl->allow($role, $resource);
        return $this;
    }

    /**
     * Denies access to an action by a role
     * 
     * Passing null for the role will deny all users access to the action.
     * 
     * Passing null for the module will deny everything to the role supplied. 
     * Specifying a module but leaving controller and action null will deny
     * access to all controllers in the specified module.  Specifying a module
     * and a controller but leaving action null will deny access to all actions
     * in the specified controller.
     *
     * @param Zend_Acl_Role_Interface|string $role
     * @param string $module The module name
     * @param string $controller The controller name
     * @param string $action The action name
     * @return Xyster_Controller_Action_Helper_Acl provides a fluent interface
     */
    public function deny( $role, $module = null, $controller = null, $action = null )
    {
        $resource = $this->_getResource($module, $controller, $action);
        $this->_acl->deny($role, $resource);
        return $this;
    }
    
    /**
     * Called before Zend_Controller_Front enters its dispatch loop
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        foreach( $this->_rules as $rule ) {
            $role = isset($rule['role']) ? $rule['role'] : null;
            $module = isset($rule['module']) ? $rule['module'] : null;
            $controller = isset($rule['controller']) ? $rule['controller'] : null;
            $action = isset($rule['action']) ? $rule['action'] : null;
            
            if ( isset($rule['type']) && $rule['type'] == Zend_Acl::TYPE_DENY ) {
                $this->deny($role, $module, $controller, $action);
            } else {
                $this->allow($role, $module, $controller, $action);
            }
        }
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
     * Retrieve the current not-authenticated action
     *
     * @return string
     */
    public function getLoginAction()
    {
        return $this->_loginAction;
    }
    
    /**
     * Retrieve the current not-authenticated controller
     *
     * @return string
     */
    public function getLoginController()
    {
        return $this->_loginController;
    }

    /**
     * Retrieve the current not-authenticated module
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
     * Sets multiple rules simultaneously
     * 
     * Each element in the $rules array should be itself an associative array
     * containing the following keys:
     *  - type : either Zend_Acl::TYPE_ALLOW or Zend_Acl::TYPE_DENY 
     *  - role : the string role ID
     *  - module : the module name
     *  - controller : the controller name
     *  - action : the action name
     * 
     * If 'type' is omitted or is null, Zend_Acl::TYPE_ALLOW is assumed.  If any
     * of the other keys are omitted, null is assumed.  See {@link allow} and
     * {@link deny} for the behavior when null is specified for these values. 
     *
     * @param array $rules
     * @return Xyster_Controller_Plugin_Acl provides a fluent interface
     */
    public function setRules( array $rules )
    {
        $this->_rules = $rules;
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