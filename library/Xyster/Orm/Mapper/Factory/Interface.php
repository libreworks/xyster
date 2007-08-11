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
 * Provides the ability to create mappers
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Mapper_Factory_Interface
{
    /**
     * Gets the mapper for a given class
     * 
     * @param string $className The name of the entity class
     * @return Xyster_Orm_Mapper_Interface The mapper object
     */
    function get( $className );
    /**
     * A convenience method to get the entity meta for a given class
     *
     * @param string $className The name of the entity class
     * @return Xyster_Orm_Entity_Meta
     */
    function getEntityMeta( $className );
    /**
     * Gets the manager that contains the factory
     *
     * @return Xyster_Orm_Manager
     */
    function getManager();
    /**
     * Sets the manager that contains the factory
     *
     * @param Xyster_Orm_Manager $manager
     */
    function setManager( Xyster_Orm_Manager $manager );
}