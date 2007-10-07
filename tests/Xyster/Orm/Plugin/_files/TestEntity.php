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
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';
/**
 * Zend_Acl_Resource_Interface
 */
require_once 'Zend/Acl/Resource/Interface.php';
/**
 * A stub entity for plugin testing
 *
 */
class Xyster_Orm_Plugin_TestEntity extends Xyster_Orm_Entity implements Zend_Acl_Resource_Interface
{
    /**
     * Creates a sample entity
     *
     * @param array $values  Doesn't do anything
     */
    public function __construct( array $values = null )
    {
        $this->_values = array('test'=>1);
    }
    
    /**
     * Gets the ACL resource id
     *
     * @return string
     */
    public function getResourceId()
    {
        return spl_object_hash($this);
    }
}
