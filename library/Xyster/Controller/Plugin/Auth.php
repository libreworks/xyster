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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
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
     * Creates a new auth plugin
     *
     * @param Zend_Auth_Adapter_Interface $adapter
     * @param Zend_Acl $acl
     */
    public function __construct( Zend_Auth_Adapter_Interface $adapter = null, Zend_Acl $acl = null )
    {
        $this->_adapter = $adapter;
        $this->_acl = $acl;
    }
    
    /**
     * Called before Zend_Controller_Front determines the dispatch route
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        if ( !$auth->hasIdentity() && $this->_adapter ) {
            $auth->authenticate($this->_adapter);
        }

        $role = $this->getRole();
            
        if ( $role instanceof Zend_Acl_Role_Interface && $this->_acl && 
            !$this->_acl->hasRole($role) ) {
            $this->_acl->addRole($role, $this->getRoleProvider()->getRoleParents($role));
        }
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
     * Gets the role provider used to translato the identity into a role
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
     * Sets the ACL to which the authenticated role will be added
     *
     * @param Zend_Acl $acl
     * @return Xyster_Controller_Plugin_Auth
     */
    public function setAcl( Zend_Acl $acl )
    {
        $this->_acl = $acl;
        return $this;
    }
    
    /**
     * Sets the role provider used to translate the identity into a role
     *
     * @param Xyster_Acl_Role_Provider_Interface $provider
     * @return Xyster_Controller_Plugin_Auth
     */
    public function setRoleProvider( Xyster_Acl_Role_Provider_Interface $provider )
    {
        $this->_provider = $provider;
        return $this;
    }
}