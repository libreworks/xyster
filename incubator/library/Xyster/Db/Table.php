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
 * @see Xyster_Db_ColumnOwner
 */
require_once 'Xyster/Db/ColumnOwner.php';
/**
 * @see Xyster_Db_PrimaryKey
 */
require_once 'Xyster/Db/PrimaryKey.php';
/**
 * @see Xyster_Db_ForeignKey
 */
require_once 'Xyster/Db/ForeignKey.php';
/**
 * @see Xyster_Db_UniqueKey
 */
require_once 'Xyster/Db/UniqueKey.php';
/**
 * @see Xyster_Db_Index
 */
require_once 'Xyster/Db/Index.php';
/**
 * A table in a relational database
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Table extends Xyster_Db_ColumnOwner
{
    protected $_foreign = array();
    
    protected $_indexes = array();
    
    protected $_options = array();
    
    protected $_pkey;
    
    protected $_schema;
    
    protected $_uniques = array();
    
    /**
     * Creates a new table
     *
     * @param string $name Optional, the name of the table
     */
    public function __construct( $name = null )
    {
        if ( $name !== null ) {
            $this->setName($name);
        }
    }
    
    /**
     * Adds the index to the table
     *
     * @param Xyster_Db_Index $index
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function addIndex( Xyster_Db_Index $index )
    {
        if ( array_key_exists($index->getName(), $this->_indexes) ) {
            require_once 'Xyster/Db/Exception.php';
            throw new Xyster_Db_Exception('Index already exists: ' . $index->getName());
        }
        $this->_indexes[$index->getName()] = $index;
        return $this;
    }
    
    /**
     * Adds a foreign key to the table
     *
     * @param Xyster_Db_ForeignKey $fk
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function addForeignKey( Xyster_Db_ForeignKey $fk )
    {
        if ( array_key_exists($fk->getName(), $this->_foreign) ) {
            require_once 'Xyster/Db/Exception.php';
            throw new Xyster_Db_Exception('Foreign key already exists: ' . $fk->getName());
        }
        $this->_foreign[$fk->getName()] = $fk;
        return $this;
    }
    
    /**
     * Adds a unique key to the table
     *
     * @param Xyster_Db_UniqueKey $uk
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function addUniqueKey( Xyster_Db_UniqueKey $uk )
    {
        if ( array_key_exists($uk->getName(), $this->_uniques) ) {
            require_once 'Xyster/Db/Exception.php';
            throw new Xyster_Db_Exception('Unique key already exists: ' . $uk->getName());
        }
        $this->_uniques[$uk->getName()] = $uk;
        return $this;
    }
    
    /**
     * Gets the index by name or null
     *
     * @param string $name
     * @return Xyster_Db_Index 
     */
    public function getIndex( $name )
    {
        return array_key_exists($name, $this->_indexes) ? 
            $this->_indexes[$name] : null;
    }
    
    /**
     * Gets the foreign key by name or null
     *
     * @param string $name
     * @return Xyster_Db_ForeignKey
     */
    public function getForeignKey( $name )
    {
        return array_key_exists($name, $this->_foreign) ?
            $this->_foreign[$name] : null;
    }
    
    /**
     * Gets an array of the foreign keys
     *
     * @return array of {@link Xyster_Db_ForeignKey} objects
     */
    public function getForeignKeys()
    {
        return array() + $this->_foreign;
    }

    /**
     * Gets an array of the indexes
     *
     * @return array of {@link Xyster_Db_Index} objects
     */
    public function getIndexes()
    {
        return array() + $this->_indexes;
    }
    
    /**
     * Gets the option value or null if not found
     *
     * @param string $name The option name
     */
    public function getOption( $name )
    {
        return array_key_exists($name, $this->_options) ?
            $this->_options[$name] : null;
    }
    
    /**
     * Gets the options
     *
     * @return array
     */
    public function getOptions()
    {
        return array() + $this->_options;
    }
    
    /**
     * Gets the primary key or null if none
     *
     * @return Xyster_Db_PrimaryKey
     */
    public function getPrimaryKey()
    {
        return $this->_pkey;
    }
    
    /**
     * Gets the schema to which the table belongs
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Gets the unique key by name or null
     *
     * @param string $name
     * @return Xyster_Db_UniqueKey
     */
    public function getUniqueKey( $name )
    {
        return array_key_exists($name, $this->_uniques) ? 
            $this->_uniques[$name] : null;
    }
    
    /**
     * Gets an array of the unique keys
     *
     * @return array of {@link Xyster_Db_UniqueKey} objects
     */
    public function getUniqueKeys()
    {
        return array() + $this->_uniques;
    }
    
    /**
     * Sets a database option
     *
     * @param string $name
     * @param mixed $value
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function setOption( $name, $value )
    {
        $this->_options[$name] = $value;
        return $this;
    }
    
    /**
     * Sets the primary key
     *
     * @param Xyster_Db_PrimaryKey $value
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function setPrimaryKey( Xyster_Db_PrimaryKey $value )
    {
        $this->_pkey = $value;
        return $this;
    }
    
    /**
     * Sets the schema name
     *
     * @param string $value
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function setSchema( $value )
    {
        $this->_schema = $value;
        return $this;
    }
}