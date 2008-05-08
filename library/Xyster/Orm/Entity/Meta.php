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
 * @see Xyster_Orm_Entity_Field
 */
require_once 'Xyster/Orm/Entity/Field.php';
/**
 * @see Xyster_Orm_Xsql
 */
require_once 'Xyster/Orm/Xsql.php';
/**
 * A helper for meta information about entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity_Type
{
	/**
	 * @var string
	 */
    protected $_class;
    
    /**
     * Cache for entity fields
     *
     * @var array
     */
    protected $_fields = array();
    
    /**
     * The mapper factory
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mapFactory;
    
    /**
     * A cache for all entity members
     *
     * @var array
     */
    protected $_members = array();
    
    /**
     * The field names of the primary key
     *
     * @var array
     */
    protected $_primary = array();
    
    /**
     * The relation objects
     *
     * @var array
     */
    protected $_relations = array();
    
    /**
     * The cache for runtime column lookups
     *
     * @var array
     */
    protected $_runtime = array();

    /**
     * Creates a new Entity Metadata helper
     *
     * @param Xyster_Orm_Mapper $map
     */
    public function __construct( Xyster_Orm_Mapper_Interface $map )
    {
        $this->_class = $map->getEntityName();
        $this->_mapFactory = $map->getFactory();

        foreach( $map->getFields() as $name => $values ) {
            $translated = $map->translateField($name);
            $field = new Xyster_Orm_Entity_Field($translated, $values);
            $this->_fields[$translated] = $field;
            if ( $field->isPrimary() ) {
                $this->_primary[] = $translated;
            }
        }
    }
    
    /**
     * Asserts a field's presence in the entity class' members
     *
     * @param string $field
     * @throws Xyster_Orm_Entity_Exception
     */
    public function assertValidField( $field )
    {
    	if ( $field instanceof Xyster_Data_Field ) {
    		$field = trim($field->getName());
    	} else {
    		$field = trim($field);
	        require_once 'Xyster/Data/Field/Aggregate.php';
	        $matches = Xyster_Data_Field_Aggregate::match($field);
	        if ( count($matches) ) {
	            $field = trim($matches["field"]);
	        }
    	}
        
        $calls = Xyster_Orm_Xsql::splitArrow($field);
        /*
            for composite references (i.e.  supervisor->name )
            - check each column exists in its container
        */
        if ( count($calls) > 1 ) {
            
            $container = $this->_class;
            $meta = $this;
            foreach( $calls as $k=>$v ) {
                $meta->assertValidField($v);
                if ( $meta->isRelation($v) ) {
                    $details = $meta->getRelation($v);
                    if ( !$details->isCollection() ) {
                        $container = $details->getTo();
                        $meta = $this->_mapFactory->getEntityMeta($container);
                    } else {
                        break;
                    }
                } else { 
                    break;
                }
            }
            
        } else {

            /*
                for method calls
                - check method exists in class
                - check any method parameters that may themselves be members
            */
            if ( preg_match("/^(?P<name>[a-z0-9_]+)(?P<meth>\((?P<args>[\w\W]*)\))$/i", $field, $matches) ) {
                if ( !in_array($matches['name'], $this->getMembers()) ) {
                    require_once 'Xyster/Orm/Entity/Exception.php';
                    throw new Xyster_Orm_Entity_Exception($matches['name'] . ' is not a member of the ' . $this->_class . ' class' );
                }
                if ( strlen(trim($matches['args'])) ) {
                    foreach( Xyster_Orm_Xsql::splitComma($matches['args']) as $v ) {
                        if ( Xyster_Orm_Xsql::isValidField($v) ) {
                        	$this->assertValidField($v);
                        }
                    }
                }
            /*
                for properties and relationships
                - check column exists in class
            */
            } else if ( !in_array($field, $this->getMembers()) ) {
                require_once 'Xyster/Orm/Entity/Exception.php';
                throw new Xyster_Orm_Entity_Exception($field . ' is not a member of the ' . $this->_class . ' class');
            }
        }
    }
        
    /**
     * Creates a 'one to one' relationship for entities on the 'many' end of a 'one to many' relationship
     * 
     * Options can contain the following values:
     * 
     * <dl>
     * <dt>class</dt><dd>The foreign class. The relation name by default</dd>
     * <dt>id</dt><dd>The name of the foreign key field(s) on the declaring
     * entity.  This should either be an array (if multiple) or a string (if
     * one).  By default, this is <var>class</var>Id</dd>
     * <dt>filters</dt><dd>In XSQL, any Criteria that should be used
     * against the entity to be loaded</dd>
     * </dl>
     * 
     * @param string $name  The name of the relationship
     * @param array $options  An array of options
     * @return Xyster_Orm_Relation  The relationship created
     */
    public function belongsTo( $name, array $options = array() )
    {
        return $this->_baseCreate('belongs', $name, $options);
    }
    
    /**
     * Gets the class name of the entity
     *
     * @return string The class name
     */
    public function getEntityName()
    {
        return $this->_class;
    }
    
    /**
     * Gets the names of the fields for the entity
     *
     * @return array An array of field names
     */
    public function getFieldNames()
    {
        return array_keys($this->_fields);
    }

    /**
     * Gets the fields for the entity
     *
     * @return array An array of {@link Xyster_Orm_Entity_Field} objects
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Gets the mapper factory
     *
     * @return Xyster_Orm_Mapper_Factory_Interface
     */
    public function getMapperFactory()
    {
        return $this->_mapFactory;
    }
    
    /**
     * Gets all available class members (fields, relations, and methods)
     * 
     * @return array 
     */
    public function getMembers()
    {
        if ( !$this->_members ) {
            $this->_members = array_merge($this->_members, array_keys($this->_fields));
            $this->_members = array_merge($this->_members, $this->getRelationNames());
            $this->_members = array_merge($this->_members, get_class_methods($this->_class));
        }
        return $this->_members;
    }
    
    /**
     * Gets an array containing the field name or names for the primary key
     * 
     * @return array  The field names
     */
    public function getPrimary()
    {
        return array_values($this->_primary);
        // so current() can be called without reset()ing the array
    }
    
    /**
     * Gets the relationship by name
     *
     * @param string $name The name of the relationship
     * @return Xyster_Orm_Relation
     * @throws Xyster_Orm_Relation_Exception if the relationship name is invalid 
     */
    public function getRelation( $name )
    {
        if ( !$this->isRelation($name) ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception("'" . $name
                . "' is not a valid relation for the '" . $this->_class . "' class");
        }

        return $this->_relations[$name];
    }
    
    /**
     * Gets the names of all relations defined for this entity
     *
     * @return array The names of defined relations
     */
    public function getRelationNames()
    {
        return array_keys($this->_relations);
    }
    
    /**
     * Gets the relations for the entity
     *
     * @return array An array of {@link Xyster_Orm_Relation} objects
     */
    public function getRelations()
    {
        return $this->_relations;
    }
    
	/**
	 * Creates a 'one to one' relationship
	 * 
	 * Options can contain the following values:
	 * 
	 * <dl>
	 * <dt>class</dt><dd>The foreign class. The relation name by default</dd>
	 * <dt>id</dt><dd>The name of the foreign key field(s) on the declaring
	 * entity.  This should either be an array (if multiple) or a string (if
	 * one).  By default, this is <var>class</var>Id</dd>
	 * <dt>filters</dt><dd>In XSQL, any Criteria that should be used
	 * against the entity to be loaded</dd>
	 * </dl>
	 * 
	 * @param string $name  The name of the relationship
	 * @param array $options  An array of options
	 * @return Xyster_Orm_Relation  The relationship created
	 */
	public function hasOne( $name, array $options = array() )
	{
		return $this->_baseCreate('one', $name, $options);
	}
	
	/**
	 * Creates a 'one to many' relationship 
	 * 
	 * Options can contain the following values:
	 * 
	 * <dl>
	 * <dt>class</dt><dd>The foreign class.  The relation name minus a trailing
	 * 's' by default</dd>
	 * <dt>id</dt><dd>The name of the foreign key field(s) on the related
	 * entity.  This should either be an array (if multiple) or a string (if
	 * one).  By default, this is <var>class</var>Id</dd>
	 * <dt>filters</dt><dd>In XSQL, any Criteria that should be used
	 * against the entities to be loaded</dd>
	 * </dl>
	 * 
	 * @param string $name  The name of the relationship
	 * @param array $options  An array of options
	 * @return Xyster_Orm_Relation  The relationship created 
	 */	
	public function hasMany( $name, array $options = array() )
	{
		return $this->_baseCreate('many', $name, $options);
	}
	
	/**
	 * Creates a 'many to many' relationship
	 * 
	 * <dl>
	 * <dt>class</dt><dd>The class of entity to load through the join table. It
	 * will be the relationship name minus a trailing 's' by default.</dd>
	 * <dt>table</dt><dd>The join table name.  By default this will be
	 * <var>declaring_class</var>_<var>class</var></dd>
	 * <dt>left</dt><dd>The column(s) in the join table referencing the 
	 * declaringClass entity. By default: <var>declaring_class</var>_id</dd>
	 * <dt>right</dt><dd>The column(s) in the join table referencing the
	 * foreign entity.  By default, it's <var>class_name</var>_id</dd>
	 * <dt>filters</dt><dd>In XSQL, any Criteria that should be used
	 * against the join table.  Column names should be specified in the format 
	 * native to the data store (i.e. with underscores, not camelCase)</dd>
	 * </dl>
	 * 
	 * @param string $name  The name of the relationship
	 * @param array $options  An array of options
	 * @return Xyster_Orm_Relation  The relationship created
	 */
	public function hasJoined( $name, array $options = array() )
	{
		return $this->_baseCreate('joined', $name, $options);
	}
	
	/**
     * Whether the entity has a relationship with the name supplied
     * 
     * @param string $name The name of the relationship
     * @return boolean
     */
	public function isRelation( $name )
	{
	    return array_key_exists($name, $this->_relations);
	}
    
    /**
     * Verifies if a {@link Xyster_Data_Symbol} is runtime
     * 
     * @param Xyster_Data_Symbol $object 
     * @param string $class
     * @return boolean
     */
    public function isRuntime( Xyster_Data_Symbol $object )
    {
        if ( $object instanceof Xyster_Data_Criterion ) {
            
            foreach( Xyster_Data_Criterion::getFields($object) as $v ) {
                if ($this->isRuntime($v)) {
                    return true;
                }
            }
            return false;
            
        } else if ( $object instanceof Xyster_Data_Field ) {
            
            $name = $object->getName();
	        if ( !isset($this->_runtime[$name]) ) {
	            $this->_runtime[$name] = $this->_isRuntime($object);
	        }
	        return $this->_runtime[$name];
            
        } else if ( $object instanceof Xyster_Data_Sort ) {
            
            return $this->isRuntime($object->getField());
            
        }
        
        require_once 'Xyster/Orm/Exception.php';
        throw new Xyster_Orm_Exception('Unexpected type: ' . gettype($object));
    }
    	
    /**
     * Base creator method
     * 
     * @param string $type The type of the relationship
     * @param string $name The name of the relationship
     * @param array $options An array of options
     * @return Xyster_Orm_Relation
     * @throws Xyster_Orm_Relation_Exception if the relationship is already defined
     */
    protected function _baseCreate( $type, $name, array $options )
    {
        $declaringClass = $this->_class;
        
        if ( array_key_exists($name, $this->_relations) ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception("The relationship '" . $name . "' already exists");
        }

        require_once 'Xyster/Orm/Relation.php';
        $this->_relations[$name] = new Xyster_Orm_Relation($this, $type, $name, $options);
            
        return $this->_relations[$name];
    }
    
    /**
     * Returns if a field is runtime
     *
     * @param Xyster_Data_Field $field
     * @return boolean
     */
    protected function _isRuntime( Xyster_Data_Field $field )
    {
    	$calls = Xyster_Orm_Xsql::splitArrow($field->getName());

        if ( count($calls) == 1 ) {
        	
        	$field = $field->getName();
            // the call isn't composite - could be a member or a relation
            return ( Xyster_Orm_Xsql::isMethodCall($calls[0]) ) ? true :
                ( !in_array($field, $this->getFieldNames())
                    && !$this->isRelation($field->getName()) );
                    
        } else {
            
            // the call is composite - loop through to see if we can figure 
            // out the type bindings
            $container = $this->_class;
            $meta = $this;
            foreach( $calls as $call ) {
                if ( Xyster_Orm_Xsql::isMethodCall($call) ) {
                    return true;
                } else {
                    $isRel = $meta->isRelation($call);
                    if ( !in_array($call, array_keys($meta->getFields()))
                        && !$isRel ) {
                        return true;
                    } else if ( $isRel ) {
                        $container = $meta->getRelation($call)->getTo();
                        $meta = $this->_mapFactory->getEntityMeta($container);
                    }
                }
            }
            return false;

        }
    }
}