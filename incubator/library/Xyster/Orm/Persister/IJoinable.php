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
 * Persisters that can be loaded by outer join
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_IJoinable
{
    /**
     * Gets the from clause part of joins
     * 
     * @param string $alias Query alias
     * @param boolean $innerJoin
     * @param boolean $includeSubclasses
     * @return string
     */
    function getFromJoinSql($alias, $innerJoin, $includeSubclasses);
    
    /**
     * Gets the ON clause of any joins
     * 
     * @param string $alias
     * @param boolean $innerJoin
     * @param boolean $includeSubclasses
     * @return string
     */
    function getJoinOnSql($alias, $innerJoin, $includeSubclasses);
    
    /**
     * Gets the column names to use for joining
     * 
     * @return array
     */
    function getKeyColumnNames();
    
    /**
     * Gets the class name or collection role
     * 
     * @return string
     */
    function getName();
    
    /**
     * Gets the where clause for a one-to-many join
     * 
     * @param string $alias
     * @return strring
     */
    function getOneToManyWhereSql($alias);
    
    /**
     * Gets all columns to select
     * 
     * @param Xyster_Orm_Persister_IJoinable $right
     * @param string $rightAlias
     * @param string $leftAlias
     * @param string $entitySuffix
     * @param string $collectionSuffix
     * @param boolean $useCollectionCols
     * @return string
     */
    function getSelectSql(Xyster_Orm_Persister_IJoinable $right, $rightAlias, $leftAlias, $entitySuffix, $collectionSuffix, $useCollectionCols);
    
    /**
     * The table to join
     * 
     * @return string
     */
    function getTableName();
    
    /**
     * Gets the where clause
     * 
     * @param string $alias Query alias
     * @param Xyster_Collection_Map_Interface $enabledFilters
     * @return string
     */
    function getWhereSql($alias, Xyster_Collection_Map_Interface $enabledFilters);
    
    /**
     * Whether this persister is for collections
     * 
     * @return boolean
     */
    function isCollection();
}