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
 * Represents which direction a foreign key constraint goes
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Engine_ForeignKeyDirection extends Xyster_Enum
{
    const FromParent = 0;
    const ToParent = 1;
    
    /**
     * A foreign key from parent to child
     *
     * @return Xyster_Orm_Engine_ForeignKeyDirection
     */
    public static function FromParent()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * A foreign key from child to parent
     *
     * @return Xyster_Orm_Engine_ForeignKeyDirection
     */
    public static function ToParent()
    {
        return Xyster_Enum::_factory();
    }
}