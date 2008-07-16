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
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * SQL referential action enumerated type
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_ReferentialAction extends Xyster_Enum
{
    const Cascade    = 0;
    const Restrict   = 1;
    const NoAction   = 2;
    const SetNull    = 3;
    const SetDefault = 4;

    protected static $_sql = array(
            0 => 'CASCADE',
            1 => 'RESTRICT',
            2 => 'NO ACTION',
            3 => 'SET NULL',
            4 => 'SET DEFAULT'
        );
    
    /**
     * Gets the SQL syntax for the action
     *
     * @return string
     */
    public function getSql()
    {
        return self::$_sql[$this->getValue()];
    }

    /**
     * Gets the correct enum for the SQL syntax
     *
     * @param string $sql
     * @return Xyster_Db_ReferentialAction
     */
    static public function fromSql( $sql )
    {
        $method = str_replace(' ', '', ucwords(strtolower($sql)));
        return Xyster_Enum::parse(__CLASS__, $method);
    }
    
    /**
     * The CASCADE action
     *
     * @return Xyster_Db_ReferentialAction
     */
    static public function Cascade()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * The RESTRICT action
     *
     * @return Xyster_Db_ReferentialAction
     */
    static public function Restrict()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * The NO ACTION action
     *
     * @return Xyster_Db_ReferentialAction
     */
    static public function NoAction()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * The SET NULL action
     *
     * @return Xyster_Db_ReferentialAction
     */
    static public function SetNull()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * The SET DEFAULT action
     *
     * @return Xyster_Db_ReferentialAction
     */
    static public function SetDefault()
    {
       return Xyster_Enum::_factory();
    }
}