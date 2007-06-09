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
 * The abstract backend for Xyster_Orm_Mapper
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Backend_Abstract
{
	/**
	 * The mapper passed in
	 *
	 * @var Xyster_Orm_Mapper
	 */
	protected $_mapper;

	/**
	 * Creates a new orm backend object
	 *
	 * @param Xyster_Orm_Mapper $mapper  The mapper the backend is supporting
	 */
	final public function __construct( Xyster_Orm_Mapper $mapper )
	{
		$this->_mapper = $mapper;
		$entityName = $this->_mapper->getEntityName();
		if ( !class_exists($entityName,false) ) {
		    Xyster_Orm::loadClass($entityName);
		}
	}

    /**
	 * Gets the fields for an entity as they appear in the backend
	 * 
	 * The array should come in the format of the describeTable method of the 
	 * Zend_Db_Adapter_Abstract class.
	 * 
	 * @see Zend_Db_Adapter_Abstract::describeTable
	 * @return array
	 */
	abstract public function getFields();
	/**
	 * Returns one entity found by primary key
	 * 
	 * Criteria will either be a scalar value, in which case, an entity should 
	 * be found directly (ID=?), or it will be an array containing column names
	 * as keys and their expected values (VAL1=?,VAL2=?).
	 *
	 * @param mixed $id Primary key or keys
	 * @return Xyster_Orm_Entity The entity found
	 */
	abstract public function findByPrimary( $id );
	/**
	 * Returns one entity found by criteria 
	 * 
	 * If the value for a column is an array, the value should be any of the
	 * values in the array (VAL1 IN ?,?,? ).
	 * 
	 * @param mixed $criteria Array of Criteria, a {@link Xyster_Data_Criterion}
	 * @return Xyster_Orm_Entity The first entity found
	 */
	abstract public function findByCriteria( $criteria );
    /**
     * Returns a collection of entities found by primary keys
     * 
     * @param array $ids The ids to retrieve
     * @return Xyster_Orm_Set The entities found
     */	
	abstract public function findManyByPrimary( array $ids );
	/**
	 * Returns a collection of entities found by criteria
	 * 
	 * @param mixed $criteria Array of Criteria, a {@link Xyster_Data_Criterion}, or null
	 * @param mixed $sort Array of Xyster_Data_Sort objects, a {@link Xyster_Data_Sort}, or null
	 * @return Xyster_Orm_Set The entities found
	 */
	abstract public function findManyByCriteria( $criteria = null, $sort = null );
	/**
	 * Reloads an entity's values with fresh ones from the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to refresh
	 */
	abstract public function refresh ( Xyster_Orm_Entity $entity );
	/**
	 * Saves a new entity into the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to insert
	 * @return mixed  The new primary key
	 */
	abstract public function insert( Xyster_Orm_Entity $entity );
	/**
	 * Updates the values of an entity in the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to update
	 */
	abstract public function update( Xyster_Orm_Entity $entity );
	/**
	 * Removes entities from the backend
	 *
	 * @param Xyster_Data_Criterion $where  The criteria on which to remove entities
	 */
	abstract public function delete( Xyster_Data_Criterion $where );

	/**
	 * Asserts the correct property names in a criteria array
	 *
	 * @param array $criteria
	 * @throws Xyster_Orm_Backend_Exception if one of the field names is invalid
	 */
	protected function _checkPropertyNames( array $criteria )
	{
	    // get the array of Xyster_Orm_Entity_Field objects
		$fields = $this->_mapper->getFields();
		
		foreach( $criteria as $k => $v ) { 
			if ( !array_key_exists($k,$fields) ) {
				require_once 'Xyster/Orm/Backend/Exception.php';
				throw new Xyster_Orm_Backend_Exception("'" . $k
				    . "' is not a valid field for "
					. $this->_mapper->getEntityName() );
			}
		}
	}
	/**
	 * Creates an entity from the row supplied and store it in the map
	 *
	 * @param array $row
	 * @return Xyster_Orm_Entity  The entity created
	 */
	protected function _create( $row )
	{
		$entityName = $this->_mapper->getEntityName();
		// this class should already be loaded when the constructor fires
		return new $entityName($row);
	}
}