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
     * @var Xyster_Type
     */
    protected $_persisterType;
    
    /**
     * @var array
     */
    protected $_properties;
    
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
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
        
    }
    
    /**
     * Gets the type of entity this class represents
     * 
     * @return string The class name
     */
    public function getClassName()
    {
        
    }
    
    /**
     * Gets the identifier property for this entity
     *
     * @return Xyster_Orm_Mapping_Property
     */
    public function getIdentifier()
    {
        
    }
    
    /**
     * Gets the type of loader for this entity type
     *
     * @return Xyster_Type
     */
    public function getLoaderType()
    {
        
    }
    
    /**
     * Gets the type of entity this class represents
     *
     * @return Xyster_Type The type
     */
    public function getMappedType()
    {
        
    }
    
    /**
     * Gets the type of persister for this entity type
     *
     * @return Xyster_Type
     */
    public function getPersisterType()
    {
        
    }
    
    /**
     * Gets the properties in the class
     *
     * @return array of {@link Xyster_Orm_Mapping_Property} objects
     */
    public function getProperties()
    {
        
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
        
    }
    
    /**
     * Gets the table that corresponds to this type
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        
    }
    
    /**
     * Gets the version property or null if none
     *
     * @return Xyster_Orm_Mapping_Property
     */
    public function getVersion()
    {
        
    }
    
    /**
     * Whether this type has an identifier property
     * 
     * @return boolean
     */
    public function hasIdentifierProperty()
    {
        
    }
    
    /**
     * Gets whether this type has lazy loaded parts
     * 
     * @return boolean
     */
    public function isLazy()
    {
        
    }
    
    /**
     * Whether the mapped type is mutable
     *
     */
    public function isMutable()
    {
        
    }
    
    /**
     * Gets whether the entity has a version property
     *
     * @return boolean
     */
    public function isVersioned()
    {
        
    }
    
    /**
     * Sets the identifier property
     *
     * @param Xyster_Orm_Mapping_Property $prop
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setIdentifier( Xyster_Orm_Mapping_Property $prop )
    {
        
    }
    
    /**
     * Sets whether this type has lazy loaded parts or not
     *
     * @param boolean $lazy
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setLazy( $lazy = true )
    {
        
    }
    
    /**
     * Sets the loader for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setLoaderType( Xyster_Type $type )
    {
        
    }
    
    /**
     * Sets whether this type is mutable or not
     *
     * @param boolean $mutable
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setMutable( $mutable = true )
    {
        
    }
    
    /**
     * Sets the persister for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setPersisterType( Xyster_Type $type )
    {
        
    }
    
    /**
     * Sets the table for this type
     *
     * @param Xyster_Db_Table $table The table
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setTable( Xyster_Db_Table $table )
    {
        
    }
    
    /**
     * Sets the version property for this type
     *
     * @param Xyster_Orm_Mapping_Property $prop The property
     * @return Xyster_Orm_Mapping_Entity provides a fluent interface
     */
    public function setVersion( Xyster_Orm_Mapping_Property $prop )
    {
        
    }
}