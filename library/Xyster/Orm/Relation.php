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
     * @var mixed
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
     * @var mixed
     */
    protected $_joinLeft;
    /**
     * The join table field linking to the right entity's primary key fields
     *
     * @var string
     */
    protected $_joinRight;
    
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
	 * @param string $declaringClass  The class to which this belongs
	 * @param string $type  One of belongs, one, many, joined
	 * @param string $name  The name of the relationship
	 * @param array $options  An array of options
	 */
	protected function __construct( $declaringClass, $type, $name, array $options = array() )
	{	
		if ( !in_array($type,self::$_types) ) {
			require_once 'Xyster/Orm/Exception.php';
			throw new Xyster_Orm_Exception("'" . $type . "' is an invalid relationship type");
		}

		$this->_from = $declaringClass;
		$class = ( array_key_exists('class',$options) ) ? $options['class'] : null;
		$id = ( array_key_exists('id',$options) ) ? $options['id'] : null;
		$filters = ( array_key_exists('filters',$options) ) ? $options['filters'] : null;

		/**
		 * @todo do something with filters
		 */
//		if ( $filters ) {
//			$filters = wfDataSyntax::parseCriterion($filters);
//		}
		
		if ( !$class && ( $type == 'one' || $type == 'belongs' ) ) {
			$class = $name;
		} else if ( !$class && ( $type == 'many' || $type == 'joined' ) ) { 
			$class = ( substr($name,-1) == 's' ) ? substr($name,0,-1) : $name;
		}
		if ( !$id && $type != 'joined' ) { 
			$id = ( $type == 'many' ) ? $declaringClass.'Id' : $class.'Id';
		}

	    $this->_name = $name;
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
				$orm = Xyster_Orm::getInstance();
				$leftMap = $orm->getMapper($declaringClass);
				$rightMap = $orm->getMapper($class);
				$this->_joinTable = array_key_exists('table',$options) ? 
					$options['table'] : $leftMap->getTable().'_'.$rightMap->getTable();
				$this->_joinLeft = array_key_exists('left',$options) ? 
					$options['left'] : $leftMap->getTable().'_id';
				$this->_joinRight = array_key_exists('right',$options) ?
					$options['right'] : $rightMap->getTable().'_id';
				$this->_joinAdd = ( array_key_exists('add',$options) &&
					is_array($options['add']) ) ? $options['add'] : null;
			}
		}
	}

    /**
     * Gets the relationship by name and entity
     *
     * @param mixed $entity Either a {@link Xyster_Orm_Entity} or the class name
     * @param string $name The name of the relationship
     * @return Xyster_Orm_Relation
     * @throws Xyster_Orm_Exception if the relationship name is invalid 
     */
    static public function get( $entity, $name )
    {
        if ( is_object($entity) ) {
            $entity = get_class($entity);
        }

        if ( !self::isValid($entity, $name) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception("'" . $name
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
     * Gets the id field name
     * 
     * @return string
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
	 * Checks to see if the class referenced by this relationship has a belongs
	 * 
	 * This relationship must be a 'many' to return true, and the target class
	 * must have a 'belongs' relationship pointing back to this declaring class
	 * 
	 * @return boolean
	 */
	public function hasBelongsTo()
	{
		$has = false;
		if ( isset(self::$_registry[$this->_to]) && $this->_type == 'many' ) {
			foreach( self::$_registry[$this->_to] as $v ) {
				if ( $v->_type == 'belongs' && $v->_to == $this->_from ) {
					$has = true;
					break;
				}
			}
		}
		return $has;
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
     * Base creator method
     * 
     * @param string $type The type of the relationship
     * @param string $name The name of the relationship
     * @param array $options An array of options
     * @return Xyster_Orm_Relation
     * @throws Xyster_Orm_Exception if the relationship is already defined
     */
    static protected function _baseCreate( $type, $name, array $options )
    {
        $bt = debug_backtrace();
        if ( !isset($bt[2]) || !isset($bt[2]['class'])
            || substr($bt[2]['class'],-6,6) != 'Mapper' ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception("This method must only be called from inside a Xyster_Orm_Mapper");
        }
        // the 'object' entry of the bt array should be the mapper
        $declaringClass = $bt[2]['object']->getEntityClass();
        
        if ( self::isValid($declaringClass, $name) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception("The relationship '" . $name . "' already exists");
        }

        self::$_registry[$declaringClass][$name] = 
            new self($declaringClass,$type,$name,$options);

        return self::$_registry[$declaringClass][$name];
    }
}