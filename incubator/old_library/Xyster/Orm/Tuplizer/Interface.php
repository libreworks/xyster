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
 * A tuplizer manages how to get/set and create a type of data
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Tuplizer_Interface
{
    /**
     * Return the class managed by this tuplizer
     *
     * @return Xyster_Type
     */
    function getMappedType();
    
    /**
     * Get the value of a specified property from the given entity
     *
     * @param mixed $entity
     * @param int $i
     * @return mixed
     */
    function getPropertyValue( $entity, $i );
    
    /**
     * Get all values on the given entity (essentially, turn into assoc. array)
     *
     * @param mixed $entity
     * @return array
     */
    function getPropertyValues( $entity );
    
    /**
     * Create a new instance of the entity
     *
     * @return mixed
     */
    function instantiate();
    
    /**
     * Whether the supplied object is an instance of the entity supported
     *
     * @param mixed $entity
     */
    function isInstance( $entity );
        
    /**
     * Injects the values into the supplied entity 
     *
     * @param mixed $entity
     * @param array $values
     */
    function setPropertyValues( $entity, array $values );
}