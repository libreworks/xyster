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
class Xyster_Orm_Mapping_Entity
{
    /**
     * @var Xyster_Orm_Mapping_Property
     */
    protected $_identifier;
    
    /**
     * @var boolean
     */
    protected $_lazy = false;
    
    /**
     * @var Xyster_Type
     */
    protected $_loaderType;
    
    /**
     * @var boolean
     */
    protected $_mutable = true;
    
    /**
     * @var string
     */
    protected $_name;

    /**
     * @var Xyster_Orm_Engine_Versioning
     */
    protected $_optimisticLock;
    
    /**
     * @var Xyster_Type
     */
    protected $_persisterType;
    
    /**
     * @var array
     */
    protected $_properties = array();
    
    /**
     * @var boolean
     */
    protected $_selectBeforeUpdate = false;
    
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
    /**
     * @var Xyster_Type
     */
    protected $_tuplizerType;
    
    /**
     * @var Xyster_Orm_Mapping_Property
     */
    protected $_version;
    
    /**
     * Adds a property to the class
     *
     * @param Xyster_Orm_Mapping_Property $prop
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function addProperty( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_properties[$prop->getName()] = $prop;
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
     * Gets the identifier property for this entity
     *
     * @return Xyster_Orm_Mapping_Property
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
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
    public function getOptimisticLockMode()
    {
        return $this->_optimisticLock;
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
     * Gets the properties in the class
     *
     * @return array of {@link Xyster_Orm_Mapping_Property} objects
     */
    public function getProperties()
    {
        return array() + $this->_properties;
    }
    
    /**
     * Gets a property by name
     *
     * @param string $name
     * @throws Xyster_Orm_Exception if the property isn't found
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
     * Gets the table that corresponds to this type
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
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
     * Whether this type has an identifier property
     * 
     * @return boolean
     */
    public function hasIdentifier()
    {
        return $this->_identifier !== null;
    }
    
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
     * Whether the mapped type is mutable
     *
     * @return boolean
     */
    public function isMutable()
    {
        return $this->_mutable;
    }
 
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
    public function isVersioned()
    {
        return $this->_version !== null;
    }
    
    /**
     * Sets the class name of the entity supported
     *
     * @param string $name The entity class name
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setClassName( $name )
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Sets the identifier property
     *
     * @param Xyster_Orm_Mapping_Property $prop
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setIdentifier( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_identifier = $prop;
        return $this;
    }
    
    /**
     * Sets whether this type has lazy loaded parts or not
     *
     * @param boolean $lazy
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
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
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setLoaderType( Xyster_Type $type )
    {
        $this->_loaderType = $type;
        return $this;
    }
    
    /**
     * Sets whether this type is mutable or not
     *
     * @param boolean $mutable
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setMutable( $mutable = true )
    {
        $this->_mutable = $mutable;
        return $this;
    }
    
    /**
     * Sets the mode of optimistic locking 
     *
     * @param Xyster_Orm_Engine_Versioning $mode
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
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
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setPersisterType( Xyster_Type $type )
    {
        $this->_persisterType = $type;
        return $this;
    }
    
    /**
     * Sets that a select must be performed before an update occurs
     *
     * @param boolean $flag
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setSelectBeforeUpdate( $flag = true )
    {
        $this->_selectBeforeUpdate = $flag;
        return $this;
    }
    
    /**
     * Sets the table for this type
     *
     * @param Xyster_Db_Table $table The table
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setTable( Xyster_Db_Table $table )
    {
        $this->_table = $table;
        return $this;
    }
    
    /**
     * Sets the type of tuplizer for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setTuplizerType( Xyster_Type $type )
    {
        $this->_tuplizerType = $type;
        return $this;
    }
    
    /**
     * Sets the version property for this type
     *
     * @param Xyster_Orm_Mapping_Property $prop The property
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setVersion( Xyster_Orm_Mapping_Property $prop )
    {
        $this->_version = $prop;
        return $this;
    }
}