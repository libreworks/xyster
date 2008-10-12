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
 * @see Xyster_Orm_Mapping_Class_Abstract
 */
require_once 'Xyster/Orm/Mapping/Class/Abstract.php';
/**
 * A subclass in a table-per-class hierarchy
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapping_Subclass extends Xyster_Orm_Mapping_Class_Abstract
{
    private $_parentClass;
    
    private $_classPersisterType;
    
    /**
     * Creates a new subclass
     * 
     * @param Xyster_Orm_Mapping_Class_Abstract $parent
     */
    public function __construct( Xyster_Orm_Mapping_Class_Abstract $parent )
    {
        $this->_parentClass = $parent;
    }
    
    /**
     * 
     */
    public function addJoin(Xyster_Orm_Mapping_Join $j)
    {
        // @todo
    }
    
    /**
     *  
     */
    public function addProperty(Xyster_Orm_Mapping_Property $p)
    {
        // @todo
    }
    
    /**
     * 
     * @return unknown_type
     */
    public function createForeignKey()
    {
        // @todo
    }

    /**
     * Gets the discriminator
     * 
     * @return Xyster_Orm_Mapping_Value
     */
    public function getDiscriminator()
    {
        // @todo
    }
    
    /**
     * Gets the identifier mapper (a component)
     * 
     * @return Xyster_Orm_Mapping_Component
     */
    public function getIdMapper()
    {
        // @todo
    }
    
    /**
     * Gets the identifier property for this entity
     *
     * @return Xyster_Orm_Mapping_Property
     */
    public function getIdProperty()
    {
        return $this->_identifier;
    }
    
    /**
     * Gets the join closure
     * 
     * @return Iterator
     */
    public function getJoinClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append($this->getParentclass()->getJoinClosureIterator());
        $iters->append(parent::getJoinClosureIterator());
        return $iters;
    }
    
    /**
     * Gets the join closure span
     * 
     * @return integer
     */
    public function getJoinClosureSpan()
    {
        // @todo
    }

    /**
     * Gets the mode of optimistic locking 
     *
     * @return Xyster_Orm_Engine_Versioning
     */
    public function getOptimisticLockMode()
    {
        return $this->_optimisticLock;
    }

    /**
     * Gets the parent class in the hierarchy
     * 
     * @return Xyster_Orm_Mapping_Class_Abstract
     */
    public function getParentclass()
    {
        return $this->_parentClass;
    }
    
    /**
     * Gets the type of persister for this entity type
     *
     * @return Xyster_Type
     */
    public function getPersisterType()
    {
        return $this->_persisterType;
    }
    
    /**
     * Gets the property closure
     * 
     * @return array
     */
    public function getPropertyClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append($this->getParentclass()->getPropertyClosureIterator());
        $iters->append($this->getPropertyIterator());
        return $iters;
    }

    /**
     * Gets the property closure span
     * 
     * @return integer
     */
    public function getPropertyClosureSpan()
    {
        // @todo
    }
    
    /**
     * Gets the top-level class
     * 
     * @return Xyster_Orm_Mapping_Class
     */
    public function getRootClass()
    {
        return $this->getParentclass()->getRootClass();
    }
    
    /**
     * Gets the top-level table
     * 
     * @return Xyster_Db_Table
     */
    public function getRootTable()
    {
        // @todo
    }
    
    /**
     * Gets the subclass id
     *  
     * @return integer
     */
    public function getSubclassId()
    {
        // @todo
    }
    
    /**
     * Gets the table that corresponds to this type
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     * Gets the table closure
     * 
     * @return Iterator of {@link Xyster_Db_Table} objects
     */
    public function getTableClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append($this->getParentclass()->getTableClosureIterator());
        $iters->append(new ArrayIterator(array($this->getTable())));
        return $iters;
    }

    /**
     * Gets the type of tuplizer for this entity type
     *
     * @return Xyster_Type
     */
    public function getTuplizerType()
    {
        return $this->_tuplizerType;
    }
    
    /**
     * Gets the version property or null if none
     *
     * @return Xyster_Orm_Mapping_Property
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * Gets the 'WHERE' SQL filter
     * 
     * @return string
     */
    public function getWhere()
    {
        // @todo
    }
    
    /**
     * Whether this type has an identifier property
     * 
     * @return boolean
     */
    public function hasIdProperty()
    {
        return $this->getParentclass()->hasIdProperty();
    }
    
    /**
     * Gets whether the join specified is part of the entity or its parents
     * 
     * @param Xyster_Orm_Mapping_Join $join
     * @return boolean
     */
    public function isClassJoin(Xyster_Orm_Mapping_Join $join)
    {
        // @todo
    }

    /**
     * Gets whether the table specified is part of the entity or its parents
     * 
     * @param Xyster_Db_Table $table
     * @return boolean
     */
    public function isClassTable(Xyster_Db_Table $table)
    {
        // @todo
    }
    
    /**
     * Whether the discriminator column is insertable
     * 
     * @return boolean
     */
    public function isDiscriminatorInsertable()
    {
        // @todo
    }
    
    /**
     * Whether this type is inherited
     * 
     * @return boolean
     */
    public function isInherited()
    {
        return true;
    }
    
    /**
     * Whether this entity is a joined subclass
     *  
     * @return boolean
     */
    public function isJoinedSubclass()
    {
        // @todo
    }
    
    /**
     * Whether the mapped type is mutable
     *
     * @return boolean
     */
    public function isMutable()
    {
        return $this->_mutable;
    }
    
    /**
     * Gets whether the entity has a version property
     *
     * @return boolean
     */
    public function isVersioned()
    {
        return $this->_version !== null;
    }
    
    /**
     * Sets the parent class of this entity
     * 
     * @param Xyster_Orm_Mapping_Class_Abstract $class
     * @return Xyster_Orm_Mapping_Subclass provides a fluent interface
     */
    public function setParentclass(Xyster_Orm_Mapping_Class_Abstract $class)
    {
        // @todo
    }
    
    /**
     * Sets the persister for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Subclass provides a fluent interface
     */
    public function setPersisterType( Xyster_Type $type )
    {
        $this->_persisterType = $type;
        return $this;
    }
    
    /**
     * Sets the type of tuplizer for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Subclass provides a fluent interface
     */
    public function setTuplizerType( Xyster_Type $type )
    {
        $this->_tuplizerType = $type;
        return $this;
    }
    
    /**
     * Adds a subclass join
     * 
     * @param Xyster_Orm_Mapping_Join $j
     */
    protected function _addSubclassJoin(Xyster_Orm_Mapping_Join $j)
    {
        // @todo
    }
    
    /**
     * Adds a subclass property
     * @param Xyster_Orm_Mapping_Property $p
     */
    protected function _addSubclassProperty(Xyster_Orm_Mapping_Property $p)
    {
        // @todo
    }

    /**
     * Adds a subclass table
     * 
     * @param Xyster_Db_Table $table
     */
    protected function _addSubclassTable(Xyster_Db_Table $table)
    {
        // @todo
    }
}