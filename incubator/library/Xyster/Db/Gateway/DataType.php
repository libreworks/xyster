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
 * SQL Data type enumerated type
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_DataType extends Xyster_Enum
{
    const Varchar   = 0;
    const Char      = 1;
    const Integer   = 2;
    const Smallint  = 3;
    const Float     = 4;
    const Timestamp = 5;
    const Date      = 6;
    const Time      = 7;
    const Clob      = 8;
    const Blob      = 9;
    const Boolean   = 10;
    const Identity  = 11;
    const Bigint    = 12;

    /**
     * The Varchar data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Varchar()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Char data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Char()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Integer data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Integer()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Smallint data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Smallint()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Float data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Float()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Timestamp data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Timestamp()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Date data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Date()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Time data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Time()
    {
       return Xyster_Enum::_factory();
    }
    

    /**
     * The Clob data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Clob()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Blob data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Blob()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Boolean data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Boolean()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Identity data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Identity()
    {
       return Xyster_Enum::_factory();
    }

    /**
     * The Bigint data type
     *
     * @return Xyster_Db_Gateway_DataType
     */
    static public function Bigint()
    {
       return Xyster_Enum::_factory();
    }
}