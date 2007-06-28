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
 * @see Xyster_Orm_Query
 */
require_once 'Xyster/Orm/Query.php';
/**
 * A report query object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
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
	    
	}
	
	/**
	 * Adds a projected field to this query 
	 *
	 * @param Xyster_Data_Field $field
	 * @return Xyster_Orm_Query_Report provides a fluent interface
	 */
	public function field( Xyster_Data_Field $field )
	{
	    if ( $field instanceof Xyster_Data_Field_Grouped ) {
	        return $this->group($field);
	    }
	    
	    Xyster_Orm_Query_Parser::assertValidColumnForClass($field,$this->_table);
	    $this->_runtime[self::FIELDS] |= Xyster_Orm_Query_Parser::isRuntime($field,$this->_table);
	    
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
	 * @param Xyster_Data_Field_Grouped $group
	 * @return Xyster_Orm_Query_Report provides a fluent interface
	 */
	public function group( Xyster_Data_Field_Grouped $group )
	{
	    Xyster_Orm_Query_Parser::assertValidFieldForClass($group,$this->_table);
	    $this->_runtime[self::GROUP] |= Xyster_Orm_Query_Parser::isRuntime($group,$this->_table);
	    
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
	    $fields = $having->getFields();
		foreach ( $fields as $field ) {
			Xyster_Orm_Query_Parser::assertValidFieldForClass($field,$this->_table);
			$aggs += ($field instanceof Xyster_Data_Field_Aggregate) ? 1 : 0;
		}

		if ( !$aggs || count($fields) != $aggs ) {
        	require_once 'Xyster/Orm/Query/Exception.php';
			throw new Xyster_Orm_Query_Excepton('The criterion provided must contain only aggregated fields');
		}
		
		if ( Xyster_Orm_Query_Parser::isRuntime($having,$this->_table) ) {
			$this->_runtime[self::GROUP] = true;
		}
		
		$this->_parts[self::HAVING][] = $having;
		
		return $this;
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