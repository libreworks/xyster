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
 * @see Xyster_Orm_Type_IType
 */
require_once 'Xyster/Orm/Type/IType.php';
/**
 * A type that can represent a version of an entity record
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Type_IVersion extends Xyster_Orm_Type_IType
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