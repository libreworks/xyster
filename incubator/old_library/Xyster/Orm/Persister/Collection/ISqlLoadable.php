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
 * @see Xyster_Orm_Persister_Collection_IQueryable
 */
require_once 'Xyster/Orm/Persister/Collection/IQueryable.php';
/**
 * A collection that can be queried or loaded by outer join
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_Collection_ISqlLoadable extends Xyster_Orm_Persister_Collection_IQueryable 
{
    /**
     * Gets the column aliases for the collection properties
     * 
     * @param string $name
     * @param string $string
     * @return array
     */
    function getCollectionPropertyColumnAliases($name, $string);
    
    /**
     * Gets the identifier column name
     *  
     * @return string
     */
    function getIdColumnName();
}