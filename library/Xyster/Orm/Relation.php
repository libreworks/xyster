<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Mapper
 */
require_once 'Xyster/Orm/Mapper.php';
/**
 * A factory and instance for entity relationships
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Relation
{
    /**
     * The type of relation; one of ('one','belongs','many','joined')
     *
     * @var string
     */
    protected $_type;
    /**
     * The name of the relation
     *
     * @var string
     */
    protected $_name;
    /**
     * The class that defines the relation
     *
     * @var string
     */
    protected $_from;
    /**
     * The class on the target end of the relation
     *
     * @var string
     */
    protected $_to;
    /**
     * The primary key field(s) involved in the relation
     *
     * @var array
     */
    protected $_id;
    /**
     * Any filters associated with the relation
     *
     * @var Xyster_Data_Criterion
     */
    protected $_filters;
    /**
     * The activity to perform on entity delete
     *
     * @var string
     */
    protected $_onDelete;
    /**
     * The activity to perform when a primary key is changed
     *
     * @var string
     */
    protected $_onUpdate;
    /**
     * In a join relationship, this is the table linking the two entities
     *
     * @var string
     */
    protected $_joinTable;
    /**
     * The join table field linking to the left entity's primary key fields
     *
     * @var string 
     */
    protected $_joinLeft;
    /**
     * The join table field linking to the right entity's primary key fields
     *
     * @var string
     */
    protected $_joinRight;
    
    /**
     * The "belongs" relation opposite a "many"
     *
     * @var Xyster_Orm_Relation
     */
    protected $_reverse;
    
    /**
     * The container for instantiated relations
     *
     * @var array 
     */
    static protected $_registry = array();
    /**
     * The acceptable types
     * 
     * @var array
     */
	static private $_types = array( 'belongs','one','many','joined' );
    
    /**
	 * Create a new relationship
	 *
	 * @param Xyster_Orm_Mapper $map The mapper of the class owning the relation
	 * @param string $type  One of belongs, one, many, joined
	 * @param string $name  The name of the relationship
	 * @param array $options  An array of options
	 */
	protected function __construct( Xyster_Orm_Mapper $map, $type, $name, array $options = array() )
	{	
	    $declaringClass = $map->getEntityName();
	    
		if ( !in_array($type,self::$_types) ) {
			require_once 'Xyster/Orm/Relation/Exception.php';
			throw new Xyster_Orm_Relation_Exception("'" . $type . "' is an invalid relationship type");
		}
		
		$class = ( array_key_exists('class',$options) ) ? $options['class'] : null;
		// determine the class if not provided
		if ( !$class && ( $type == 'one' || $type == 'belongs' ) ) {
			$class = ucfirst($name);
		} else if ( !$class && ( $type == 'many' || $type == 'joined' ) ) { 
			$class = ( substr($name,-1) == 's' ) ? substr($name,0,-1) : $name;
			$class = ucfirst($class);
		}

		$id = ( array_key_exists('id',$options) ) ? $options['id'] : null;
		// get the primary key from the mapper if not provided
		if ( !$id && $type != 'joined' ) {
		    $map = ( $type == 'many' ) ? $map : Xyster_Orm_Mapper::factory($class);
	        $fields = $map->getFields();
	        $id = array();
	        foreach ( $fields as $field ) {
	            $id[] = $field->getName();
	        }
		}
		if ( !is_array($id) ) {
		    $id = array($id);
		}

		/**
		 * @todo do something with filters
		 */
        $filters = ( array_key_exists('filters',$options) ) ? $options['filters'] : null;
//		if ( $filters ) {
//			$filters = wfDataSyntax::parseCriterion($filters);
//		}

	    $this->_name = $name;
		$this->_from = $declaringClass;
		$this->_to = $class;
		$this->_id = $id;
		$this->_filters = $filters;
		$this->_type = $type;

		if ( $type == 'many' || $type == 'joined' ) {
		    /**
		     * @todo replace this with the onDelete/onUpdate stuff
		     */
//			if ( isset($options['remove']) ) {
//				$this->_remove = ( is_array($options['remove']) ) ?
//					$options['remove'] : (bool)$options['remove'];
//			}
			if ( $type == 'joined' ) {
				$leftMap = $map;
				$rightMap = Xyster_Orm_Mapper::factory($class);
				$this->_joinTable = array_key_exists('table',$options) ? 
					$options['table'] : $leftMap->getTable().'_'.$rightMap->getTable();
				$this->_joinLeft = array_key_exists('left',$options) ? 
					$options['left'] : $leftMap->getPrimary();
				$this->_joinRight = array_key_exists('right',$options) ?
					$options['right'] : $rightMap->getPrimary();
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
	static public function belongs( $name, array $options = array() )
	{
		return self::_baseCreate('belongs',$name,$options);
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
	static public function one( $name, array $options = array() )
	{
		return self::_baseCreate('one',$name,$options);
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
	static public function many( $name, array $options = array() )
	{
		return self::_baseCreate('many',$name,$options);
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
	static public function joined( $name, array $options = array() )
	{
		return self::_baseCreate('joined',$name,$options);
	}

    /**
     * Gets the relationship by name and entity
     *
     * @param mixed $entity Either a {@link Xyster_Orm_Entity} or the class name
     * @param string $name The name of the relationship
     * @return Xyster_Orm_Relation
     * @throws Xyster_Orm_Relation_Exception if the relationship name is invalid 
     */
    static public function get( $entity, $name )
    {
        if ( is_object($entity) ) {
            $entity = get_class($entity);
        }

        if ( !self::isValid($entity, $name) ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception("'" . $name
                . "' is not a valid relation for the '" . $entity . "' class");
        }

        return self::$_registry[$entity][$name];
    }
    /**
     * Gets all relationships by entity
     * 
     * @param mixed $entity Either a {@link Xyster_Orm_Entity} or the class name
     * @return array
     */
    static public function getAll( $entity )
    {
        if ( is_object($entity) ) {
            $entity = get_class($entity);
        }

        return ( isset(self::$_registry[$entity]) ) ?
            self::$_registry[$entity] : array();
    }
    /**
     * Gets the names of any relationships the entity has
     * 
     * @param mixed $entity Either a {@link Xyster_Orm_Entity} or the class name
     * @return array
     */
    static public function getNames( $entity )
    {
        if ( is_object($entity) ) {
            $entity = get_class($entity);
        }

        return ( array_key_exists($entity,self::$_registry) ) ?
            array_keys(self::$_registry[$entity]) : array();
    }
    /**
     * Whether the entity supplied has a relationship with the name supplied
     * 
     * @param mixed $entity Either a {@link Xyster_Orm_Entity} or the class name
     * @param string $name The name of the relationship
     * @return boolean
     */
    static public function isValid( $entity, $name )
    {
        return in_array($name, self::getNames($entity));
    }

	/**
	 * Loads the {@link Xyster_Orm_Entity} or {@link Xyster_Orm_Set}
	 *
	 * @param Xyster_Orm_Entity $entity
	 * @param string $name
	 * @return mixed
	 */
	static public function load( Xyster_Orm_Entity $entity, $name )
	{
		$linked = null;
		$relation = self::get($entity,$name);

		require_once 'Xyster/Orm.php';
		$orm = Xyster_Orm::getInstance();
		
		if ( !$relation->isCollection() && $relation->_filters ) {

		    /*
		     * A one-to-one with filters
		     */
		    $criteria = Xyster_Data_Junction::all($relation->_filters,
		        $entity->getPrimaryKeyAsCriterion());
			$linked = $orm->find($relation->_to, $criteria);
			
		} else if ( !$relation->isCollection() ) {
		    
		    /*
		     * A one-to-one without filters
		     */
            $key = array();
            foreach( $relation->_id as $name ) {
                $key[$name] = $entity->$name;
            }
		    $linked = $orm->get($relation->_to, $key);
			
		} else {
		    
		    /*
		     * A one-to-many or many-to-many with filters
		     * We will use the base primary key value
		     */
            $key = $entity->getPrimaryKey(true);
			if ( $key ) {
			    
				if ( $relation->_type == 'many' ) {
                    
				    $keyNames = array_keys($key);
				    $criteria = null;
				    for( $i=0; $i<count($key); $i++ ) {
				        $keyName = $keyNames[$i];
				        $keyValue = $key[$keyName];
				        // build a criterion object based on the primary key(s)
			            require_once 'Xyster/Data/Expression.php';
			            $thiskey = Xyster_Data_Expression::eq($relation->_id[$i],
			                $keyValue);
			            if ( !$criteria ) {
			                $criteria = $thiskey;
			            } else if ( $criteria instanceof Xyster_Data_Expression ) {
			                require_once 'Xyster/Data/Junction.php';
			                $criteria = Xyster_Data_Junction::all( $criteria, $thiskey );
			            } else if ( $criteria instanceof Xyster_Data_Junction ) {
			                $criteria->add($thiskey);
			            }
				    }
				    if ( $relation->_filters ) {
				        require_once 'Xyster/Data/Junction.php';
				        $criteria = Xyster_Data_Junction::all($criteria,
				            $relation->_filters);
				    }
					$linked = $orm->findAll($relation->_to,$criteria);

				} else if ( $relation->_type == 'joined' ) {

					$map = $orm->getMapper($relation->_from);
					$linked = $map->getJoined($entity,$name);

				}
				
			} else {
			    
			    $linked = $orm->getMapper($relation->_to)->getSet();
			    
			}
			
			$linked->relateTo($relation, $entity);
			
		}

		return $linked;
	}

    /**
     * Gets the filters
     * 
     * @return Xyster_Data_Criterion
     */
    public function getFilters()
    {
        return $this->_filters;
    }
    /**
     * Gets the class that owns the relationship
     * 
     * @return string
     */
    public function getFrom()
    {
        return $this->_from;
    }
    /**
     * Gets the ids involved
     * 
     * @return array
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Gets the left entity column name in the join table
     * 
     * @return string
     */
    public function getLeft()
    {
        return $this->_joinLeft;
    }
    /**
     * Gets the name of the relationship
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * Gets the right entity column name in the join table
     * 
     * @return string
     */
    public function getRight()
    {
        return $this->_joinRight;
    }
    /**
     * Gets the join table name
     * 
     * @return string
     */
    public function getTable()
    {
        return $this->_joinTable;
    }
    /**
     * Gets the class of the relationship
     * 
     * @return string
     */
    public function getTo()
    {
        return $this->_to;
    }
    /**
     * Gets the type of the relationship
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    /**
     * If this relation is a 'many', this returns the 'belongs' relation
     *
     * @return Xyster_Orm_Relation
     * @throws Xyster_Orm_Relation_Exception
     */
    public function getReverse()
    {
        if ( $this->_type != 'many' ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('This method is only intended for "many" relations');
        }
        
        if ( $this->_reverse === null ) {
	        foreach( self::getAll($this->_to) as $relation ) {
	            if ( $relation->_type == 'belongs' && $relation->_to == $this->_from ) {
	                $this->_reverse = $relation;
	                break;
	            }
	        }
	        // so we don't bother searching on subsequent calls
	        if ( $this->_reverse === null ) {
	            $this->_reverse = false;
	        }
        }
        
        return $this->_reverse;
    }
	/**
	 * Checks to see if the class referenced by this relationship has a belongs
	 * 
	 * This relationship must be a 'many' to return true, and the target class
	 * must have a 'belongs' relationship pointing back to this declaring class
	 * 
	 * @return boolean
	 */
	public function hasBelongsTo()
	{
		return $this->getReverse() instanceof self;
	}
    /**
     * This returns true if the type is 'many' or 'joined', false otherwise
     *
     * @return boolean
     */
    public function isCollection()
    {
        return $this->_type == 'many' || $this->_type == 'joined';
    }
    /**
     * Relates one entity to another (if one-to-one)
     *
     * @param Xyster_Orm_Entity $from The entity that owns the many set
     * @param Xyster_Orm_Entity $to An entity in the many set
     * @throws Xyster_Orm_Relation_Exception if $from or $to are of the wrong type
     */
    public function relate( Xyster_Orm_Entity $from, Xyster_Orm_Entity $to )
    {
        if ( $this->_type != 'many' ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('This can only be done with "many" relations');
        }

        $fromClass = $this->_from;
        if (! $from instanceof $fromClass ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('$from must be an instance of '.$fromClass);
        }
        
        $toClass = $this->_to;
        if (! $to instanceof $toClass ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('$to must be an instance of '.$toClass);
        }
        
        if ( $this->hasBelongsTo() ) {
            $relation = $this->getReverse();
            $name = $relation->getName();
            $to->$name = $from;
            $primary = $from->getPrimaryKey(true);
            if ( $primary ) {
                $keyNames = array_keys($primary);
                $foreignNames = $relation->getId();
                for( $i=0; $i<count($keyNames); $i++ ) {
                    $keyName = $keyNames[$i];
                    $foreignName = $foreignNames[$i];
                    $to->$foreignName = $from->$keyName;
                }
            }
        }
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
    static protected function _baseCreate( $type, $name, array $options )
    {
        $bt = debug_backtrace();
        if ( !isset($bt[2]) || !isset($bt[2]['class'])
            || substr($bt[2]['class'],-6,6) != 'Mapper' ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception("This method must only be called from inside a Xyster_Orm_Mapper");
        }
        // the 'object' entry of the bt array should be the mapper
	    $map = $bt[2]['object'];
        $declaringClass = $map->getEntityName();
        
        if ( self::isValid($declaringClass, $name) ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception("The relationship '" . $name . "' already exists");
        }

        self::$_registry[$declaringClass][$name] = 
            new self($map,$type,$name,$options);
            
        return self::$_registry[$declaringClass][$name];
    }
}