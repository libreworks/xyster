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
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';
/**
 * @see Xyster_Orm_Entity_Resource
 */
require_once 'Xyster/Orm/Entity/Resource.php';
/**
 * A stub entity for plugin testing
 *
 */
class Xyster_Orm_Plugin_TestEntity extends Xyster_Orm_Entity_Resource
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
     * Gets the primary key of the entity
     *
     * @param boolean $base
     * @return array
     */
    public function getPrimaryKey( $base = false )
    {
        return array() + $this->_values;
    }
}
