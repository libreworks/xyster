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
 * Userland-accessible runtime metadata for a collection
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Meta_Collection
{
    /**
     * Gets the type of element in the collection
     *
     * @return Xyster_Orm_Type_Interface
     */
    function getElementType();
    
    /**
     * Gets the type of the index in the collection (or null if n/a)
     *
     * @return Xyster_Orm_Type_Interface
     */
    function getIndexType();
    
    /**
     * Gets the type of key in the collection (or null if n/a)
     *
     * @return Xyster_Orm_Type_Interface
     */
    function getKeyType();
    
    /**
     * Gets the name of the collection role
     *
     * @return string
     */
    function getRole();
    
    /**
     * Whether the collection has an index
     *
     * @return boolean
     */
    function hasIndex();
    
    /**
     * Whether the collection is lazy loaded
     *
     * @return boolean
     */
    function isLazy();
}