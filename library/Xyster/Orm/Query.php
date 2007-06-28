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
 * @see Xyster_Orm_Query_Parser
 */
require_once 'Xyster/Orm/Query/Parser.php';
/**
 * A query object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Query
{
    const WHERE = 'where';
    const ORDER = 'order';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    
    protected $_table = "";
    protected $_parts = array();
    protected $_backend = array();
    protected $_runtime = array();

    /**
     * Creates a new query object
     *
     * @param string $table  The table to query
     */
    public function __construct( $table )
    {
        $this->_table = $table;
        $this->_initParts();
    }
    
    /**
     * Executes the query
     * 
     * @return Xyster_Orm_Set
     */
    public function execute()
    {
        $map = Xyster_Orm_Mapper::factory($this->_table);

		$set = $map->getBackEnd()->query($this);

		if ( count($this->_runtime[self::WHERE]) ) {
    		$set->filter( Xyster_Data_Junction::fromArray('AND',$this->_runtime[self::WHERE]) );
		}

		if ( ( $this->_parts[self::LIMIT] || $this->_parts[self::OFFSET] )
		    && ( $this->_runtime[self::ORDER] || count($this->_runtime[self::WHERE]) ) ) {

			$entities = $map->getSet();
			$offset = 0;
			foreach( $set as $entity ) {
				if ( $offset < $this->_parts[self::OFFSET] ) {
				    $offset++;
				} else { 
				    $entities->add($entity);
				    $this->_putInSecondaryCache($entity);
				}
				if ( $this->_parts[self::LIMIT] && count($entities) == $this->_parts[self::LIMIT] ) {
				    break;
				}
			}
			$set = $entities;
		}
		
		if ( $this->_runtime[self::ORDER] ) {
			$set->sortBy($this->_parts[self::ORDER]);
		}
		return $set;
    }
    
    /**
     * Gets the entity class being queried
     * 
     * @return string the entity class name
     */
    public function getFrom()
    {
        return $this->_table;
    }

    /**
     * Gets the maximum number of results to be returned
     * 
     * @return int the maximum result count
     */
    public function getLimit()
    {
        return $this->getPart( self::LIMIT );
    }

    /**
     * Gets the starting point in the results
     *
     * @return int the offset starting point
     */
    public function getOffset()
    {
        return $this->getPart( self::OFFSET );
    }

    /**
     * Gets the order clause (an array of {@link Xyster_Data_Sort} objects)
     * 
     * @return array 
     */
    public function getOrder()
    {
        return $this->getPart( self::ORDER );
    }

    /**
     * Gets a part of the query
     *
     * @param string $part one of the class constants
     * @return mixed
     */
    public function getPart( $part )
    {
        return ( array_key_exists($part,$this->_parts) ) ?
            $this->_parts[$part] : null;
    }

    /**
     * Gets the where clause (an array of {@link Xyster_Data_Criterion} objects)
     *
     * @return array
     */
    public function getWhere()
    {
        return $this->getPart( self::WHERE );
    }

    /**
     * Gets whether this query has an order clause that evaluates at runtime
     *
     * @return boolean
     */
    public function hasRuntimeOrder()
    {
        return $this->_runtime[self::ORDER];
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
        Xyster_Orm_Query_Parser::assertValidColumnForClass($order->getField(), $this->_table);
        $this->_runtime[self::ORDER] |= Xyster_Orm_Query_Parser::isRuntime($order, $this->_table);
            
        $this->_parts[self::ORDER][] = $order;
        
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
        foreach( $where->getFields() as $field ) {
            if ( $field->getAggregate() ) {
                require_once 'Xyster/Orm/Query/Exception.php';
                throw new Xyster_Orm_Query_Exception('Aggregated fields are not allowed in this query');
            }
            Xyster_Orm_Query_Parser::assertValidFieldForClass($field,$this->_table);
        }
        
        if ( Xyster_Orm_Query_Parser::isRuntime($where, $this->_table) ) {
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
        $this->_parts[self::ORDER] = array();
        
        $this->_runtime[self::WHERE] = array();
        $this->_runtime[self::ORDER] = false;
        
        $this->_backend[self::WHERE] = array();
    }
    
    /**
     * Puts the entity in the secondary repository
     * 
     * @param Xyster_Orm_Entity $entity
     */
    protected function _putInSecondaryCache( Xyster_Orm_Entity $entity )
    {
        $repo = Xyster_Orm::getSecondaryCache();
        $className = get_class($entity);
        $map = Xyster_Orm_Mapper::factory($className);
        $cacheLifetime = $map->getLifetime();

        // only store the entity if it should be cached longer than the request
        // that's why we have the primary repository
        if ( $repo && $cacheLifetime > -1 ) {
            
            $repoId = array( 'Xyster_Orm', $map->getDomain(), $className );
            foreach( $entity->getPrimaryKey() as $key => $value ) {
                $repoId[] = $key . '=' . $value;
            }
            $repoId = md5(implode("/",$repoId));
            $repo->save( $repoId, $entity, null, $cacheLifetime );

        }
    }
}