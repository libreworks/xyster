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
 * @see Xyster_Orm_Persister_Entity_Loadable_OuterJoin_Interface
 */
require_once 'Xyster/Orm/Persister/Entity/Loadable/OuterJoin/Interface.php';
/**
 * @see Xyster_Orm_Persister_Entity_Queryable_Interface
 */
require_once 'Xyster/Orm/Persister/Entity/Queryable/Interface.php';
/**
 * Basic behavior for persisting an entity
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Persister_Entity_Abstract implements Xyster_Orm_Persister_Entity_Loadable_OuterJoin_Interface, Xyster_Orm_Persister_Entity_Queryable_Interface
{
    /**
     * @var Xyster_Orm_Runtime_EntityMeta
     */
    protected $_entityMeta;
    
    /**
     * @var Xyster_Orm_Session_Factory_Interface
     */
    protected $_factory;
    
    protected $_idColumnNames = array();
    
    protected $_idColumnSpan;
    
    protected $_lazyProperties = array();
    protected $_lazyPropertyNames = array();
    protected $_lazyPropertyNumbers = array();
    protected $_lazyPropertyTypes = array();
    
    protected $_properties = array();
    protected $_propertyColumnNames = array();
    protected $_propertyColumnSpans = array();
        
    protected $_versionColumnName;
    
    /**
     * Creates a new Abstract entity persister
     * 
     * @param Xyster_Orm_Mapping_Class_Abstract $em
     * @param Xyster_Orm_Session_Factory_Interface $factory
     */
    public function __construct( Xyster_Orm_Mapping_Class_Abstract $em, Xyster_Orm_Session_Factory_Interface $factory )
    {
        $this->_factory = $factory;
        $this->_entityMeta = new Xyster_Orm_Runtime_EntityMeta($em, $factory);
        
        // id
        
        $this->_idColumnSpan = $em->getIdentifier()->getColumnSpan();
        foreach( $em->getIdentifier()->getColumns() as $col ) {
            $this->_idColumnNames[] = $col->getName();
        }
        
        // version
        
        if ( $em->isVersioned() ) {
            $columns = $em->getVersion()->getColumns();
            $this->_versionColumnName = $columns[0];
        }
        
        // properties
        
        foreach( array_values($em->getProperties()) as $i => $prop ) {
            /* @var $prop Xyster_Orm_Mapping_Property */
            $this->_properties[] = $prop;
            $span = $prop->getColumnSpan();
            $this->_propertyColumnSpans[$i] = $span;
            $colNames = array();
            foreach( $prop->getColumns() as $thing ) {
                /* @var $thing Xyster_Db_Column */
                $colNames[] = $thing->getName();
            }
            $this->_propertyColumnNames[$i] = $colNames;
            
            if ( $prop->isLazy() ) {
                $this->_lazyProperties[] = $prop;
                $this->_lazyPropertyNames[] = $prop->getName();
                $this->_lazyPropertyNumbers[] = $i;
                $this->_lazyPropertyTypes[] = $prop->getValue()->getType();
            }
        }
    }
    
    /**
     * Create a proxy for the entity 
     *
     * @param mixed $id
     * @param Xyster_Orm_Session_Interface $sess
     * @return object
     */
    public function createProxy( $id, Xyster_Orm_Session_Interface $sess )
    {
        
    }
    
    /**
     * Delete the persistent instance
     *
     * @param mixed $id
     * @param mixed $version
     * @param object $entity
     * @param Xyster_Orm_Session_Interface $sess
     */
    public function delete( $id, $version, $entity, Xyster_Orm_Session_Interface $sess )
    {
        
    }
    
    /**
     * Get the current version of the object or null if not found
     *
     * @param mixed $id
     * @param Xyster_Orm_Session_Interface $sess
     * @return mixed
     */
    public function getCurrentVersion( $id, Xyster_Orm_Session_Interface $sess )
    {
        
    }
    
    /**
     * Gets the entity metamodel
     * 
     *  @return Xyster_Orm_Runtime_EntityMeta
     */
    public function getEntityMetamodel()
    {
        return $this->_entityMeta;
    }
    
    /**
     * Gets the name of the entity
     *
     * @return string
     */
    public function getEntityName()
    {
        
    }
    
    /**
     * Gets the session factory
     * 
     * @return Xyster_Orm_Session_Factory_Interface
     */
    public function getFactory()
    {
        return $this->_factory;
    }
    
    /**
     * Gets the identifier of an instance
     *
     * @param object $entity
     * @throws Xyster_Orm_Exception if no identifier property
     * @return mixed
     */
    public function getId( $entity )
    {
        
    }
    
    /**
     * Get the names of columns used to persist the identifier
     * 
     * @return array
     */
    public function getIdColumnNames()
    {
        
    }
    
    /**
     * Gets the identifier strategy
     *
     * @return Xyster_Orm_Engine_IdGenerator_Interface
     */
    public function getIdGenerator()
    {
        
    }
    
    /**
     * Get the name of the identifier property (or null if none) 
     *
     * @return string
     */
    public function getIdPropertyName()
    {
        
    }
    
    /**
     * Gets the type of the identifier property
     *
     * @return Xyster_Orm_Type_Interface
     */
    public function getIdType()
    {
        
    }
    
    /**
     * Gets the type of the entity
     *
     * @return Xyster_Type
     */
    public function getMappedType()
    {
        
    }
    
    /**
     * Get the column names mapped for this property
     * 
     * Name can be an integer or a property name string.
     *  
     * @return array
     */
    public function getPropertyColumnNames( $name )
    {
        
    }

    /**
     * Get the property number of the given property name
     * 
     * @param string $name
     * @return integer
     */
    public function getPropertyIndex( $name )
    {
        
    }
    
    /**
     * Gets which properties are generated by the database on insert
     * 
     * @return array of {@link Xyster_Orm_Engine_ValueInclusion} objects
     */
    public function getPropertyInsertGenerationInclusions()
    {
        
    }
    
    /**
     * Gets the property laziness
     *
     * @return array
     */
    public function getPropertyLaziness()
    {
        
    }
    
    /**
     * Gets the property names
     *
     * @return array
     */
    public function getPropertyNames()
    {
        
    }
    
    /**
     * Gets the property nullability
     *
     * @return array
     */
    public function getPropertyNullability()
    {
        
    }
        
    /**
     * Gets the type of a particular property
     *
     * @param string $propertyName
     * @return Xyster_Orm_Type_Interface
     */
    public function getPropertyType( $propertyName )
    {
        
    }
    
    /**
     * Get the types of all properties
     *
     * @return array
     */
    public function getPropertyTypes()
    {
        
    }
    
    /**
     * Gets which properties are database generated values on update
     * 
     * @return array of {@link Xyster_Orm_Engine_ValueInclusion} objects
     */
    public function getPropertyUpdateGenerationInclusions()
    {
        
    }
    
    /**
     * Gets the value of a particular property
     *
     * @param object $entity 
     * @param mixed $property The index or property name
     * @return mixed
     */
    public function getPropertyValue($entity, $property)
    {
        
    }
    
    /**
     * Gets the values of the mapped properties
     *
     * @param object $entity
     * @return array
     */
    public function getPropertyValues( $entity )
    {
        
    }
    
    /**
     * Get the versionability (is optimistic locked?) of the properties
     *
     * @return array
     */
    public function getPropertyVersionability()
    {
        
    }
        
    /**
     * Gets the tuplizer for this entity type
     * 
     * @return Xyster_Orm_Tuplizer_Entity_Interface
     */
    public function getTuplizer()
    {
        return $this->_entityMeta->getTuplizer();
    }
    
    /**
     * Gets the version number from the object (null if not versioned)
     *
     * @param object $entity
     * @return mixed
     */
    public function getVersion( $entity )
    {
        
    }
    
    /**
     * Gets the name of the version column
     * 
     * @return string
     */
    public function getVersionColumnName()
    {
        
    }
    
    /**
     * Get the comparator for version values
     * 
     * @return Xyster_Collection_Comparator_Interface
     */
    public function getVersionComparator()
    {
        
    }
    
    /**
     * Return the index of the version property if the entity is versioned
     *
     * @return int
     */
    public function getVersionProperty()
    {
        
    }
    
    /**
     * Return the type of the version property if the entity is indexed
     *
     * @return Xyster_Orm_Type_Version
     */
    public function getVersionType()
    {
        
    }
    
    /**
     * Whether the entity contains collections
     *
     * @return boolean
     */
    public function hasCollections()
    {
        
    }
    
    /**
     * Whether the class has an identifier property
     *
     * @return boolean
     */
    public function hasIdProperty()
    {
        
    }
    
    /**
     * Whether the entity has any database-generated values on insert
     *
     * @return boolean
     */
    public function hasInsertGeneratedProperties()
    {
        
    }
    
    /**
     * Whether the entity has lazy-loaded properties
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
     * Whether the entity has any database-generated values on update
     *
     * @return boolean
     */
    public function hasUpdateGeneratedProperties()
    {
        
    }
    
    /**
     * Whether this class supports dynamic proxies
     *
     * @return boolean
     */
    public function hasProxy()
    {
        
    }
    
    /**
     * Create an instance with the given identifier
     *
     * @param mixed $id
     * @return object
     */
    public function instantiate( $id )
    {
        
    }
    
    /**
     * Whether the supplied object is an instance of the entity type
     *
     * @param mixed $object
     * @return boolean
     */
    public function isInstance( $object )
    {
        
    }
    
    /**
     * Sets a specific property
     *
     * @param object $object
     * @param int $i
     * @param mixed $value
     */
    public function setPropertyValue( $object, $i, $value )
    {
        
    }
}