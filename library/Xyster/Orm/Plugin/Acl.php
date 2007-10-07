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
 * @package   Xyster_Orm
 * @subpackage Plugins
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Plugin_Abstract
 */
require_once 'Xyster/Orm/Plugin/Abstract.php';
/**
 * The base ORM plugin object 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @subpackage Plugins
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Plugin_Acl extends Xyster_Orm_Plugin_Abstract
{
    /**
     * The access control list 
     *
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * Creates a new ACL plugin
     *
     * @param Zend_Acl $acl
     */
    public function __construct( Zend_Acl $acl )
    {
        $this->_acl = $acl;
    }
    
    /**
     * Gets the access control list
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }
    
    /**
     * Loads an entity into the ACL if it's a {@link Zend_Acl_Resource_Interface} 
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postLoad( Xyster_Orm_Entity $entity )
    {
        if ( $entity instanceof Zend_Acl_Resource_Interface
            && !$this->_acl->has($entity) ) {
            $this->_acl->add($entity);
        }
    }
}