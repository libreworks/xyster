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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Entity_Lookup_Interface
 */
require_once 'Xyster/Orm/Entity/Lookup/Interface.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Abstract base lookup
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Entity_Lookup_Abstract implements Xyster_Orm_Entity_Lookup_Interface
{
    /**
     * The name of the lookup field on the entity
     * @var string
     */
    private $_name;
    
    /**
     * The entity type supported by this lookup
     * @var Xyster_Orm_Entity_Type
     */
    private $_entity;
    
    /**
     * Creates a new lookup
     * 
     * @param Xyster_Orm_Entity_Type $type The type of entity supported
     */
    public function __construct( Xyster_Orm_Entity_Type $type, $name )
    {
        $this->_entity = $type;
        if ( in_array($name, $type->getMembers()) ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('Field name already exists: ' . $name);
        }
        $this->_name = $name;
    }
    
    /**
     * Gets the entity type supported by this lookup
     *
     * @return Xyster_Orm_Entity_Type
     */
    public function getEntityType()
    {
        return $this->_entity;
    }
    
    /**
     * Gets the field name of this lookup on the entity class 
     *
     * @return string The field name
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Checks the incoming entity for the correct type
     *
     * @param Xyster_Orm_Entity $entity
     */
    protected function _checkEntity( Xyster_Orm_Entity $entity )
    {
        if ( !$this->getEntityType()->isInstance($entity) ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('You must provide a ' . $this->getEntityType() . ' object');
        }
    }
    
    /**
     * Sets any fields affected by changing the value of this lookup
     * 
     * @param Xyster_Orm_Entity $entity
     * @param mixed $value The new value for the lookup
     */
    protected function _checkSet( Xyster_Orm_Entity $entity, $value )
    {
        $this->_checkEntity($entity);
        if ( !$this->getType()->isInstance($value) ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('You must provide a ' . $this->getType() . ' object');
        }
    }
}