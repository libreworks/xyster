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
 * @see Xyster_Orm_Query
 */
require_once 'Xyster/Orm/Query.php';
/**
 * A report query object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Query_Report extends Xyster_Orm_Query
{
    const DISTINCT = 'distinct';
    const FIELDS = 'fields';
    const GROUP = 'group';
    const HAVING = 'having'; 
    
    /**
     * Turns excluding duplicate records on or off
     * 
     * @param boolean $distinct  True excludes duplicates
     */
    public function distinct( $distinct = true )
    {
        $this->_parts[self::DISTINCT] = $distinct;
        
        return $this;
    }
    
    /**
     * Executes the report query
     * 
     * If the entire query cannot be executed in the backend, this method will 
     * return full entities from the data store, put them in the cache, then do
     * any runtime calculations it needs to do.
     *
     * @return Xyster_Data_Set  The query result set
     * @throws Xyster_Orm_Query_Exception if there's a select column not aggregated or grouped
     */
    public function execute()
    {
        // make sure all fields are either grouped or aggregates
        if ( count($this->_parts[self::GROUP]) ) {
            $groups = array();
            foreach( $this->_parts[self::GROUP] as $group ) {
                $groups[] = $group->getName();
            }
            foreach( $this->_parts[self::FIELDS] as $field )
                if ((!$field instanceof Xyster_Data_Field_Aggregate) &&
                    !in_array($field->getName(), $groups)) {
                    require_once 'Xyster/Orm/Query/Exception.php';
                    throw new Xyster_Orm_Query_Exception($field->getName() . ' is not in the group clause');
                }
        }

        // run the query in the backend
        $collection = $this->_manager->executeQuery($this);
        if ( $collection instanceof Xyster_Data_Set &&
            ! $collection instanceof Xyster_Orm_Set ) {
            // if it's not an entity set, the whole thing was in the backend;
	        // it's safe to just return it
            return $collection;
        }

        // apply any runtime filters to the entity set
        if ( count($this->_runtime[Xyster_Orm_Query::WHERE]) ) {
            $collection->filter(Xyster_Data_Criterion::fromArray('AND', $this->_runtime[Xyster_Orm_Query::WHERE]));
        }

        $fieldsAndGroups = array_merge($this->_parts[self::GROUP], $this->_parts[self::FIELDS]);
        
        // setup the Xyster_Data_Set and add the columns to return
        $rs = new Xyster_Data_Set();
        foreach( $fieldsAndGroups  as $field ) {
            $rs->addColumn( $field->getAlias() );
        }

        if ( $this->_parts[self::GROUP] ) {
            
            require_once 'Xyster/Data/Tuple.php';
            // let Xyster_Data_Tuple do the work for runtime grouping
            Xyster_Data_Tuple::makeTuples(
                $rs,
                $collection,
                $fieldsAndGroups,
                $this->_parts[self::HAVING],
                $this->_parts[Xyster_Orm_Query::LIMIT],
                $this->_parts[Xyster_Orm_Query::OFFSET]
                );
                
        } else {
            
            // check to see if the fields contain an aggregate
            $aggregate = false;
            foreach( $this->_parts[self::FIELDS] as $field ) {
                if ( $field instanceof Xyster_Data_Field_Aggregate ) {
                    $aggregate = $field->getFunction();
                    break;
                }
            }
            if ( $aggregate ) {
                // just one row that contains aggregates
                require_once 'Xyster/Data/Tuple.php';
                $tuple = new Xyster_Data_Tuple(array(), $collection);
                $rs->add($tuple->toRow($this->_parts[self::FIELDS]));
            } else {
                // add the values to the set (enforcing limit & offset)
                foreach( $collection as $offset=>$entity ) {
                    if ( $offset >= $this->_parts[Xyster_Orm_Query::OFFSET] ) {
                        $values = array();
                        foreach( $this->_parts[self::FIELDS] as $field ) {
                            $values[$field->getAlias()] = $field->evaluate($entity);
                        }
                        $rs->add( $values );
                        if ( $this->_parts[Xyster_Orm_Query::LIMIT] > 0 &&
                            count($rs) == $this->_parts[Xyster_Orm_Query::LIMIT] ) {
                            break;
                        }
                    }
                }
            }
        }

        // apply any runtime sort order to the entity set
        if ( $this->_runtime[Xyster_Orm_Query::ORDER] ) {
            $sorts = array();
            foreach( $this->_parts[Xyster_Orm_Query::ORDER] as $sort ) {
                foreach( $this->_parts[self::FIELDS] as $field ) {
                    // make sure the sort field is actually in those returned
                    if ( $sort->getField()->getName() == $field->getName() ) {
                        $sorts[] = $sort;
                        break;
                    }
                }
            }
            require_once 'Xyster/Data/Comparator.php';
            $rs->sort(new Xyster_Data_Comparator($sorts));
        }

        return $rs;
    }
    
    /**
     * Adds a projected field to this query 
     *
     * @param Xyster_Data_Field $field
     * @return Xyster_Orm_Query_Report provides a fluent interface
     */
    public function field( Xyster_Data_Field $field )
    {
        if ( $field instanceof Xyster_Data_Field_Group ) {
            return $this->group($field);
        }
        
        $this->_parser->assertValidFieldForClass($field, $this->_class);
        $this->_runtime[self::FIELDS] |= $this->_parser->isRuntime($field, $this->_class);
        $this->_parts[self::FIELDS][] = $field;
        
        return $this;
    }
    
    /**
     * Gets the non-group {@link Xyster_Data_Field} objects added to the statement
     * 
     * @return array
     */
    public function getFields()
    {
        return $this->getPart(self::FIELDS);
    }

    /**
     * Gets the grouped {@link Xyster_Data_Field} objects added to the statement
     * 
     * @return array
     */
    public function getGroup()
    {
        return $this->getPart(self::GROUP);
    }

    /**
     * Gets the {@link Xyster_Data_Aggregate} {@link Xyster_Data_Criterion} objects added to the statement
     * 
     * @return array
     */
    public function getHaving()
    {
        return $this->getPart(self::HAVING);
    }
    
    /**
     * Adds grouped field to this report query 
     *
     * @param Xyster_Data_Field_Group $group
     * @return Xyster_Orm_Query_Report provides a fluent interface
     */
    public function group( Xyster_Data_Field_Group $group )
    {
        $this->_parser->assertValidFieldForClass($group, $this->_class);
        $this->_runtime[self::GROUP] |= $this->_parser->isRuntime($group, $this->_class);
        
        $this->_parts[self::GROUP][] = $group;
        
        return $this;
    }
    
    /**
     * Adds a group-criterion to this query
     * 
     * All fields in the criterion must be instances of
     * {@link Xyster_Data_Field_Aggregate}, as a "having" clause is applied to
     * groups.
     *
     * @param Xyster_Data_Criterion $having
     * @return Xyster_Orm_Query_Report provides a fluent interface
     * @throws Xyster_Orm_Query_Exception if not all fields are aggregated
     */
    public function having( Xyster_Data_Criterion $having )
    {
        $aggs = 0;
        $fields = Xyster_Data_Criterion::getFields($having);
        foreach ( $fields as $field ) {
            $this->_parser->assertValidFieldForClass($field, $this->_class);
            $aggs += ($field instanceof Xyster_Data_Field_Aggregate) ? 1 : 0;
        }

        if ( !$aggs || count($fields) != $aggs ) {
            require_once 'Xyster/Orm/Query/Exception.php';
            throw new Xyster_Orm_Query_Exception('The criterion provided must contain only aggregated fields');
        }
        
        if ( $this->_parser->isRuntime($having, $this->_class) ) {
            $this->_runtime[self::GROUP] = true;
        }
        
        $this->_parts[self::HAVING][] = $having;
        
        return $this;
    }
    
    /**
     * Gets whether or not this report query has fields that evaluate at runtime
     *
     * @return boolean
     */
    public function hasRuntimeFields()
    {
        return (bool) $this->_runtime[self::FIELDS];
    }
    
    /**
     * Gets whether or not this report query has groups that evaluate at runtime
     * 
     * @return boolean
     */
    public function hasRuntimeGroup()
    {
        return (bool) $this->_runtime[self::GROUP];
    }
    
    /**
     * Gets whether or not to exclude duplicate results
     *
     * @return boolean
     */
    public function isDistinct()
    {
        return $this->getPart(self::DISTINCT);
    }
    
    /**
     * Gets whether this query has parts that are evaluated at runtime
     *
     * @return boolean
     */
    public function isRuntime()
    {
        return parent::isRuntime() || $this->hasRuntimeGroup() ||
            $this->hasRuntimeFields();
    }
    
    /**
     * Inits the parts of the query  
     *
     */
    protected function _initParts()
    {
        parent::_initParts();
        
        $this->_parts[self::DISTINCT] = false;
        $this->_parts[self::FIELDS] = array();
        $this->_parts[self::GROUP] = array();
        $this->_parts[self::HAVING] = array();
        
        $this->_runtime[self::FIELDS] = false;
        $this->_runtime[self::GROUP] = false;
    }
}