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
 * A helper for meta information about entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity_Meta
{
    private function __construct()
    {
    }

    /**
     * Gets the fields defined for an entity
     * 
     * @param mixed $class The entity class (as a string or an instance)
     * @return array An array of {@link Xyster_Orm_Entity_Field} objects
     */
    static public function getFields( $class )
    {
        if ( is_object($class) ) {
            $class = get_class($class);
        }
        
        // the factory method checks to make sure the class is an entity
        $map = Xyster_Orm_Mapper::factory($class);
        return $map->getFields();
    }
    
	/**
	 * Gets an array of all of the class' properties, methods, and relationships
	 *
	 * @param string $className
	 * @return array
	 */
	static public function getMembers( $className )
	{
		if ( !isset(self::$_members[$className]) ) {
			$members = array('id');
			$members = array_merge($members,self::getFields($className));
			$members = array_merge($members,Xyster_Orm_Relation::getNames($className));
			$members = array_merge($members,get_class_methods($className));
			self::$_members[$className] = $members;
		}

		return self::$_members[$className];
	}
}