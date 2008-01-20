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
 * Provides the ability to create mappers
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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
     * The factory should NOT call the manager's setMapperFactory method.  It is
     * usually in response to this method that setManager is called.
     *
     * @param Xyster_Orm_Manager $manager
     */
    function setManager( Xyster_Orm_Manager $manager );
}