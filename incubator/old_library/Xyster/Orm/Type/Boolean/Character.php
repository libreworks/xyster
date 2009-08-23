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
 * @see Xyster_Orm_Type_Boolean
 */
require_once 'Xyster/Orm/Type/Boolean.php';
/**
 * The boolean type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_Boolean_Character extends Xyster_Orm_Type_Boolean
{
    /**
     * Gets the type out of a result set statement
     *
     * @param array $values The values returned from the result fetch
     * @param object $owner The owning entity
     * @param Xyster_Orm_Session_Interface $sess The ORM session
     */
    public function get(array $values, $owner, Xyster_Orm_Session_Interface $sess )
    {
        $code = trim($values[0]);
        if ( $code == null || !strlen($code) ) {
            return null;
        } else {
            return !strcasecmp($code, $this->_getTrueString());
        }
    }
    
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Char();
    }

    /**
     * Whether this type needs to have {@link get}() called
     *
     * @return boolean
     */
    public function hasResolve()
    {
        return true;
    }
    
    /**
     * Returns the string representing a false value.
     *
     * @return string
     */
    protected abstract function _getFalseString();

    /**
     * Returns the string representing a true value.
     *
     * @return string
     */
    protected abstract function _getTrueString();

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
            $stmt->bindValue($index, $this->_toCharacter($value),
                Zend_Db::PARAM_STR);
        }
    }

    /**
     * Gets the right string for the boolean value
     * 
     * @param boolean $value
     * @return string
     */
    private function _toCharacter( $value )
    {
        return $value ? $this->_getTrueString() : $this->_getFalseString();
    }
}