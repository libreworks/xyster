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
 * Persisters are aware of mapping and persistence information for an entity
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_Entity_Interface
{
    /**
     * Delete the persistent instance
     *
     * @param mixed $id
     * @param mixed $version
     * @param object $entity
     * @param Xyster_Orm_Session_Interface $sess
     */
    function delete( $id, $version, $entity, Xyster_Orm_Session_Interface $sess );
    
    /**
     * Get the current version of the object or null if not found
     *
     * @param mixed $id
     * @param Xyster_Orm_Session_Interface $sess
     * @return mixed
     */
    function getCurrentVersion( $id, Xyster_Orm_Session_Interface $sess );
    
    /**
     * Get the underlying entity metamodel
     *
     * @return Xyster_Orm_Runtime_EntityMeta
     */
    function getEntityMetamodel();
    
    /**
     * Get the entity name for this persister
     *
     * @return string
     */
    function getEntityName();
    
    /**
     * Get the session factory that created this persister
     *
     * @return Xyster_Orm_Session_Factory_Interface
     */
    function getFactory();
    
    /**
     * Gets the identifier of the instance
     *
     * @param object $object
     * @throws Xyster_Orm_Exception if there is no identifier property
     */
    function getId( $object );
    
    /**
     * Gets the identifier strategy
     *
     * @return Xyster_Orm_Engine_IdGenerator_Interface
     */
    function getIdGenerator();
    
    /**
     * Gets the name of the identifier property
     *
     * @return string
     */
    function getIdPropertyName();
    
    /**
     * Gets the type of the identifier property
     *
     * @return Xyster_Orm_Type_Interface
     */
    function getIdType();
    
    /**
     * Gets the mapped, persistent type 
     *
     * @return Xyster_Type
     */
    function getMappedType();
    
    /**
     * Gets the property laziness
     *
     * @return array
     */
    function getPropertyLaziness();
    
    /**
     * Get the property nullability
     *
     * @return array
     */
    function getPropertyNullability();
    
    /**
     * Gets the type of a particular property by name
     *
     * @param string $propertyName
     * @return Xyster_Orm_Type
     */
    function getPropertyType( $propertyName );
    
    /**
     * Get the types of the properties
     * 
     * @return array
     */
    function getPropertyTypes();
    
    /**
     * Gets the value of a particular property
     *
     * @param object $entity 
     * @param mixed $property The index or property name
     * @return mixed
     */
    function getPropertyValue($entity, $property);
    
    /**
     * Gets the values of the mapped properties
     *
     * @param object $entity
     * @return array
     */
    function getPropertyValues( $entity );
    
    /**
     * Get the versionability (is optimistic locked?) of the properties
     *
     * @return array
     */
    function getPropertyVersionability();
    
    /**
     * Gets the version number from the object (null if not versioned)
     *
     * @param object $entity
     * @return mixed
     */
    function getVersion( $entity );
    
    /**
     * Return the index of the version property if the entity is versioned
     *
     * @return int
     */
    function getVersionProperty();
    
    /**
     * Return the type of the version property if the entity is indexed
     *
     * @return Xyster_Orm_Type
     */
    function getVersionType();
    
    /**
     * Whether the entity contains collections
     *
     * @return boolean
     */
    function hasCollections();
    
    /**
     * Whether the entity has an identifier property
     *
     * @return boolean
     */
    function hasIdProperty();
    
    /**
     * Whether the entity has any database-generated values on insert
     *
     * @return boolean
     */
    function hasInsertGeneratedProperties();
    
    /**
     * Whether the entity has lazy-loaded properties
     *
     * @return boolean
     */
    function hasLazyProperties();
    
    /**
     * Whether the entity has mutable properties
     * 
     * @return boolean
     */
    function hasMutableProperties();
    
    /**
     * Whether the entity has any database-generated values on update
     *
     * @return boolean
     */
    function hasUpdateGeneratedProperties();
    
    /**
     * Inserts an instance (with or without an ID)
     * 
     * If the ID is left blank, it is assumed that the data store will generate
     * the identifier.
     *
     * @param Xyster_Orm_Session_Interface $sess
     * @param object $entity
     * @param array $fields
     * @param mixed $id
     */
    function insert(Xyster_Orm_Session_Interface $sess, $entity, array $fields, $id = null);
    
    /**
     * Create an instance with the given identifier
     *
     * @param mixed $id
     * @return object
     */
    function instantiate( $id );
    
    /**
     * Whether the id is generated by insert into the data store
     * 
     * @return boolean
     */
    function isIdByInsert();
    
    /**
     * Whether the supplied object is an instance of the entity type
     *
     * @param boolean $object
     */
    function isInstance( $object );
    
    /**
     * Whether instances of this entity are considered mutable
     *
     * @return boolean
     */
    function isMutable();
    
    /**
     * Whether the option for selecting a snapshot before update is enabled
     *
     * @return boolean
     */
    function isSelectBeforeUpdate();
    
    /**
     * Whether optimistic locking is enabled by column
     *
     * @return boolean
     */
    function isVersioned();
    
    /**
     * Loads the object
     *
     * @param mixed $id
     * @return object
     */
    function load( $id );
    
    /**
     * Sets the entity's identifier
     *
     * @param object $entity
     * @param mixed $id
     */
    function setId($entity, $id);
    
    /**
     * Sets a specific property
     *
     * @param object $object
     * @param int $i
     * @param mixed $value
     */
    function setPropertyValue( $object, $i, $value );
    
    /**
     * Sets the given values to the entity
     *
     * @param object $object
     * @param array $values
     */
    function setPropertyValues( $object, array $values );
    
    /**
     * Updates the instance
     *
     * @param mixed $id
     * @param array $fields
     * @param array $dirtyFields
     * @param boolean $hasDirtyCollection
     * @param array $oldFields
     * @param mixed $oldVersion
     * @param object $object
     * @param mixed $rowId
     * @param Xyster_Orm_Session_Interface $sess
     */
    function update( $id, array $fields, array $dirtyFields, $hasDirtyCollection, array $oldFields, $oldVersion, $object, $rowId, Xyster_Orm_Session_Interface $sess );
}