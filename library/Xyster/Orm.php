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
	 * Setting for entity timeout in seconds
	 * 
	 * It is set to 180 (3 minutes) by default
	 *
	 * @var int
	 */
	static private $_timeout = 180;

	public function __construct()
	{
	    
	}
	
	/**
	 * Gets the time in seconds an entity should by cached
	 *
	 * @return int
	 */
	static public function getTimeout()
	{
		return self::$_timeout;
	}
	/**
	 * Sets the time in seconds an entity should be cached
	 * 
	 * @param int $seconds
	 */
	static public function setTimeout( $seconds )
	{
		self::$_timeout = intval($seconds);
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
	public function get( $className, $id )
	{
	    
	}
	public function getAll( $className, array $ids = null )
	{
	    
	}
	public function find( $className, $criteria )
	{
	    
	}
	public function findAll( $className, $criteria )
	{
	    
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