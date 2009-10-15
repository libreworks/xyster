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
 * @see Xyster_Orm_Meta_Entity
 */
require_once 'Xyster/Orm/Meta/Entity.php';
/**
 * Responsible for creating the read-only metadata containing domain mappings.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Meta_EntityBuilder
{
    /**
     * @var Xyster_Orm_Meta_Property
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
     * Creates a new entityBuilder
     * 
     * @param Xyster_Type $type the entity class type
     */
    public function __construct(Xyster_Type $type, Xyster_Db_Table $table)
    {
        $this->_type = $type;
        $this->_table = $table;
    }
    
    /**
     * Adds a property to the class
     *
     * @param Xyster_Orm_Meta_Property $prop
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function addProperty( Xyster_Orm_Meta_Property $prop )
    {
        $this->_properties[] = $prop;
        return $this;
    }

    /**
     * Builds an entity metadata definition from the settings applied.
     * 
     * @return Xyster_Orm_Meta_Entity The entity created
     */
    public function build()
    {
        return new Xyster_Orm_Meta_Entity($this->_type, $this->_properties,
            $this->_table, $this->_identifier, $this->_version,
            array(
                Xyster_Orm_Meta_Entity::OPTION_LAZY => $this->_lazy,
                Xyster_Orm_Meta_Entity::OPTION_MUTABLE => $this->_mutable,
                Xyster_Orm_Meta_Entity::OPTION_PERSISTER => $this->_persisterType,
                Xyster_Orm_Meta_Entity::OPTION_PROXY => $this->_proxyInterface,
                Xyster_Orm_Meta_Entity::OPTION_TUPLIZER => $this->_tuplizerType,
                Xyster_Orm_Meta_Entity::OPTION_WHERE => $this->_where
            ));
    }

    /**
     * Sets the identifier property
     *
     * @param Xyster_Orm_Meta_IdProperty $prop
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setIdProperty( Xyster_Orm_Meta_IdProperty $prop )
    {
        $this->_identifier = $prop;
        return $this;
    }

    /**
     * Sets whether this type has lazy loaded parts or not
     *
     * @param boolean $lazy
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setLazy( $lazy = true )
    {
        $this->_lazy = $lazy;
        return $this;
    }
    
    /**
     * Sets whether this type is mutable or not
     *
     * @param boolean $mutable
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
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
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setPersisterType( Xyster_Type $type )
    {
        $this->_persisterType = $type;
        return $this;
    }

    /**
     * Sets the proxy interface type
     * 
     * @param Xyster_Type $type
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setProxyInterfaceType( Xyster_Type $type )
    {
        $this->_proxyInterface = $type;
        return $this;
    }

    /**
     * Sets the type of tuplizer for this entity type
     *
     * @param Xyster_Type $type
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setTuplizerType( Xyster_Type $type )
    {
        $this->_tuplizerType = $type;
        return $this;
    }
    
    /**
     * Sets the version property for this type
     *
     * @param Xyster_Orm_Meta_Property $prop The property
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setVersion( Xyster_Orm_Meta_Property $prop )
    {
        $this->_version = $prop;
        return $this;
    }

    /**
     * Sets the 'WHERE' SQL filter
     * 
     * @param string $where
     * @return Xyster_Orm_Meta_EntityBuilder provides a fluent interface
     */
    public function setWhere( $where )
    {
        $this->_where = $where;
        return $this;
    }
}