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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * A component tuplizer
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Tuplizer_IComponent
{
    /**
     * Gets the actual class of the component
     * 
     * @return Xyster_Type
     */
    function getComponentType();
    
    /**
     * Get the value of a specified property from the given entity
     *
     * @param mixed $component
     * @param int $i
     * @return mixed
     */
    function getPropertyValue( $component, $i );
    
    /**
     * Get all values on the given entity (essentially, turn into assoc. array)
     *
     * @param mixed $component
     * @return array
     */
    function getPropertyValues( $component );
    
    /**
     * Create a new instance of the entity
     *
     * @return mixed
     */
    function instantiate();
    
    /**
     * Whether the supplied object is an instance of the entity supported
     *
     * @param mixed $component
     */
    function isInstance( $component );
    
    /**
     * Injects the values into the supplied component 
     *
     * @param mixed $component
     * @param array $values
     */
    function setPropertyValues( $component, array $values );
}