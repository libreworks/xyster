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
 * Runtime metamodel entity information
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Runtime_EntityMeta
{
    /**
     * @var string
     */
    private $_name;
    /**
     * @var boolean
     */
    private $_hasInsertGeneratedValues;
    /**
     * @var boolean
     */
    private $_hasUpdateGeneratedValues = false;
    /**
     * @var boolean
     */
    private $_hasCollections = false;
    /**
     * @var boolean
     */
    private $_hasMutableProperties = false;
    /**
     * @var boolean
     */
    private $_hasLazyProperties = false;
    /**
     * @var boolean
     */
    private $_hasNonIdentifierPropertyNamedId = false;
    /**
     * @var Xyster_Orm_Runtime_Property_Identifier
     */
    private $_identifierProperty;
    /**
     * @var array
     */
    private $_insertInclusions = array();
    /**
     * @var boolean
     */
    private $_lazy = false;
    /**
     * @var boolean
     */
    private $_mutable = false;
    /**
     * @var Xyster_Orm_Engine_Versioning
     */
    private $_optimisticLockMode;
    /**
     * @var array
     */
    private $_properties = array();
    /**
     * @var array
     */
    private $_propertyIndexes = array();
    /**
     * @var array
     */
    private $_propertyLaziness = array();
    /**
     * @var array
     */
    private $_propertyNames = array();
    /**
     * @var array
     */
    private $_propertyNullability = array();
    /**
     * @var int
     */
    private $_propertySpan = 0;
    /**
     * @var array
     */
    private $_propertyTypes = array();
    /**
     * @var array
     */
    private $_propertyVersionability = array();
    /**
     * @var boolean
     */
    private $_selectBeforeUpdate = false;
    /**
     * @var Xyster_Orm_Session_Factory_Interface
     */
    private $_sessionFactory;
    /**
     * @var array 
     */
    private $_updateInclusions = array();
    /**
     * @var boolean
     */
    private $_versioned = false;
    /**
     * @var int
     */
    private $_versionPropertyIndex = -1;
    
    /**
     * Creates a new runtime entity meta object
     *
     * @param Xyster_Orm_Mapping_Entity $entityMapping
     * @param Xyster_Orm_Session_Factory_Interface $sessionFactory
     */
    public function __construct( Xyster_Orm_Mapping_Entity $entityMapping, Xyster_Orm_Session_Factory_Interface $sessionFactory )
    {
        
    }
    
    /**
     * Gets the identifier property
     *
     * @return Xyster_Orm_Runtime_Property_Identifier
     */
    public function getIdentifier()
    {
        
    }
    
    /**
     * Gets the class name of the entity
     *
     * @return string
     */
    public function getName()
    {
        
    }
    
    /**
     * Gets the optimistic lock mode
     *
     * @return Xyster_Orm_Engine_Versioning
     */
    public function getOptimisticLockMode()
    {
        
    }
    
    /**
     * Gets the properties on this entity type
     * 
     * This array does not include the identifier.
     *
     * @return array of {@link Xyster_Orm_Runtime_Property_Standard} objects
     */
    public function getProperties()
    {
        
    }
    
    /**
     * Gets the numeric index of the property name given
     *
     * @param string $name
     * @param boolean $nullIfNone Return null if property not found?
     * @return int
     * @throws Xyster_Orm_Exception if the property wasn't found
     */
    public function getPropertyIndex( $name, $nullIfNone = false )
    {
        
    }
    
    /**
     * Gets an array of booleans representing whether properties are lazy
     *
     * @return array
     */
    public function getPropertyLaziness()
    {
        
    }
    
    /**
     * Gets an array of property names
     *
     * @return array
     */
    public function getPropertyNames()
    {
        
    }
    
    /**
     * Gets an array of booleans representing whether properties are nullable
     *
     * @return array
     */
    public function getPropertyNullability()
    {
        
    }
    
    /**
     * Gets the number of properties in the type
     *
     * @return int
     */
    public function getPropertySpan()
    {
        
    }
    
    /**
     * Gets an array of {@link Xyster_Orm_Type_Interface} objects
     *
     * @return array
     */
    public function getPropertyTypes()
    {
        
    }
    
    /**
     * Gets an array of booleans representing whether properties are versioned
     *
     * @return array
     */
    public function getPropertyVersionability()
    {
        
    }
    
    /**
     * Gets the session factory
     *
     * @return Xyster_Orm_Session_Factory_Interface
     */
    public function getSessionFactory()
    {
        
    }
    
    /**
     * Gets the tuplizer
     *
     * @return Xyster_Orm_Tuplizer_Entity_Interface
     */
    public function getTuplizer()
    {
        
    }
    
    /**
     * Gets the version property
     *
     * @return Xyster_Orm_Runtime_Property_Version
     */
    public function getVersion()
    {
        
    }
    
    /**
     * Gets the numeric index of the version property
     *
     * @return int
     */
    public function getVersionIndex()
    {
        
    }
    
    /**
     * Whether the entity has collections
     *
     * @return boolean
     */
    public function hasCollections()
    {
        
    }
    
    /**
     * Whether the entity has values generated by the database on insert
     *
     * @return boolean
     */
    public function hasInsertGeneratedValues()
    {
        
    }
    
    /**
     * Whether the entity has lazy properties
     *
     * @return boolean
     */
    public function hasLazyProperties()
    {
        
    }
    
    /**
     * Whether the entity has mutable properties
     *
     * @return boolean
     */
    public function hasMutableProperties()
    {
        
    }
    
    /**
     * Whether the entity has a property named id that isn't the identifier
     *
     * @return boolean
     */
    public function hasNonIdentifierPropertyNamedId()
    {
        
    }
    
    /**
     * Whether the entity has values generated by the database on update
     *
     * @return boolean
     */
    public function hasUpdateGeneratedValues()
    {
        
    }
    
    /**
     * Whether this type is lazy loaded
     *
     * @return boolean
     */
    public function isLazy()
    {
        
    }
    
    /**
     * Whether this entity type is mutable
     *
     * @return boolean
     */
    public function isMutable()
    {
        
    }
    
    /**
     * Whether this type should be selected before it's updated
     *
     * @return boolean
     */
    public function isSelectBeforeUpdate()
    {
        
    }
    
    /**
     * Whether this entity type has a version property
     * 
     * @return boolean
     */
    public function isVersioned()
    {
        
    }
    
    /**
     * Sets this entity to be lazy-loaded
     *
     * @param boolean $lazy
     * @return Xyster_Orm_Runtime_EntityMeta provides a fluent interface
     */
    public function setLazy( $lazy = true )
    {
        
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        
    }
}