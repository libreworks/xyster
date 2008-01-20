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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Acl_RoleProvider_Interface
 */
require_once 'Xyster/Acl/Role/Provider/Interface.php';
/**
 * Zend_Acl_Role
 */
require_once 'Zend/Acl/Role.php';
/**
 * Default provider for authentication to authorization 
 *
 * @category  Xyster
 * @package   Xyster_Acl
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Acl_Role_Provider implements Xyster_Acl_Role_Provider_Interface
{
    /**
     * Gets a Zend_Acl_Role_Interface based on the identity provided
     *
     * @param mixed $identity An identity as provided by Zend_Auth
     * @return Zend_Acl_Role_Interface The role to which this identity corresponds
     */
    public function getRole( $identity )
    {
        return new Zend_Acl_Role($identity);
    }
    
    /**
     * Gets the parent roles for the provided role
     *
     * @param Zend_Acl_Role_Interface $role
     * @return array An array of Zend_Acl_Role_Interface objects
     */
    public function getRoleParents( Zend_Acl_Role_Interface $role )
    {
        return array();
    }
}