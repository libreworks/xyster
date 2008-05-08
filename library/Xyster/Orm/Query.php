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
 * @see Xyster_Data_Sort_Clause
 */
require_once 'Xyster/Data/Sort/Clause.php';
/**
 * A query object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Query
{
    const WHERE = 'where';
    const ORDER = 'order';
    const LIMIT = 'limit';
    const OFFSET = 'offset';

    /**
     * The entity being queried
     *
     * @var string
     */
    protected $_class = '';
    
    /**
     * The parts of the query
     * 
     * @var array
     */
    protected $_parts = array();
    
    /**
     * The parts of the query that can be used in the backend
     *
     * @var array
     */
    protected $_backend = array();
    
    /**
     * The orm manager
     *
     * @var Xyster_Orm_Manager
     */
    protected $_manager;
    
    /**
     * The entity meta
     *
     * @var Xyster_Orm_Entity_Type
     */
    protected $_meta;
    
    /**
     * The parts of the query that must be used at runtime
     *
     * @var array
     */
    protected $_runtime = array();

    /**
     * Creates a new query object
     *
     * @param string $class  The entity to query
     */
    public function __construct( $class, Xyster_Orm_Manager $manager )
    {
        $this->_class = $class;
        $this->_manager = $manager;
        $this->_initParts();
        $this->_meta = $manager->getMapperFactory()->getEntityMeta($class);
    }
    
    /**
     * Executes the query
     * 
     * @return Xyster_Orm_Set
     */
    public function execute()
    {
        $map = $this->_manager->getMapperFactory()->get($this->_class);

        // execute the query in the backend
		$set = $this->_manager->executeQuery($this);

        // apply any runtime filters to the entity set
		if ( count($this->_runtime[self::WHERE]) ) {
    		$set->filter(Xyster_Data_Criterion::fromArray('AND', $this->_runtime[self::WHERE]));
		}

		// if the query is runtime, enforce the offset and limit
		if ( ( $this->_parts[self::LIMIT] || $this->_parts[self::OFFSET] )
		    && $this->isRuntime() ) {
			$entities = $map->getSet();
			$offset = 0;
			foreach( $set as $entity ) {
				if ( $offset < $this->_parts[self::OFFSET] ) {
				    $offset++;
				} else { 
				    $entities->add($entity);
				}
				if ( $this->_parts[self::LIMIT] && count($entities) == $this->_parts[self::LIMIT] ) {
				    break;
				}
			}
			$set = $entities;
		}
		
		// apply any runtime sort ordering to the entity set
		if ( $this->_runtime[self::ORDER] ) {
			$set->sortBy($this->_parts[self::ORDER]);
		}
		return $set;
    }
    
    /**
     * Gets the criteria that can be run in the backend
     *
     * @return array
     */
    public function getBackendWhere()
    {
        return $this->_backend[self::WHERE];
    }
    
    /**
     * Gets the entity class being queried
     * 
     * @return string the entity class name
     */
    public function getFrom()
    {
        return $this->_class;
    }

    /**
     * Gets the maximum number of results to be returned
     * 
     * @return int the maximum result count
     */
    public function getLimit()
    {
        return $this->getPart(self::LIMIT);
    }

    /**
     * Gets the starting point in the results
     *
     * @return int the offset starting point
     */
    public function getOffset()
    {
        return $this->getPart(self::OFFSET);
    }

    /**
     * Gets the order clause (an array of {@link Xyster_Data_Sort} objects)
     * 
     * @return Xyster_Data_Sort_Clause 
     */
    public function getOrder()
    {
        return $this->getPart(self::ORDER);
    }

    /**
     * Gets a part of the query
     *
     * @param string $part one of the class constants
     * @return mixed
     */
    public function getPart( $part )
    {
        return ( array_key_exists($part, $this->_parts) ) ?
            $this->_parts[$part] : null;
    }

    /**
     * Gets the where clause (an array of {@link Xyster_Data_Criterion} objects)
     *
     * @return array
     */
    public function getWhere()
    {
        return $this->getPart(self::WHERE);
    }

    /**
     * Gets whether this query has an order clause that evaluates at runtime
     *
     * @return boolean
     */
    public function hasRuntimeOrder()
    {
        return (bool) $this->_runtime[self::ORDER];
    }

    /**
     * Gets whether this query has a where clause that evaluates at runtime
     *
     * @return boolean 
     */
    public function hasRuntimeWhere()
    {
        return count($this->_runtime[self::WHERE]) > 0;
    }
    
    /**
     * Gets whether this query has parts that are evaluated at runtime
     *
     * @return boolean
     */
    public function isRuntime()
    {
        return $this->hasRuntimeOrder() || $this->hasRuntimeWhere();
    }
    
    /**
     * Impose a maximum number of records to return and a number to skip
     *
     * @param int $limit The maximum number of records to return
     * @param int $offset The number of records to skip
     * @return Xyster_Orm_Query provides a fluent interface
     */
    public function limit( $limit, $offset=0 )
    {
        $this->_parts[self::LIMIT] = intval($limit);
        $this->_parts[self::OFFSET] = intval($offset);
        return $this;
    }

    /**
     * Adds a sorting to the results
     *
     * @param Xyster_Data_Sort $order
     * @return Xyster_Orm_Query provides a fluent interface
     */
    public function order( Xyster_Data_Sort $order )
    {
    	$this->_meta->assertValidField($order->getField());
        $this->_runtime[self::ORDER] |= $this->_meta->isRuntime($order);
            
        $this->getOrder()->add($order);
        
        return $this;
    }
    
    /**
     * Adds a criterion to the selection
     * 
     * @param Xyster_Data_Criterion $where
     * @return Xyster_Orm_Query provides a fluent interface 
     */
    public function where( Xyster_Data_Criterion $where )
    {
        foreach( Xyster_Data_Criterion::getFields($where) as $field ) {
            if ( $field instanceof Xyster_Data_Field_Aggregate ) {
                require_once 'Xyster/Orm/Query/Exception.php';
                throw new Xyster_Orm_Query_Exception('Aggregated fields are not allowed in this query');
            }
            $this->_meta->assertValidField($field);
        }
        
        if ( $this->_meta->isRuntime($where) ) {
            $this->_runtime[self::WHERE][] = $where;
        } else {
            $this->_backend[self::WHERE][] = $where;
        }
        
        $this->_parts[self::WHERE][] = $where;
        
        return $this;
    }
    
    /**
     * Initializes the parts container
     *
     */
    protected function _initParts()
    {
        $this->_parts[self::LIMIT] = 0;
        $this->_parts[self::OFFSET] = 0;
        $this->_parts[self::WHERE] = array();
        $this->_parts[self::ORDER] = new Xyster_Data_Sort_Clause;
        
        $this->_runtime[self::WHERE] = array();
        $this->_runtime[self::ORDER] = false;
        
        $this->_backend[self::WHERE] = array();
    }
}