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
 * @see Xyster_Db_Table
 */
require_once 'Xyster/Db/Table.php';
/**
 * @see Xyster_Orm_Mapping_Property
 */
require_once 'Xyster/Orm/Mapping/Property.php';
/**
 * @see Xyster_Orm_Engine_Versioning
 */
require_once 'Xyster/Orm/Engine/Versioning.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * The persistence and meta information about an entity type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Mapping_Class_Abstract
{
    /**
     * @var string
     */
    protected $_discriminatorValue;
    
    /**
     * @var Xyster_Orm_Mapping_Component
     */
    protected $_idComponent;
    
    /**
     * @var array
     */
    protected $_joins = array();
    
    /**
     * @var boolean
     */
    protected $_lazy = false;
    
    /**
     * @var Xyster_Type
     */
    protected $_loaderType;
    
    /**
     * @var string
     */
    protected $_name;

    /**
     * @var Xyster_Orm_Engine_Versioning
     */
    protected $_optimisticLock;    
    
    /**
     * @var array
     */
    protected $_properties = array();
    
    /**
     * @var Xyster_Type
     */
    protected $_proxyInterface;
    
    /**
     * @var boolean
     */
    protected $_selectBeforeUpdate = false;
    
    /**
     * @var array
     */
    protected $_subclasses = array();
    
    protected $_subclassJoins = array();
    
    protected $_subclassProperties = array();
    
    protected $_subclassTables = array();
    
    /**
     * @var Xyster_Type
     */
    protected $_tuplizerType;
        
    /**
     * Adds a join to the class
     * 
     * @param Xyster_Orm_Mapping_Join $join
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function addJoin( Xyster_Orm_Mapping_Join $join )
    {
        $this->_joins[] = $join;
        $join->setMappedClass($this);
        return $this;
    }
    
    /**
     * Adds a property to the class
     *
     * @param Xyster_Orm_Mapping_Property $prop
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function addProperty( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_properties[$prop->getName()] = $prop;
        return $this;
    }
    
    /**
     * Adds a subclass to this one
     * 
     * @param Xyster_Orm_Mapping_Subclass $subclass
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function addSubclass( Xyster_Orm_Mapping_Subclass $subclass )
    {
        $this->_subclasses[] = $subclass;
        return $this;
    }
    
    /**
     * Gets the type of entity this class represents
     * 
     * @return string The class name
     */
    public function getClassName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the immediate subclasses of this entity
     * 
     * @return Iterator
     */
    public function getDirectSubclasses()
    {
        return new ArrayIterator($this->_subclasses);
    }
    
    /**
     * Gets the discriminator
     * 
     * @return Xyster_Orm_Mapping_Value
     */
    abstract public function getDiscriminator();
    
    /**
     * Gets the discriminator value
     * 
     * @return string
     */
    public function getDiscriminatorValue()
    {
        return $this->_discriminatorValue;
    }
    
    /**
     * Gets the identifier component
     * 
     * @return Xyster_Orm_Mapping_Component
     */
    public function getIdComponent()
    {
        return $this->_idComponent;
    }
    
    /**
     * Gets the identifier property for this entity
     *
     * @return Xyster_Orm_Mapping_Property
     */
    abstract public function getIdProperty();
    
    /**
     * Gets the identifying table
     *  
     * @return Xyster_Db_Table
     */
    public function getIdTable()
    {
        return $this->getRootTable();
    }
    
    /**
     * Gets the join closure
     * 
     * @return Iterator
     */
    public function getJoinClosureIterator()
    {
        return new ArrayIterator($this->_joins);
    }
    
    /**
     * Gets the join closure span
     * 
     * @return integer
     */
    public function getJoinClosureSpan()
    {
        return count($this->_joins);
    }
    
    /**
     * Gets the joins
     * 
     * @return Iterator
     */
    public function getJoinIterator()
    {
        return new ArrayIterator($this->_joins);
    }
    
    /**
     * Gets the number of the join
     * @param Xyster_Orm_Mapping_Property $prop
     * @return integer
     */
    public function getJoinNumber( Xyster_Orm_Mapping_Property $prop )
    {
        $result = 1;
        foreach( $this->getSubclassJoinClosure() as $join ) {
            if ( $join->containsProperty($prop) ) {
                return $result;
            }
            $result++;
        }
        return 0;
    }
    
    /**
     * Gets the class key (used for joins)
     * 
     * @return Xyster_Orm_Mapping_Value
     */
    public abstract function getKey();
    
    /**
     * Gets the key closure
     * 
     * @return Iterator
     */
    public abstract function getKeyClosureIterator();
    
    /**
     * Gets the type of loader for this entity type
     *
     * @return Xyster_Type
     */
    public function getLoaderType()
    {
        return $this->_loaderType;
    }
    
    /**
     * Gets the type of entity this class represents
     *
     * @return Xyster_Type The type
     */
    public function getMappedType()
    {
        return new Xyster_Type($this->_name);
    }
    
    /**
     * Gets the mode of optimistic locking 
     *
     * @return Xyster_Orm_Engine_Versioning
     */
    abstract public function getOptimisticLockMode();
    
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
     * Gets the properties in the class
     *
     * @return Iterator of {@link Xyster_Orm_Mapping_Property} objects
     */
    public function getPropertyIterator()
    {
        $iters = new AppendIterator();
        $iters->append(new ArrayIterator($this->_properties));
        foreach( $this->_joins as $join ) {
            /* @var $join Xyster_Orm_Mapping_Join */
            $iters->append($join->getPropertyIterator());
        }
        return $iters;
    }
    
    /**
     * Gets a property by name
     *
     * @param string $name
     * @throws Xyster_Orm_Mapping_Exception if the property isn't found
     * @return Xyster_Orm_Mapping_Property
     */
    public function getProperty( $name )
    {
        if ( !array_key_exists($name, $this->_properties) ) {
            require_once 'Xyster/Orm/Mapping/Exception.php';
            throw new Xyster_Orm_Mapping_Exception('Property not found: ' . $name);
        }
        return $this->_properties[$name];
    }
    
    /**
     * Gets the property closure
     * 
     * @return Iterator
     */
    abstract public function getPropertyClosureIterator();
    
    /**
     * Gets the property closure span
     * 
     * @return integer
     */
    public function getPropertyClosureSpan()
    {
        $span = count($this->_properties);
        foreach( $this->_joins as $join ) {
            $span += $join->getPropertySpan();
        }
        return $span;
    }
    
    /**
     * Gets the type used for proxying
     * 
     * @return Xyster_Type
     */
    public function getProxyInterfaceType()
    {
        return $this->_proxyInterface;
    }
    
    /**
     * Gets a property by name
     * 
     * @param string $propertyPath
     * @return Xyster_Orm_Mapping_Property
     * @throws Xyster_Orm_Mapping_Exception if the property is not found
     */
    public function getRecursiveProperty($propertyPath)
    {
        // @todo
    }
    
    /**
     * Gets the properties which are referencable
     * 
     * @return Iterator
     */
    public function getReferencedPropertyIterator()
    {
        return $this->getPropertyClosureIterator();
    }
    
    /**
     * Gets the property given the specified path
     * 
     * @param string $propertyPath
     * @return Xyster_Orm_Mapping_Property
     * @throws Xyster_Orm_Mapping_Exception if the property is not found
     */
    public function getReferencedProperty( $propertyPath )
    {
        // @todo
    }
    
    /**
     * Gets the top-level class
     * 
     * @return Xyster_Orm_Mapping_Class
     */
    abstract public function getRootClass();
    
    /**
     * Gets the top-level table
     * 
     * @return Xyster_Db_Table
     */
    abstract public function getRootTable();
    
    /**
     * Gets the subclass closure
     * 
     * @return Iterator
     */
    public function getSubclassClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append(new ArrayIterator(array($this)));
        foreach( $this->getSubclassIterator() as $subclass ) {
            /* @var $subclass Xyster_Orm_Mapping_Subclass */
            $iters->append($subclass->getSubclassClosureIterator());
        }
        return $iters;
    }
    
    /**
     * Gets the subclass id
     *  
     * @return integer
     */
    abstract public function getSubclassId();
    
    /**
     * Gets the subclasses
     * 
     * @return Iterator of {@link Xyster_Orm_Mapping_Class_Abstract} objects
     */
    public function getSubclassIterator()
    {
        $iters = new AppendIterator();
        foreach( $this->_subclasses as $subclass ) {
            /* @var $subclass Xyster_Orm_Mapping_Subclass */
            $iters->append($subclass->getSubclassIterator());
        }
        $iters->append(new ArrayIterator($this->_subclasses));
        return $iters;
    }
    
    /**
     * Gets the subclass joins
     * 
     * @return Iterator
     */
    public function getSubclassJoinClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append($this->getJoinClosureIterator(),
            new ArrayIterator($this->_subclassJoins));
        return $iters;
    }

    /**
     * Gets the subclass properties
     *
     * @return Iterator
     */
    public function getSubclassPropertyClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append($this->getPropertyClosureIterator());
        $iters->append(new ArrayIterator($this->_subclassProperties));
        foreach( $this->_subclassJoins as $join ) {
            /* @var $join Xyster_Orm_Mapping_Join */
            $iters->append($join->getPropertyIterator());
        }
        return $iters;
    }
    
    /**
     * Gets the number of subclasses
     * 
     * @return integer
     */
    public function getSubclassSpan()
    {
        $span = count($this->_subclasses);
        foreach( $this->_subclasses as $sub ) {
            $span += $sub->getSubclassSpan();
        }
        return $span;
    }
    
    /**
     * Gets the subclass table closure
     * 
     * @return array of {@link Xyster_Db_Table} objects
     */
    public function getSubclassTableClosureIterator()
    {
        $iters = new AppendIterator();
        $iters->append($this->getTableClosureIterator(),
            new ArrayIterator($this->_subclassTables));
    }
    
    /**
     * Gets the parent class in the hierarchy
     * 
     * @return Xyster_Orm_Mapping_Class_Abstract
     */
    abstract public function getParentclass();
    
    /**
     * Gets the table that corresponds to this type
     *
     * @return Xyster_Db_Table
     */
    abstract public function getTable();
    
    /**
     * Gets the table closure
     * 
     * @return Iterator of {@link Xyster_Db_Table} objects
     */
    abstract public function getTableClosureIterator();
    
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
     * Gets an iterator for properties defined on this class not part of a join
     * 
     * @return Iterator
     */
    public function getUnjoinedPropertyIterator()
    {
        return new ArrayIterator($this->_properties);
    }
    
    /**
     * Gets the version property or null if none
     *
     * @return Xyster_Orm_Mapping_Property
     */
    abstract public function getVersion();

    /**
     * Gets the 'WHERE' SQL filter
     * 
     * @return string
     */
    abstract public function getWhere();
    
    /**
     * Whether this entity has an identifier component
     * 
     * @return boolean
     */
    public function hasIdComponent()
    {
        $this->_idComponent != null;
    }
    
    /**
     * Whether this type has an identifier property
     * 
     * @return boolean
     */
    abstract public function hasIdProperty();
    
    /**
     * Whether this class has a natural identifier
     * 
     * @return boolean
     */
    public function hasNaturalId()
    {
        $props = $this->getRootClass()->getPropertyIterator();
        foreach( $props as $prop ) {
            if ( $prop->isNaturalId() ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Whether this entity has subclasses
     * 
     * @return boolean
     */
    public function hasSubclasses()
    {
        return count($this->_subclasses) > 0;
    }
    
    /**
     * Gets whether the join specified is part of the entity or its parents
     * 
     * @param Xyster_Orm_Mapping_Join $join
     * @return boolean
     */
    public function isClassJoin(Xyster_Orm_Mapping_Join $join)
    {
        return in_array($join, $this->_joins, true);
    }

    /**
     * Gets whether the table specified is part of the entity or its parents
     * 
     * @param Xyster_Db_Table $table
     * @return boolean
     */
    public function isClassTable(Xyster_Db_Table $table)
    {
        return $this->getTable() === $table;
    }
    
    /**
     * Whether the discriminator column is insertable
     * 
     * @return boolean
     */
    abstract public function isDiscriminatorInsertable();
    
    /**
     * Whether the discriminator value allows null
     * 
     * @return boolean
     */
    public function isDiscriminatorValueNull()
    {
        return $this->getDiscriminatorValue() == 'null';
    }
    
    /**
     * Whether this type is inherited
     * 
     * @return boolean
     */
    abstract public function isInherited();
    
    /**
     * Whether this entity is a joined subclass
     *  
     * @return boolean
     */
    abstract public function isJoinedSubclass();
    
    /**
     * Gets whether this type has lazy loaded parts
     * 
     * @return boolean
     */
    public function isLazy()
    {
        return $this->_lazy;
    }
    
    /**
     * Whether the lazy properties are cacheable
     * 
     * @return boolean
     */
    abstract public function isLazyPropertiesCacheable();
    
    /**
     * Whether the mapped type is mutable
     *
     * @return boolean
     */
    abstract public function isMutable();
 
    /**
     * Whether this type should be selected before it's updated
     *
     * @return boolean
     */
    public function isSelectBeforeUpdate()
    {
        return $this->_selectBeforeUpdate;
    }
    
    /**
     * Gets whether the entity has a version property
     *
     * @return boolean
     */
    abstract public function isVersioned();
    
    /**
     * Sets the class name of the entity supported
     *
     * @param string $name The entity class name
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setClassName( $name )
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Sets the discriminator value
     * 
     * @param string $value
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setDiscriminatorValue( $value )
    {
        $this->_discriminatorValue = $value;
        return $this;
    }
    
    /**
     * Sets the identifier component
     * 
     * @param Xyster_Orm_Mapping_Component $handle
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setIdComponent( Xyster_Orm_Mapping_Component $handle )
    {
        $this->_idComponent = $handle;
        return $this;
    }
    
    /**
     * Sets whether this type has lazy loaded parts or not
     *
     * @param boolean $lazy
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setLazy( $lazy = true )
    {
        $this->_lazy = $lazy;
        return $this;
    }
    
    /**
     * Sets the loader for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setLoaderType( Xyster_Type $type )
    {
        $this->_loaderType = $type;
        return $this;
    }

    /**
     * Sets the mode of optimistic locking 
     *
     * @param Xyster_Orm_Engine_Versioning $mode
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setOptimisticLockMode( Xyster_Orm_Engine_Versioning $mode )
    {
        $this->_optimisticLock = $mode;
        return $this;
    }
    
    /**
     * Sets the persister for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    abstract public function setPersisterType( Xyster_Type $type );
    
    /**
     * Sets the proxy interface type
     * 
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setProxyInterfaceType( Xyster_Type $type )
    {
        $this->_proxyInterface = $type;
        return $this;
    }
    
    /**
     * Sets that a select must be performed before an update occurs
     *
     * @param boolean $flag
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
     */
    public function setSelectBeforeUpdate( $flag = true )
    {
        $this->_selectBeforeUpdate = $flag;
        return $this;
    }
    
    /**
     * Sets the type of tuplizer for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Class_Abstract provides a fluent interface
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
        $this->_subclassJoins[] = $j;
    }
    
    /**
     * Adds a subclass property
     * @param Xyster_Orm_Mapping_Property $p
     */
    protected function _addSubclassProperty(Xyster_Orm_Mapping_Property $p)
    {
        $this->_subclassProperties[] = $p;
    }

    /**
     * Adds a subclass table
     * 
     * @param Xyster_Db_Table $table
     */
    protected function _addSubclassTable(Xyster_Db_Table $table)
    {
        $this->_subclassTables[] = $table;
    }
    
    /**
     * Gets the discriminator columns
     * 
     * @return Iterator
     */
    protected function _getDiscriminatorColumnIterator()
    {
        return new EmptyIterator();
    }
    
    /**
     * Gets the unique properties
     * 
     * @return Iterator
     */
    protected function _getNonDuplicatedPropertyIterator()
    {
        return $this->getUnjoinedPropertyIterator();
    }
    
    private function _checkPropertyDuplication()
    {
        $names = array();
        foreach( $this->getPropertyIterator() as $prop ) {
            if ( in_array($prop->getName(), $names) ) {
                require_once 'Xyster/Orm/Mapping/Exception.php';
                throw new Xyster_Orm_Mapping_Exception('Duplicate property mapping: '
                    . $this->_name . '.' . $prop->getName());
            }
            $names[] = $prop->getName();
        }
    }
    
    private function _getRecursiveProperty($propertyPath, Iterator $iter)
    {
        // @todo
    }
    
    private function _getProperty($propertyName, Iterator $iter)
    {
        // @todo
    }
}