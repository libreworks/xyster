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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Metadata for an entity's domain mappings.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Meta_Entity
{
    const OPTION_LAZY = "lazy";
    const OPTION_MUTABLE = "mutable";
    const OPTION_PERSISTER = "persisterType";
    const OPTION_PROXY = "proxyInterface";
    const OPTION_TUPLIZER = "tuplizerType";
    const OPTION_WHERE = "where";
    
    /**
     * @var boolean
     */
    protected $_hasNaturalId = false;
    
    /**
     * @var Xyster_Orm_Meta_IdProperty
     */
    protected $_identifier;
        
    /**
     * @var boolean
     */
    protected $_lazy = false;

    /**
     * @var boolean
     */
    protected $_mutable = true;
    
    /**
     * @var Xyster_Type
     */
    protected $_persisterType;
    
    /**
     * @var Xyster_Orm_Meta_Property
     */
    protected $_properties = array();

    /**
     * @var Xyster_Type
     */
    protected $_proxyInterface;

    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
        
    /**
     * @var Xyster_Type
     */
    protected $_tuplizerType;

    /**
     * @var Xyster_Type
     */
    protected $_type;
    
    /**
     * @var Xyster_Orm_Meta_Property
     */
    protected $_version;

    /**
     * @var string
     */
    protected $_where;

    /**
     * Creates a new Entity metadata container.
     * 
     * The options argument is a name-value hash that should contain the class
     * constants as keys and the appropriate values.
     * 
     * @param Xyster_Type $type The entity class 
     * @param array $properties Array containing {@link Xyster_Orm_Meta_Property} objects
     * @param Xyster_Db_Table $table The table this entity represents 
     * @param Xyster_Orm_Meta_Property $id Optional. The identifier property.
     * @param Xyster_Orm_Meta_Property $version Optional. The version property.
     * @param array $options An array of name-value pairs containing options.
     */
    public function __construct(Xyster_Type $type, array $properties, Xyster_Db_Table $table, Xyster_Orm_Meta_IdProperty $id = null, Xyster_Orm_Meta_Property $version = null, array $options = array())
    {
        $this->_type = $type;
        $this->_table = $table;
        foreach($properties as $prop) {
            if ( !$prop instanceof Xyster_Orm_Meta_Property ) {
                throw new Xyster_Orm_Meta_Exception("Invalid property supplied");
            }
            $this->_properties[$prop->getName()] = $prop;
            if ( $prop->isNaturalId() ) {
                $this->_hasNaturalId = true;
            }
        }
        $this->_identifier = $id;
        $this->_version = $version;
        
        // get options
        if ( isset($options[self::OPTION_LAZY]) && is_bool($options[self::OPTION_LAZY]) ) {
            $this->_lazy = $options[self::OPTION_LAZY];
        }
        if ( isset($options[self::OPTION_MUTABLE]) && is_bool($options[self::OPTION_MUTABLE]) ) {
            $this->_mutable = $options[self::OPTION_MUTABLE];
        }
        if ( isset($options[self::OPTION_WHERE]) && is_string($options[self::OPTION_WHERE]) ) {
            $this->_where = $options[self::OPTION_WHERE];
        }
        if ( isset($options[self::OPTION_PERSISTER]) && $options[self::OPTION_PERSISTER] instanceof Xyster_Type ) {
            $this->_persisterType = $options[self::OPTION_PERSISTER];
        }
        if ( isset($options[self::OPTION_PROXY]) && $options[self::OPTION_PROXY] instanceof Xyster_Type ) {
            $this->_proxyInterface = $options[self::OPTION_PROXY];
        }
        if ( isset($options[self::OPTION_TUPLIZER]) && $options[self::OPTION_TUPLIZER] instanceof Xyster_Type ) {
            $this->_tuplizerType = $options[self::OPTION_TUPLIZER];
        }        
    }
    
    /**
     * Gets the type of entity this class represents
     * 
     * @return string The class name
     */
    public function getClassName()
    {
        return $this->_type->getName();
    }

    /**
     * Gets the identifier property for this entity
     *
     * @return Xyster_Orm_Meta_IdProperty
     */
    public function getIdProperty()
    {
        return $this->_identifier;
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
     * @return Traversable containing {@link Xyster_Orm_Mapping_Property} objects
     */
    public function getProperties()
    {
        return new ArrayIterator($this->_properties);
    }
    
    /**
     * Gets a property by name
     *
     * @param string $name
     * @throws Xyster_Orm_Mapping_Exception if the property isn't found
     * @return Xyster_Orm_Meta_Property
     */
    public function getProperty( $name )
    {
        if ( !array_key_exists($name, $this->_properties) ) {
            require_once 'Xyster/Orm/Meta/Exception.php';
            throw new Xyster_Orm_Meta_Exception('Property not found: ' . $name);
        }
        return $this->_properties[$name];
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
     * Gets the table
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
     * Gets the entity type
     * 
     * @return Xyster_Type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Gets the version property or null if none
     *
     * @return Xyster_Orm_Meta_Property
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
     * Whether this class has a natural identifier
     * 
     * @return boolean
     */
    public function hasNaturalId()
    {
        return $this->_hasNaturalId;
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
     * Gets whether the entity has a version property
     *
     * @return boolean
     */
    public function isVersioned()
    {
        return $this->_version !== null;
    }
}