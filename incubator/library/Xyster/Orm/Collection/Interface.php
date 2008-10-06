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
 * Represents which direction a foreign key constraint goes
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Collection_Interface
{
    /**
     * Called after initializing from cache
     */
    function postInit();
    
    /**
     * Called after inserting a row to get the generated id
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param object $entry
     * @param int $i
     */
    function postInsert(Xyster_Orm_Persister_Collection_Interface $persister, $entry, $i);

    /**
     * Called before initializing with elements
     *  
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param int $size
     */
    function preInit(Xyster_Orm_Persister_Collection_Interface $persister, $size);
}