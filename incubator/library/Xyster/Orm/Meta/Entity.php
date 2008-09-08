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
 * Userland-accessible runtime metadata
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Meta_Entity
{
    /**
     * Gets the name of the entity
     *
     * @return string
     */
    function getEntityName();
    
    /**
     * Gets the identifier of an instance
     *
     * @param object $entity
     * @throws Xyster_Orm_Exception if no identifier property
     * @return mixed
     */
    function getId( $entity );
    
    /**
     * Get the name of the identifier property (or null if none) 
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
     * Gets the type of the entity
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
     * Gets the property names
     *
     * @return array
     */
    function getPropertyNames();
    
    /**
     * Gets the property nullability
     *
     * @return array
     */
    function getPropertyNullability();
    
    /**
     * Gets the type of a particular property
     *
     * @param string $propertyName
     * @return Xyster_Orm_Type_Interface
     */
    function getPropertyType( $propertyName );
    
    /**
     * Get the types of all properties
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
    
    // @todo figure out how getPropertyValuesToInsert is used
    // function getPropertyValuesToInsert( $entity, Xyster_Collection_Map_Interface $map, Xyster_Orm_Session_Interface $sess );
    
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
     * Whether the class has an identifier property
     *
     * @return boolean
     */
    function hasIdProperty();
    
    /**
     * Whether this class supports dynamic proxies
     *
     * @return boolean
     */
    function hasProxy();
    
    /**
     * Create an instance with the given identifier
     *
     * @param mixed $id
     * @return object
     */
    function instantiate( $id );
    
    /**
     * Whether instances of this entity are considered mutable
     *
     * @return boolean
     */
    function isMutable();
    
    /**
     * Whether optimistic locking is enabled by column
     *
     * @return boolean
     */
    function isVersioned();
    
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
}