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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Data_Set
 */
require_once 'Xyster/Data/Set.php';
/**
 * 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Set extends Xyster_Data_Set
{
    /**
     * The entity to which this set belongs
     * 
     * This is only set if the set is a many relation, not if pulling entities
     * from the data store 
     *
     * @var Xyster_Orm_Entity
     */
    protected $_entity;
    /**
     * The relation of which the set is a result
     * 
     * This is only set if the set is a many relation, not if pulling entities
     * from the data store 
     *
     * @var Xyster_Orm_Relation
     */
    protected $_relation;
    /**
     * The initial snapshot of the set
     *
     * @var array
     */
    protected $_base = array();

    /**
     * Creates a new entity set
     * 
     * @param Xyster_Collection_Interface $values
     */
    public function __construct( Xyster_Collection_Interface $values = null )
    {
        parent::__construct($values);
        if ( $values ) {
            $this->_base = $this->_items;
        }
    }

    /**
     * Adds an item to the set
     * 
     * This collection doesn't accept duplicate values, and will return false
     * if the provided value is already in the collection.
     * 
     * It can only accept Xyster_Orm_Entity objects, otherwise it will throw a 
     * Xyster_Data_Set_Exception.
     *
     * @param mixed $item The item to add
     * @return boolean Whether the set changed as a result of this method
     * @throws Xyster_Data_Set_Exception if the collection cannot contain the value
     */
    public function add( $item )
    {
        $class = $this->getEntityName();
        if (! $item instanceof $class ) {
            require_once 'Xyster/Data/Set/Exception.php';
            throw new Xyster_Data_Set_Exception('This set can only contain Xyster_Orm_Entity objects');
        }
        
        /*
         * Relate the owning entity to the new item
         */
        if ( $this->_relation && $this->_entity ) {
            $this->_relation->relate($this->_entity, $item);
        }

        return parent::add($item);
    }
    /**
     * Gets the class name of the entity supported by this set
     *
     * By default this method will return the class name minus the 'Set' suffix.
     * 
     * For instance, for 'UserSet' this method will return 'User'.
     * 
     * Class authors can overwrite this method if their entity names aren't 
     * the same as the set name.
     * 
     * @return string
     */
    public function getEntityName()
    {
        return substr(get_class($this),0,-3);
    }
    /**
     * Gets the base state of this set
     *
     * @return array
     */
    public function getBase()
    {
        return $this->_base;
    }
    /**
     * Gets any added entities since creation
     * 
     * @return array
     */
    public function getDiffAdded()
    {
        return array_diff($this->_items,$this->_base);
    }
    /**
     * Gets any removed entities since creation
     * 
     * @return array
     */
    public function getDiffRemoved()
    {
        return array_diff($this->_base,$this->_items);
    }
    /**
     * Gets the entity related to this set
     * 
     * @return Xyster_Orm_Entity the related entity
     */
    public function getRelatedEntity()
    {
        return $this->_entity;
    }
    /**
     * Gets the relation for this set
     * 
     * @return Xyster_Orm_Relation the relation
     */
    public function getRelation()
    {
        return $this->_relation;
    }
    /**
     * Relates the set to an entity and relationship
     * 
     * @param Xyster_Orm_Relation $relation The relation
     * @param Xyster_Orm_Entity $entity The entity
     * @throws Xyster_Orm_Exception if the set is already associated
     */
    public function relateTo( Xyster_Orm_Relation $relation, Xyster_Orm_Entity $entity )
    {
        if ( $this->_relation instanceof Xyster_Orm_Relation ||
            $this->_entity instanceof Xyster_Orm_Entity ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('This set is already related to an entity');
        }
        $this->_relation = $relation;
        $this->_entity = $entity;
    }
}