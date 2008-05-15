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
 * An interface for property lookups
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Entity_Lookup_Interface
{
    /**
     * Gets the entity type supported by this lookup
     *
     * @return Xyster_Orm_Entity_Type
     */
    function getEntityType();
    
    /**
     * Gets the field name of this lookup on the entity class 
     *
     * @return string The field name
     */
    function getName();
    
    /**
     * Gets the type of object or value returned by this lookup
     *
     * @return Xyster_Type The value type
     */
    function getType();
    
    /**
     * Gets the lookup value for the entity given
     *
     * @param Xyster_Orm_Entity $entity
     * @return mixed The lookup value or object
     */
    function get( Xyster_Orm_Entity $entity );
    
    /**
     * Sets any fields affected by changing the value of this lookup
     * 
     * @param Xyster_Orm_Entity $entity
     * @param mixed $value The new value for the lookup
     */
    function set( Xyster_Orm_Entity $entity, $value );
}