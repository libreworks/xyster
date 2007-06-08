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
 * The main front-end for the ORM package
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm
{
   	/**
	 * Setting for entity cache lifetime in seconds
	 * 
	 * It is set to 180 (3 minutes) by default
	 *
	 * @var int
	 */
	static private $_lifetime = 180;
	
	/**
	 * The singleton instance of this class
	 * 
	 * @var Xyster_Orm
	 */
	static protected $_instance;
	
	/**
	 * The entity repository (identity map)
	 *
	 * @var Xyster_Orm_Repository
	 */
	protected $_repository;

	/**
	 * Creates a new Xyster_Orm object
	 */
	protected function __construct()
	{
	    $this->_repository = new Xyster_Orm_Repository();
	}

    /**
     * Gets an instance of Xyster_Orm
     * 
     * @return Xyster_Orm
     */	
	static public function getInstance()
	{
	    if (! self::$_instance instanceof self ) {
	        self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * Gets the time in seconds an entity should by cached
	 *
	 * @return int
	 */
	static public function getLifetime()
	{
		return self::$_lifetime;
	}
	/**
	 * Sets the time in seconds an entity should be cached
	 * 
	 * @param int $seconds
	 */
	static public function setLifetime( $seconds )
	{
		self::$_lifetime = intval($seconds);
	}

	public function commit()
	{
	    
	}
	public function delete( Xyster_Orm_Entity $entity )
	{
	    
	}
	public function destroy()
	{
	    
	}
	/**
	 * Gets an entity by class and primary key
	 * 
	 * @param string $className
	 * @param mixed $id
	 * @return Xyster_Orm_Entity
	 */
	public function get( $className, $id )
	{
	    if ( $entity = $this->getRepository()->get($className,$id) ) {
			return $entity;
		} else {
			$map = $this->getMapper($className);
			if ( $entity = $map->get($id) ) {
				$this->getRepository()->add($entity);
				return $entity;
			}
		}
		return null;
	}
	/**
	 * Gets all entities from the data source or a subset if given the keys
	 *
	 * @param string $className
	 * @param array $ids
	 * @return Xyster_Orm_Set
	 */
	public function getAll( $className, array $ids = null )
	{
	    $all = null;
	    $map = $this->getMapper($className);
	    
	    if ( is_array($ids) && count($ids) ) {
	        // we're getting a few entities by primary key

			if ( $this->_repository->hasAll($className) ) {
				$all = $map->getSet();
				foreach( $ids as $id ) {
				    $entity = $this->_repository->get($className,$id);
				    if ( $entity ) {
					    $all->add( $entity );
				    }
				}
			} else {
				$all = $map->getAll($ids);
				$this->_repository->addAll($all);
			}
			
		} else {
		    // we're getting ALL entities from the source

			if ( $this->_repository->hasAll($className) ) {
				$all = $this->_repository->getAll($className);
				if ( is_array($all) ) {
					$all = $map->getSet( Xyster_Collection::using($all) );
				}
			} else {
				$all = $map->getAll();
				$this->_repository->addAll($all);
				$this->_repository->setHasAll($className,true);
			}
			
		}
		
		return $all;
	}
	/**
	 * Gets the first entity found matching a set of criteria
	 *
	 * @param string $className
	 * @param array $criteria
	 * @return Xyster_Orm_Entity The entity found or null if none
	 */
	public function find( $className, array $criteria )
	{
	    if ( $entity = $this->_repository->find($className,$criteria) ) {
	        
			return $entity;
			
		} else {
		    
			$map = $this->getMapper($className);
			$entity = $map->find($criteria);
			if ( $entity ) {
    			$this->_repository->add($entity);
			}
			return $entity;
		}
	}
	/**
	 * Finds all entities matching a given criteria
	 *
	 * @param string $className
	 * @param mixed $criteria {@link Xyster_Data_Criterion} or associative array 
	 * @param mixed $sorts Array of {@link Xyster_Data_Sort} objects
	 */
	public function findAll( $className, $criteria, $sorts = null )
	{
	    $all = $this->getMapper($className)->findAll($criteria,$sorts);
		if ( count($all) ) {
			$this->_repository->addAll($all);
		}
		return $all;
	}
	/**
	 * Gets the entity repository in use by the ORM session
	 *
	 * @return Xyster_Orm_Repository
	 */
	public function getRepository()
	{
	    return $this->_repository;
	}
	/**
	 * Gets a data mapper for a given entity type
	 *
	 * @param string $className  The type of entity mapper to return
	 * @return Xyster_Orm_Mapper
	 */
	public function getMapper( $className )
	{
		return Xyster_Orm_Mapper::factory($className);
	}
	/**
	 * Refreshes the values of an entity 
	 */
	public function refresh( Xyster_Orm_Entity $entity )
	{
	    $this->getMapper(get_class($entity))->refresh($entity);
	}
	
	public function rollBack()
	{
	    
	}
	public function persist( Xyster_Orm_Entity $entity )
	{
	    
	}
	public function update( Xyster_Orm_Entity $entity )
	{
	    
	}
}