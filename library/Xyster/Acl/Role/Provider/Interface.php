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
 * Interface for combining authentication and authorization
 * 
 * If desired, there shouldn't be any problem with the Role_Provider caching or
 * persisting the Roles it creates, but this is left up to the implementor. 
 *
 * @category  Xyster
 * @package   Xyster_Acl
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Acl_Role_Provider_Interface
{
    /**
     * Gets a Zend_Acl_Role_Interface based on the identity provided
     *
     * @param mixed $identity An identity as provided by Zend_Auth
     * @return Zend_Acl_Role_Interface The role to which this identity corresponds
     */
    function getRole( $identity );
    
    /**
     * Gets the parent roles for the provided role
     *
     * @param Zend_Acl_Role_Interface $role
     * @return array An array of Zend_Acl_Role_Interface objects
     */
    function getRoleParents( Zend_Acl_Role_Interface $role );
}
