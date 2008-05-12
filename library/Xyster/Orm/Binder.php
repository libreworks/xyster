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
 * @see Xyster_Data_Binder
 */
require_once 'Xyster/Data/Binder.php';
/**
 * @see Xyster_Orm_Binder_Setter
 */
require_once 'Xyster/Orm/Binder/Setter.php';
/**
 * A binder for entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Binder extends Xyster_Data_Binder
{
    /**
     * @var Xyster_Orm_Manager
     */
    protected $_manager;
    
    /**
     * @var Xyster_Orm_Entity_Type
     */
    protected $_type;
        
    protected $_primary = false;
    
    protected static $_ormsetters = array();
    
    /**
     * Creates a new binder for an entity
     *
     * @param Xyster_Orm_Manager $manager
     * @param Xyster_Orm_Entity $entity
     * @param boolean $allowPrimary Optional. True enables binding primary key values
     */
    public function __construct( Xyster_Orm_Manager $manager, Xyster_Orm_Entity $entity, $allowPrimary = false )
    {
        $this->_manager = $manager;
        $this->_target = $entity;
        $this->_primary = $allowPrimary;
        $this->_type = $this->_manager->getMapperFactory()->getEntityType(get_class($this->_target));
        $this->_defaultSetter = $this->_getEntitySetter($this->_type);
    }
    
    /**
     * Bind the values to the target
     *
     * @param array $values
     */
    public function bind( array $values )
    {
        $type = $this->_type;
        $bindable = array();
        
        // go through the bindable values and replace relation foreign keys with actual relations
        $bindKeys = array_keys($values);
        foreach( $type->getRelations() as $relation ) {
            /* @var $relation Xyster_Orm_Relation */
            if ( !$relation->isCollection() ) {
                $keys = $relation->getId(); 
                if ( array_intersect($keys, $bindKeys) == $keys ) {
                    $rname = $relation->getName();
                    $lookupKeys = array();
                    foreach( $keys as $keyname ) {
                        $lookupKeys[$keyname] = $values[$keyname];
                        unset($values[$keyname]);
                    }
                    // this relation's keys are being binded
                    $bindable[$rname] = $this->_manager->get($relation->getTo(), $lookupKeys);
                    // just to make sure it can be set!
                    if ( !$this->isAllowed($rname) ) {
                        $this->_allowed[] = $rname;
                    }
                }
            }
        }
        parent::bind(array_merge($bindable, $values)); // add remaining values
    }

    /**
     * Tests if a field is allowed
     *
     * @param string $field
     * @return boolean
     */
    public function isAllowed( $field )
    {
        return (!in_array($field, $this->_type->getPrimary()) || $this->_primary)
            && parent::isAllowed($field);
    }
    
    /**
     * Gets the setter for an entity type
     *
     * @param Xyster_Orm_Entity_Type $type
     * @return Xyster_Orm_Binder_Setter
     */
    protected function _getEntitySetter( Xyster_Orm_Entity_Type $type )
    {
        $className = $type->getName();
        if ( !array_key_exists($className, self::$_ormsetters) ) {
            self::$_ormsetters[$className] = new Xyster_Orm_Binder_Setter($type);
        }
        return self::$_ormsetters[$className];
    }
}