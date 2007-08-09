<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * An field description object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity_Field
{
    protected $_name;
    protected $_original;
    protected $_type;
    protected $_length;
    protected $_default;
    protected $_null;
    protected $_primary;
    protected $_primaryPosition;
    protected $_identity;

    /**
     * Create a new entity field
     * 
     * The array passed should be in the format returned by the describeTable
     * method of the Zend_Db_Adapter_Abstract class
     * 
     * @see Zend_Db_Adapter_Abstract::describeTable
     * @param array $details
     */
    public function __construct( $name, array $details )
    {
        $this->_name = $name;
        $this->_original = $details['COLUMN_NAME'];
        $this->_type = $details['DATA_TYPE'];
        $this->_length = $details['LENGTH'];
        $this->_null = $details['NULLABLE'];
        $this->_default = $details['DEFAULT'];
        $this->_primary = $details['PRIMARY'];
        $this->_primaryPosition = $details['PRIMARY_POSITION'];
        $this->_identity = (isset($details['IDENTITY'])) ? $details['IDENTITY'] : null;
    }

    /**
     * Gets the name of the field
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * Gets the original name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->_original;
    }
    /**
     * Gets the native type of the field
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    /**
     * Gets the length of the field
     *
     * @return int
     */
    public function getLength()
    {
        return $this->_length;
    }
    /**
     * Gets whether the field can have a null value
     *
     * @return boolean
     */
    public function isNullable()
    {
        return $this->_null;
    }
    /**
     * Gets the default value for the field
     *
     * @return boolean
     */
    public function getDefault()
    {
        return $this->_default;
    }
    /**
     * Gets whether the field is part of a primary key
     *
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->_primary;
    }
    /**
     * Gets the index of this field in the primary key
     *
     * @return int
     */
    public function getPrimaryPosition()
    {
        return $this->_primaryPosition;
    }
    /**
     * Gets whether the field uses an autonumbering scheme
     *
     * @return boolean
     */
    public function isIdentity()
    {
        return $this->_identity;
    }
}