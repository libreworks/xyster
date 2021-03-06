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
namespace Xyster\Acl;
/**
 * An access control list that can dynamically build its own rules
 *
 * @category  Xyster
 * @package   Xyster_Acl
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Acl extends \Zend_Acl
{
    /**
     * The authorizers
     *
     * @var IAuthorizer[]
     */
    protected $_authorizers = array();

    /**
     * Adds an authorizer to the ACL
     *
     * @param IAuthorizer $authorizer
     * @return Acl provides a fluent interface
     */
    public function addAuthorizer(IAuthorizer $authorizer)
    {
        if (!in_array($authorizer, $this->_authorizers, true)) {
            $this->_authorizers[] = $authorizer;
        }
        return $this;
    }

    /**
     * Throws an exception if the Role is denied access to the Resource
     *
     * @param  Zend_Acl_Role_Interface|string     $role
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @throws Zend_Acl_Exception
     */
    public function assertAllowed($role = null, $resource = null, $privilege = null)
    {
        if ($this->isAllowed($role, $resource, $privilege)) {
            return true;
        }

        $msg = 'Insufficient permissions: ';
        $msg .= ( $role instanceof \Zend_Acl_Role_Interface ) ?
                $role->getRoleId() : $role;
        $msg .= ' -> ';
        $msg .= ( $resource instanceof \Zend_Acl_Resource_Interface ) ?
                $resource->getResourceId() : $resource;
        if ($privilege) {
            $msg .= ' (' . $privilege . ')';
        }

        throw new \Zend_Acl_Exception($msg);
    }

    /**
     * Gets the authorizer for a resource
     * 
     * If more than one authorizer applies to a resource, only the first is
     * returned (in the order in which they were added.  First in, first out).
     * 
     * If none apply, null is returned.
     *
     * @param Zend_Acl_Resource_Interface|string $resource
     * @return IAuthorizer
     */
    public function getAuthorizer($resource = null)
    {
        $resource = ( $resource !== null ) ? $this->get($resource) : null;
        $return = null;

        if ($resource !== null) {
            foreach ($this->_authorizers as $authorizer) {
                /* @var $authorizer IAuthorizer */
                if ($authorizer->applies($resource)) {
                    $return = $authorizer;
                }
            }
        }

        return $return;
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * {@inherit}
     *
     * @param  Zend_Acl_Role_Interface|string     $role
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @return boolean
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $role = ( $role !== null ) ? $this->getRole($role) : null;
        $resource = ( $resource !== null ) ? $this->get($resource) : null;

        if ($this->_getRuleType($resource, $role, $privilege) === null) {
            if ($authorizer = $this->getAuthorizer($resource)) {
                $allowed = $authorizer->isAllowed($role, $resource, $privilege);

                if ($allowed) {
                    $this->allow($role, $resource, $privilege);
                } else {
                    $this->deny($role, $resource, $privilege);
                }

                return $allowed;
            }
        }
        return parent::isAllowed($role, $resource, $privilege);
    }
}