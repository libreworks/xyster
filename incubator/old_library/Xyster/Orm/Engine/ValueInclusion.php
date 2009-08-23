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
 * @see Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Distinguishes unsaved identifier values from persisted ones
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Engine_ValueInclusion extends Xyster_Enum
{
    const None = 0;
    const Partial = 1;
    const Full = 2;
    
    /**
     * No value inclusion
     *
     * @return Xyster_Orm_Engine_ValueInclusion
     */
    public static function None()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Partial value inclusion
     *
     * @return Xyster_Orm_Engine_ValueInclusion
     */
    public static function Partial()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Full value inclusion
     *
     * @return Xyster_Orm_Engine_ValueInclusion
     */
    public static function Full()
    {
        return Xyster_Enum::_factory();
    }
}