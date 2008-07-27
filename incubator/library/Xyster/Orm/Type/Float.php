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
 * @see Xyster_Orm_Type_Immutable
 */
require_once 'Xyster/Orm/Type/Immutable.php';
/**
 * Maps a SQL DOUBLE PRECISION (8-bytes) to a PHP float
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Float extends Xyster_Orm_Type_Immutable
{
    /**
     * @var Xyster_Type
     */
    static protected $_type;
    
    /**
     * Gets the type out of a result set statement
     *
     * @param Zend_Db_Statement_Interface $rs The statement used to fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $names The column names
     * @return float
     */
    public function get(Zend_Db_Statement_Interface $rs, array $names, $owner, Xyster_Orm_Session_Interface $sess )
    {
        $val = null;
        $rs->bindColumn($names[0], $val);
        $rs->fetch(Zend_Db::FETCH_BOUND);
        return $val;
    }
        
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Float();
    }
    
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'float';
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        if ( !self::$_type ) {
            self::$_type = new Xyster_Type('double');
        }
        return self::$_type;
    }
    
    /**
     * Sets the value to the prepared statement
     *
     * @param Zend_Db_Statement_Interface $stmt The statment to set
     * @param mixed $value The value to bind into the statement
     * @param int $index The starting index to bind
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     * @param array $settable Boolean for each column's settability
     */
    public function set(Zend_Db_Statement_Interface $stmt, $value, $index, Xyster_Orm_Session_Interface $sess, array $settable = array() )
    {
        if ( !count($settable) || $settable[0] ) {
            $stmt->bindValue($index, $value);
        }
    }
}