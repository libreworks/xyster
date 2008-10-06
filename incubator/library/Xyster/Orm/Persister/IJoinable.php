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
     * The table to join
     * 
     * @return string
     */
    function getTableName();
    
    /**
     * Whether this persister is for collections
     * 
     * @return boolean
     */
    function isCollection();
}