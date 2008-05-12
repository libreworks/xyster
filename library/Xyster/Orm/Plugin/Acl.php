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
 * @package   Xyster_Orm
 * @subpackage Plugins
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Plugin_Abstract
 */
require_once 'Xyster/Orm/Plugin/Abstract.php';
/**
 * An ORM plugin for authorization checks 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @subpackage Plugins
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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