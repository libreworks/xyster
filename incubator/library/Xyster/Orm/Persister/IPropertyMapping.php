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
 * For mappings that define properties: entities and collection elements 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_IPropertyMapping
{
    /**
     * Get the type of the thing containing the properties
     * 
     * @return Xyster_Orm_Type_Interface
     */
    function getType();
    
    /**
     * Get the column names for the given property path (and optionally alias)
     * 
     * @param string $propertyName
     * @param string $alias
     * @return array
     */
    function toColumns($propertyName, $alias=null);
    
    /**
     * Gets the type of the property specified
     * 
     * @param string $propertyName
     * @return Xyster_Orm_Type_Interface
     */
    function toType($propertyName);
}