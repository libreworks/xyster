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
 * @see Xyster_Orm_Type_Version
 */
require_once 'Xyster/Orm/Type/Version.php';
/**
 * @see Xyster_Orm_Type_Immutable
 */
require_once 'Xyster/Orm/Type/Immutable.php';
/**
 * The int type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Integer extends Xyster_Orm_Type_Immutable implements Xyster_Orm_Type_Version 
{
    /**
     * @var Xyster_Type
     */
    static protected $_type;

    /**
     * Compare two instances of this type
     *
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare( $a, $b )
    {
        if ( $a > $b ) {
            return 1;
        } else if ( $a == $b ) {
            return 0;
        } else {
            return -1;
        }
    }
    
    /**
     * Gets a comparator for version values
     *
     * @return Xyster_Collection_Comparator_Interface
     */
    public function getComparator()
    {
        return $this;
    }
    
    /**
     * Gets the fetch type for binding
     *
     * @return int
     */
    public function getFetchType()
    {
        return Zend_Db::PARAM_INT;
    }
        
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Integer();
    }
    
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'integer';
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        if ( !self::$_type ) {
            self::$_type = new Xyster_Type('integer');
        }
        return self::$_type;
    }

    /**
     * Gets the initial version id
     *
     * @return mixed The initial version
     */
    public function initial()
    {
        return 0;
    }
        
    /**
     * Gets the next version id
     *
     * @param mixed $current
     * @return mixed The next version
     */
    public function next( $current )
    {
        return $current + 1;
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
            $stmt->bindValue($index, $value, Zend_Db::PARAM_INT);
        }
    }   
}