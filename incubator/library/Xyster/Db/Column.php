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
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * A database table column
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Column extends Xyster_Data_Field
{
    protected $_defaultValue;
    protected $_length;
    protected $_name;
    protected $_nullable = true;
    protected $_precision;
    protected $_scale;
    protected $_type;
    protected $_unique;
    
    /**
     * Creates a new column information object
     *
     * @param string $name Optional. The column name
     */
    public function __construct( $name = null )
    {
        if ( $name !== null ) {
            $this->_name = $name;
        }
    }
    
    /**
     * Gets whether the object is equal to this one
     *
     * @param mixed $object
     * @return boolean
     */
    public function equals( $object )
    {
        return $object === $this || ($object instanceof Xyster_Db_Column && 
            !strcasecmp($this->_name, $object->_name));
    }
    
    /**
     * Gets the default value 
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }
    
    /**
     * Gets the length of the column
     *
     * @return int
     */
    public function getLength()
    {
        return $this->_length;
    }
    
    /**
     * Gets the precision of the column
     *
     * @return int
     */
    public function getPrecision()
    {
        return $this->_precision;
    }

    /**
     * Gets the scale of the column
     *
     * @return int
     */
    public function getScale()
    {
        return $this->_scale;
    }
    
    /**
     * Gets the data type of the column
     *
     * @return Xyster_Db_DataType
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Gets a hash code for this column
     *
     * @return int
     */
    public function hashCode()
    {
        return Xyster_Type::hash(strtolower($this->_name));
    }
    
    /**
     * Gets whether the column is nullable
     *
     * @return boolean
     */
    public function isNullable()
    {
        return $this->_nullable;
    }
    
    /**
     * Gets whether the column is unique
     *
     * @return boolean
     */
    public function isUnique()
    {
        return $this->_unique;
    }
    
    /**
     * Sets the default value for the column
     *
     * @param mixed $value The new default value
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setDefaultValue( $value )
    {
        $this->_defaultValue = $value;
        return $this;
    }
    
    /**
     * Sets the length of the column
     *
     * @param int $value The column length
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setLength( $value )
    {
        $this->_length = (int)$value;
        return $this;
    }

    /**
     * Sets the column name
     *
     * @param string $value The name
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setName( $value )
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * Sets whether the column is nullable
     *
     * @param boolean $value Whether the column is nullable
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setNullable( $value = true )
    {
        $this->_nullable = (bool)$value;
        return $this;
    }
    
    /**
     * Sets the precision of the column
     *
     * @param int $value The precision
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setPrecision( $value )
    {
        $this->_precision = (int)$value;
        return $this;
    }
    
    /**
     * Sets the scale of the column
     *
     * @param int $value The scale
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setScale( $value )
    {
        $this->_scale = (int)$value;
        return $this;
    }

    /**
     * Sets the column's data type
     *
     * @param Xyster_Db_DataType $type The data type
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setType( Xyster_Db_DataType $type )
    {
        $this->_type = $type;
        return $this;
    }
    
    /**
     * Sets the column to be uniquely constrained
     *
     * @param boolean $value Whether the column is unique
     * @return Xyster_Db_Column provides a fluent interface
     */
    public function setUnique( $value = true )
    {
        $this->_unique = (bool)$value;
        return $this;
    }
    
    /**
     * Gets the string representation of the object
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->_name;
    }
}