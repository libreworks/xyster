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
 * @see Xyster_Orm_Persister_Collection_Interface
 */
require_once 'Xyster/Orm/Persister/Collection/Interface.php';
/**
 * @see Xyster_Orm_Persister_IJoinable
 */
require_once 'Xyster/Orm/Persister/IJoinable.php';
/**
 * @see Xyster_Orm_Persister_IPropertyMapping
 */
require_once 'Xyster/Orm/Persister/IPropertyMapping.php';
/**
 * A collection that can be queried or loaded by outer join
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_Collection_IQueryable extends Xyster_Orm_Persister_Collection_Interface, Xyster_Orm_Persister_IJoinable, Xyster_Orm_Persister_IPropertyMapping
{
    /**
     * Gets the names of the collection element columns
     * 
     * @param string $alias Optional. The table alias
     * @return array
     */
    function getElementColumnNames($alias = null);
    
    /**
     * Gets the element persister if this collection contains entities
     * 
     * @return Xyster_Orm_Persister_Entity_Interface
     */
    function getElementPersister();
    
    /**
     * Gets the names of the collection index columns
     * 
     * @param string $alias Optional. The table alias
     * @return array
     */
    function getIndexColumnNames($alias = null);
    
    /**
     * Gets the order by clause for the target table of a many-to-many
     * 
     * @param string $alias Table alias
     * @return string
     */
    function getManyToManyOrderBySql($alias);
    
    /**
     * Gets the order by clause
     * 
     * @param string $alias Table alias
     * @return string
     */
    function getOrderBySql($alias);
    
    /**
     * Gets a list of collection index and element columns
     * 
     * @param string $alias
     * @param string $colSuffix
     * @return string
     */
    function getSelectColumnSql($alias, $colSuffix);
    
    /**
     * Gets whether this collection has a where clause
     * 
     * @return boolean
     */
    function hasWhere();
}