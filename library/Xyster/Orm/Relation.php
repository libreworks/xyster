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
     * @var array 
     */
    protected $_joinLeft;
    /**
     * The join table field linking to the right entity's primary key fields
     *
     * @var array
     */
    protected $_joinRight;
    
    /**
     * The "belongs" relation opposite a "many"
     *
     * @var Xyster_Orm_Relation
     */
    protected $_reverse;
    
    /**
     * The mapper factory
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mapFactory;

    /**
     * The acceptable types
     * 
     * @var array
     */
	static private $_types = array('belongs', 'one', 'many', 'joined');
    
    /**
	 * Create a new relationship
	 *
	 * @param Xyster_Orm_Mapper $map The mapper of the class owning the relation
	 * @param string $type  One of belongs, one, many, joined
	 * @param string $name  The name of the relationship
	 * @param array $options  An array of options
	 */
	public function __construct( Xyster_Orm_Entity_Meta $meta, $type, $name, array $options = array() )
	{	
	    $declaringClass = $meta->getEntityName();
	    $this->_mapFactory = $meta->getMapperFactory();
	    
		if ( !in_array($type, self::$_types) ) {
			require_once 'Xyster/Orm/Relation/Exception.php';
			throw new Xyster_Orm_Relation_Exception("'" . $type . "' is an invalid relationship type");
		}

		$class = ( array_key_exists('class', $options) ) ? $options['class'] : null;
		// determine the class if not provided
		if ( !$class && ( $type == 'one' || $type == 'belongs' ) ) {
			$class = ucfirst($name);
		} else if ( !$class && ( $type == 'many' || $type == 'joined' ) ) { 
			$class = ( substr($name,-1) == 's' ) ? substr($name, 0, -1) : $name;
			$class = ucfirst($class);
		}

		$id = ( array_key_exists('id', $options) ) ? $options['id'] : null;
		// get the primary key from the mapper if not provided
		if ( !$id && $type != 'joined' ) {
		    // if it's a one-to-many, we're just using the declaring class' key
	        // if it's a one-to-one, we need the related class' key
		    $meta = ( $type == 'many' ) ? $meta :
		        $this->_mapFactory->getEntityMeta($class);
	        $id = $meta->getPrimary();
		}
		if ( $type != 'joined' && !is_array($id) ) {
		    $id = array($id);
		}

		$filters = ( array_key_exists('filters', $options) ) ?
		    $options['filters'] : null;
		if ( $filters ) {
		    require_once 'Xyster/Orm/Query/Parser.php';
		    $parser = new Xyster_Orm_Query_Parser($this->_mapFactory);
			$filters = $parser->parseCriterion($filters);
		}

	    $this->_name = $name;
		$this->_from = $declaringClass;
		$this->_to = $class;
		$this->_id = $id;
		$this->_filters = $filters;
		$this->_type = $type;

		if ( $type == 'many' || $type == 'joined' ) {
		    /**
		     * @todo put in the onDelete/onUpdate stuff
		     */
			if ( $type == 'joined' ) {
				$leftMap = $this->_mapFactory->get($declaringClass);
				$rightMap = $this->_mapFactory->get($class);
				$leftMeta = $leftMap->getEntityMeta();
				$rightMeta = $rightMap->getEntityMeta();

				$this->_joinTable = array_key_exists('table', $options) ? 
				    $options['table'] : $leftMap->getTable().'_'.$rightMap->getTable();
				
				if ( isset($options['left']) ) {
				    // make sure number of fields matches number of primary keys
				    $leftCount = is_array($options['left']) ?
				        count($options['left']) : 1;
				    if ( $leftCount != count($leftMeta->getPrimary()) ) {
				        require_once 'Xyster/Orm/Relation/Exception.php';
				        throw new Xyster_Orm_Relation_Exception('Number of "left" keys do not match number of keys in left table');
				    }
				    $this->_joinLeft = (array) $options['left'];
				} else {
				    $this->_joinLeft = array_map(array($leftMap,'untranslateField'), $leftMeta->getPrimary());
				}

				if ( isset($options['right']) ) {
				    // make sure number of fields matches number of primary keys
				    $rightCount = is_array($options['right']) ?
				        count($options['right']) : 1;
				    if ( $rightCount != count($rightMeta->getPrimary()) ) {
				        require_once 'Xyster/Orm/Relation/Exception.php';
				        throw new Xyster_Orm_Relation_Exception('Number of "right" keys do not match number of keys in right table');
				    }
				    $this->_joinRight = (array) $options['right'];
				} else {
				    $this->_joinRight = array_map(array($rightMap,'untranslateField'), $rightMeta->getPrimary());
				}
			}
		}
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
     * @return array
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
     * @return array
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
            $meta = $this->_mapFactory->getEntityMeta($this->_to);
	        foreach( $meta->getRelations() as $relation ) {
	            /* @var $relation Xyster_Orm_Relation */
	            if ( $relation->_type == 'belongs'
					&& $relation->_to == $this->_from
	                && $relation->_id == $this->_id ) {
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
	 * Loads the {@link Xyster_Orm_Entity} or {@link Xyster_Orm_Set}
	 *
	 * @param Xyster_Orm_Entity $entity The entity to use
	 * @param string $name
	 * @return mixed
	 */
	public function load( Xyster_Orm_Entity $entity )
	{
		$linked = null;
		$manager = $this->_mapFactory->getManager();
		
		if ( !$this->isCollection() ) {
		    
		    /*
		     * A one-to-one
		     */
            $key = array();
            $keys = $this->_mapFactory->getEntityMeta($this->_to)->getPrimary();
            foreach( $this->_id as $i => $name ) {
                $key[$keys[$i]] = $entity->$name;
            }
		    $linked = $manager->get($this->_to, $key);
			
		} else {
		    
		    /*
		     * A one-to-many or many-to-many with filters
		     * We will use the base primary key value
		     */
            $primaryKey = $entity->getPrimaryKey(true);
            
			if ( count($primaryKey) && current($primaryKey) ) {
			    
			    if ( $this->_type == 'many' ) {

           		    $find = array();
        		    $id = (array) $this->_id;
        		    $i = 0;
                    foreach( $primaryKey as $keyName => $keyValue ) {
                        $find[] = Xyster_Data_Expression::eq($id[$i], $keyValue);
                        $i++;
                    }
                    $criteria = Xyster_Data_Criterion::fromArray('AND', $find);
    			        
				    if ( $this->_filters ) {
				        require_once 'Xyster/Data/Junction.php';
				        $criteria = Xyster_Data_Junction::all($criteria,
				            $this->_filters);
				    }
					$linked = $manager->findAll($this->_to, $criteria);

				} else if ( $this->_type == 'joined' ) {

					$linked = $manager->getJoined($entity, $this);

				}
				
			} else {
			    
			    $linked = $this->_mapFactory->get($this->_to)->getSet();
			    
			}
			
			$linked->relateTo($this, $entity);
			
		}

		return $linked;
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
        // make sure this relation type is 'many'
        if ( $this->_type != 'many' ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('This can only be done with "many" relations');
        }

        // make sure the $from object is the $this->_from class
        $fromClass = $this->_from;
        if (! $from instanceof $fromClass ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('$from must be an instance of '.$fromClass);
        }
        
        // make sure the $to object is the $this->_to class
        $toClass = $this->_to;
        if (! $to instanceof $toClass ) {
            require_once 'Xyster/Orm/Relation/Exception.php';
            throw new Xyster_Orm_Relation_Exception('$to must be an instance of '.$toClass);
        }
        
        // there's only work to do if there's a belongsTo relationship
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
}