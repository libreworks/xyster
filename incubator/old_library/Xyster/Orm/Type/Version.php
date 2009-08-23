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
 * @see Xyster_Orm_Type_Interface
 */
require_once 'Xyster/Orm/Type/Interface.php';
/**
 * A type that can represent a version of an entity record
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Type_Version extends Xyster_Orm_Type_Interface
{
    /**
     * Gets a comparator for version values
     *
     * @return Xyster_Collection_Comparator_Interface
     */
    function getComparator();
    
    /**
     * Gets the initial version id
     *
     * @return mixed The initial version
     */
    function initial();
    
    /**
     * Gets the next version id
     *
     * @param mixed $current
     * @return mixed The next version
     */
    function next( $current );
}