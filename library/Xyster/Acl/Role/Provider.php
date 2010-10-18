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
namespace Xyster\Acl\Role;
/**
 * Default provider for authentication to authorization 
 *
 * @category  Xyster
 * @package   Xyster_Acl
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Provider implements IProvider
{
    /**
     * Gets a Zend_Acl_Role_Interface based on the identity provided
     *
     * @param mixed $identity An identity as provided by Zend_Auth
     * @return \Zend_Acl_Role_Interface The role to which this identity corresponds
     */
    public function getRole( $identity )
    {
        return new \Zend_Acl_Role($identity);
    }
    
    /**
     * Gets the parent roles for the provided role
     *
     * @param \Zend_Acl_Role_Interface $role
     * @return array An array of \Zend_Acl_Role_Interface objects
     */
    public function getRoleParents( \Zend_Acl_Role_Interface $role )
    {
        return array();
    }
}