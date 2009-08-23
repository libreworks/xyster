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
 * The status of an entity in the session
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Engine_Status extends Xyster_Enum
{
    const Deleted = 0;
    const Gone = 1;
    const Loading = 2;
    const Managed = 3;
    const ReadOnly = 4;
    const Saving = 5;
    
    /**
     * Entity has been deleted
     *
     * @return Xyster_Orm_Engine_Status
     */
    public static function Deleted()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Entity is gone
     *
     * @return Xyster_Orm_Engine_Status
     */
    public static function Gone()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Entity is loading
     *
     * @return Xyster_Orm_Engine_Status
     */
    public static function Loading()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Entity is now loaded and managed
     *
     * @return Xyster_Orm_Engine_Status
     */
    public static function Managed()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Entity is read-only
     *
     * @return Xyster_Orm_Engine_Status
     */
    public static function ReadOnly()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * Entity is being saved
     *
     * @return Xyster_Orm_Engine_Status
     */
    public static function Saving()
    {
        return Xyster_Enum::_factory();
    }
}