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
 * Zend_Auth
 */
require_once 'Zend/Auth.php';
/**
 * Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';
/**
 * Authentication plugin
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Plugins
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * The acl
     *
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * The Auth adapter
     *
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_adapter;
    
    /**
     * The dispatch action for authentication failure 
     *
     * @var string
     */
    protected $_failAction = 'index';
    
    /**
     * The dispatch controller for authentication failure 
     *
     * @var string
     */
    protected $_failController = 'login';
    
    /**
     * The dispatch module for authentication failure 
     *
     * @var string
     */
    protected $_failModule;
    
    /**
     * The role provider
     *
     * @var Xyster_Acl_Role_Provider_Interface
     */
    protected $_provider;
    
    /**
     * The current authenticated role
     *
     * @var Zend_Acl_Role_Interface
     */
    protected $_role;

    /**
     * Whether the 'routeStartup' method has already been called
     *
     * @var boolean
     */
    protected $_started = false;
    
    /**
     * The dispatch action for authentication success
     *
     * @var string
     */
    protected $_successAction = 'success';

    /**
     * The dispatch controller for authentication success
     *
     * @var string
     */
    protected $_successController = 'login';

    /**
     * The dispatch module for authentication success
     *
     * @var string
     */
    protected $_successModule;
    
    /**
     * Called before Zend_Controller_Front determines the dispatch route
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_started = true;
        
        $this->_authenticate();
    }
    
    /**
     * Gets the ACL assigned to the plugin
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }
    
    /**
     * Gets the dispatch action for authentication failure
     *
     * @return string
     */
    public function getFailAction()
    {
        return $this->_failAction;
    }
    
    /**
     * Gets the dispatch controller for authentication failure
     *
     * @return string
     */
    public function getFailController()
    {
        return $this->_failController;
    }
    
    /**
     * Gets the dispatch module for authentication failure
     *
     * @return string
     */
    public function getFailModule()
    {
        if ($this->_failModule === null) {
            require_once 'Zend/Controller/Front.php';
            $this->_failModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_failModule;
    }
    
    /**
     * Gets the authenticated role
     *
     * @return Zend_Acl_Role_Interface
     */
    public function getRole()
    {
        $auth = Zend_Auth::getInstance();
        if ( !$this->_role && $auth->hasIdentity() ) {
            $identity = $auth->getIdentity();
            $this->_role = $this->getRoleProvider()->getRole($identity);
        }
        
        return $this->_role;
    }
    
    /**
     * Gets the role provider used to translate the identity into a role
     *
     * @return Xyster_Acl_Role_Provider_Interface
     */
    public function getRoleProvider()
    {
        if ( !$this->_provider ) {
            require_once 'Xyster/Acl/Role/Provider.php';
            $this->_provider = new Xyster_Acl_Role_Provider();
        }
        return $this->_provider;
    }
    
    /**
     * Gets the dispatch action for authentication success
     *
     * @return string
     */
    public function getSuccessAction()
    {
        return $this->_successAction;
    }
    
    /**
     * Gets the dispatch controller for authentication success
     *
     * @return string
     */
    public function getSuccessController()
    {
        return $this->_successController;
    }
    
    /**
     * Gets the dispatch module for authentication success
     *
     * @return string
     */
    public function getSuccessModule()
    {
        if ($this->_successModule === null) {
            require_once 'Zend/Controller/Front.php';
            $this->_successModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_successModule;
    }
    
    /**
     * Sets the ACL to which the authenticated role will be added
     *
     * @param Zend_Acl $acl
     * @return Xyster_Controller_Plugin_Auth provides a fluent interface
     */
    public function setAcl( Zend_Acl $acl )
    {
        $this->_acl = $acl;
        return $this;
    }
    
    /**
     * Sets the authentication adapter 
     *
     * @param Zend_Auth_Adapter_Interface $adapter
     * @return Xyster_Controller_Plugin_Auth provides a fluent interface
     */
    public function setAuthAdapter( Zend_Auth_Adapter_Interface $adapter )
    {
        $this->_adapter = $adapter;
        if ( $this->_started ) {
            // if the plugin already tried to authenticate, use this new adapter
            $this->_authenticate();
        }
        return $this;
    }
    
    /**
     * Sets the dispatch location for a failed authentication
     *
     * @param string $module The dispatch module
     * @param string $controller The dispatch controller
     * @param string $action The dispatch action
     * @return Xyster_Controller_Plugin_Auth provides a fluent interface
     */
    public function setFailure( $module, $controller, $action )
    {
        $this->_failModule = $module;
        $this->_failController = $controller;
        $this->_failAction = $action;
        
        return $this;
    }
        
    /**
     * Sets the role provider used to translate the identity into a role
     *
     * @param Xyster_Acl_Role_Provider_Interface $provider
     * @return Xyster_Controller_Plugin_Auth provides a fluent interface
     */
    public function setRoleProvider( Xyster_Acl_Role_Provider_Interface $provider )
    {
        $this->_provider = $provider;
        return $this;
    }
    
    /**
     * Sets the dispatch location for a successful authentication
     *
     * @param string $module The dispatch module
     * @param string $controller The dispatch controller
     * @param string $action The dispatch action
     * @return Xyster_Controller_Plugin_Auth provides a fluent interface
     */
    public function setSuccess( $module, $controller, $action )
    {
        $this->_successModule = $module;
        $this->_successController = $controller;
        $this->_successAction = $action;
        
        return $this;
    }
    
    /**
     * Does the actual auth work
     *
     */
    protected function _authenticate()
    {
        $auth = Zend_Auth::getInstance();
        if ( !$auth->hasIdentity() ) {
        // no need to call the adapter if the user is authenticated
            if ( !$this->_adapter ) {
                // if we don't have an adapter, there's nothing to do
                return;
            } else {
                $result = $auth->authenticate($this->_adapter);
                $request = $this->getRequest();
                if ( $result->isValid() ) {
                    // if the authentication is valid send to the success action
                    $request->setModuleName($this->getSuccessModule())
                        ->setControllerName($this->getSuccessController())
                        ->setActionName($this->getSuccessAction())
                        ->setDispatched(false);
                } else {
                    // if the authentication fails send to the failure action
                    $request->setModuleName($this->getFailModule())
                        ->setControllerName($this->getFailController())
                        ->setActionName($this->getFailAction())
                        ->setParam('result', $result)
                        ->setDispatched(false);
                    return;
                }
            }
        }

        $role = $this->getRole();
            
        if ( $role instanceof Zend_Acl_Role_Interface && $this->_acl && 
            !$this->_acl->hasRole($role) ) {
            $this->_acl->addRole($role, $this->getRoleProvider()->getRoleParents($role));
        }
    }
}