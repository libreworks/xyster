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
 * The top-level class in a hierarchy
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapping_Class extends Xyster_Orm_Mapping_Class_Abstract
{
    /**
     * @var Xyster_Orm_Mapping_Value
     */
    protected $_discriminator;
    
    /**
     * @var boolean
     */
    protected $_discriminatorInsertable = true;
    
    /**
     * @var Xyster_Orm_Mapping_Property
     */
    protected $_identifier;
        
    /**
     * @var boolean
     */
    protected $_mutable = true;
    
    /**
     * @var integer
     */
    protected $_nextSubclassId = 0;
    
    /**
     * @var Xyster_Type
     */
    protected $_persisterType;
        
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
    /**
     * @var Xyster_Orm_Mapping_Property
     */
    protected $_version;
    
    /**
     * @var string
     */
    protected $_where;

    /**
     * Gets the discriminator
     * 
     * @return Xyster_Orm_Mapping_Value
     */
    public function getDiscriminator()
    {
        return $this->_discriminator;
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
     * Gets the identifier tables
     * 
     * @return Xyster_Collection_Set_Interface
     */
    public function getIdTables()
    {
        $tables = array();
        foreach( $this->getSubclassClosure() as $sub ) {
            $tables[] = $sub->getIdTable();
        }
        return $tables;
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
        return null;
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
     * @return Iterator
     */
    public function getPropertyClosureIterator()
    {
        return $this->getPropertyIterator();
    }
    
    /**
     * Gets the top-level class
     * 
     * @return Xyster_Orm_Mapping_Class
     */
    public function getRootClass()
    {
        return $this;
    }
    
    /**
     * Gets the top-level table
     * 
     * @return Xyster_Db_Table
     */
    public function getRootTable()
    {
        return $this->getTable();
    }
    
    /**
     * Gets the subclass id
     *  
     * @return integer
     */
    public function getSubclassId()
    {
        return 0;
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
        return new ArrayIterator(array($this->getTable()));
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
        return $this->_where;
    }
    
    /**
     * Whether this type has an identifier property
     * 
     * @return boolean
     */
    public function hasIdProperty()
    {
        return $this->_identifier !== null;
    }
    
    /**
     * Whether the discriminator column is insertable
     * 
     * @return boolean
     */
    public function isDiscriminatorInsertable()
    {
        return $this->_discriminatorInsertable;
    }
    
    /**
     * Whether this type is inherited
     * 
     * @return boolean
     */
    public function isInherited()
    {
        return false;
    }
    
    /**
     * Whether this entity is a joined subclass
     *  
     * @return boolean
     */
    public function isJoinedSubclass()
    {
        return false;
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
     * Sets the discriminator
     * 
     * @param Xyster_Orm_Mapping_Value $value
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setDiscriminator( Xyster_Orm_Mapping_Value $value )
    {
        $this->_discriminator = $value;
        return $this;
    }
    
    /**
     * Sets the discriminator's insertability
     * 
     * @param boolean $insertable
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setDiscriminatorInsertable( $insertable = true )
    {
        $this->_discriminatorInsertable = $insertable;
        return $this;
    }
    
    /**
     * Sets the identifier property
     *
     * @param Xyster_Orm_Mapping_Property $prop
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setIdProperty( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_identifier = $prop;
        return $this;
    }
    
    /**
     * Sets whether this type is mutable or not
     *
     * @param boolean $mutable
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setMutable( $mutable = true )
    {
        $this->_mutable = $mutable;
        return $this;
    }
    
    /**
     * Sets the persister for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setPersisterType( Xyster_Type $type )
    {
        $this->_persisterType = $type;
        return $this;
    }
    
    /**
     * Sets the table for this type
     *
     * @param Xyster_Db_Table $table The table
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setTable( Xyster_Db_Table $table )
    {
        $this->_table = $table;
        return $this;
    }
    
    /**
     * Sets the version property for this type
     *
     * @param Xyster_Orm_Mapping_Property $prop The property
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setVersion( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_version = $prop;
        return $this;
    }

    /**
     * Sets the 'WHERE' SQL filter
     * 
     * @param string $where
     * @return Xyster_Orm_Mapping_Class provides a fluent interface
     */
    public function setWhere( $where )
    {
        $this->_where = $where;
        return $this;
    }
}