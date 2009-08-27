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
 * @see Xyster_Orm_Type_AbstractScalar
 */
require_once 'Xyster/Orm/Type/AbstractScalar.php';
/**
 * Base type mapping for objects which cannot be changed (string, int, etc)
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_Mutable extends Xyster_Orm_Type_AbstractScalar
{
    /**
     * Whether this type can be altered 
     *
     * @return boolean
     */
    public function isMutable()
    {
        return true;
    }
}