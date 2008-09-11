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
 * @see Xyster_Orm_Tuplizer_Interface
 */
require_once 'Xyster/Orm/Tuplizer/Interface.php';
/**
 * @see Xyster_Orm_Session_Interface
 */
require_once 'Xyster/Orm/Session/Interface.php';
/**
 * A tuplizer for mapping entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Tuplizer_Entity_Interface extends Xyster_Orm_Tuplizer_Interface
{
    /**
     * Create a proxy for the entity 
     *
     * @param mixed $id
     * @param Xyster_Orm_Session_Interface $sess
     * @return object
     */
    function createProxy( $id, Xyster_Orm_Session_Interface $sess );
    
    /**
     * Gets the identifier value from an entity
     *
     * @param mixed $entity
     * @return mixed
     */
    function getIdentifier( $entity );
    
    // @todo figure out how getPropertyValuesToInsert is used
    // function getPropertyValuesToInsert( $entity, Xyster_Collection_Map_Interface $map, Xyster_Orm_Session_Interface $sess );
    
    /**
     * Gets the value of the version property
     *
     * @param object $entity
     * @return mixed
     */
    function getVersion( $entity );
    
    /**
     * Whether the entity can be proxied
     *
     * @return boolean
     */
    function hasProxy();
    
    /**
     * Create an instanceof the entity type with the given id
     *
     * @param mixed $id
     * @return mixed
     */
    function instantiateWithId( $id );
    
    /**
     * Sets the identifier property into an entity
     *
     * @param mixed $entity
     * @param mixed $id
     */
    function setIdentifier( $entity, $id );
    
    /**
     * Sets the property value
     * 
     * $name can be either the name of the property or an integer denoting its
     * location.
     *
     * @param mixed $entity
     * @param mixed $name Either property name or property index
     * @param mixed $value The new value
     */
    function setPropertyValue( $entity, $name, $value );
}