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
 * @see Xyster_Db_Table
 */
require_once 'Xyster/Db/Table.php';
/**
 * A table in a relational database that lazy-loads its information
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Table_Lazy extends Xyster_Db_Table
{
    /**
     * @var Xyster_Db_Schema_Abstract
     */
    protected $_db;
    
    protected $_loadedCols = false;
    protected $_loadedFks = false;
    protected $_loadedIxs = false;
    protected $_loadedPk = false;
    protected $_loadedUks = false;
    
    /**
     * Creates a new lazy-loading table
     *
     * @param Xyster_Db_Schema_Abstract $db
     * @param string $name
     * @param string $schema
     */
    public function __construct( Xyster_Db_Schema_Abstract $db, $name = null, $schema = null )
    {
        $this->_db = $db;
        if ( $name !== null ) {
            $this->setName($name);
        }
        if ( $schema !== null ) {
            $this->setSchema($schema);
        }
    }
    
    /**
     * Adds a column to the object if it's not already contained
     *
     * @param Xyster_Db_Column $column The column
     * @return Xyster_Db_Table_Lazy provides a fluent interface
     */
    public function addColumn( Xyster_Db_Column $column )
    {
        $this->_loadColumns();
        return parent::addColumn($column);
    }
        
    /**
     * Adds the index to the table
     *
     * @param Xyster_Db_Index $index
     * @return Xyster_Db_Table_Lazy provides a fluent interface
     */
    public function addIndex( Xyster_Db_Index $index )
    {
        $this->_loadIndexes();
        return parent::addIndex($index);
    }
    
    /**
     * Adds a foreign key to the table
     *
     * @param Xyster_Db_ForeignKey $fk
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function addForeignKey( Xyster_Db_ForeignKey $fk )
    {
        $this->_loadForeignKeys();
        return parent::addForeignKey($fk);
    }
    
    /**
     * Adds a unique key to the table
     *
     * @param Xyster_Db_UniqueKey $uk
     * @return Xyster_Db_Table provides a fluent interface
     */
    public function addUniqueKey( Xyster_Db_UniqueKey $uk )
    {
        $this->_loadUniques();
        return parent::addUniqueKey($uk);
    }
    
    /**
     * Whether this object contains the column specified
     *
     * @param Xyster_Db_Column $column The column to check
     * @return boolean
     */
    public function containsColumn( Xyster_Db_Column $column )
    {
        $this->_loadColumns();
        return parent::containsColumn($column);
    }
    
    /**
     * Gets the column by index or null
     *
     * @param int $index
     * @return Xyster_Db_Column
     */
    public function getColumn( $index )
    {
        $this->_loadColumns();
        return parent::getColumn($index);
    }
    
    /**
     * Gets the columns in the object
     *
     * @return array of {@link Xyster_Db_Column} objects
     */
    public function getColumns()
    {
        $this->_loadColumns();
        return parent::getColumns();
    }
    
    /**
     * Gets the foreign key by name or null
     *
     * @param string $name
     * @return Xyster_Db_ForeignKey
     */
    public function getForeignKey( $name )
    {
        $this->_loadForeignKeys();
        return parent::getForeignKey($name);
    }
    
    /**
     * Gets an array of the foreign keys
     *
     * @return array of {@link Xyster_Db_ForeignKey} objects
     */
    public function getForeignKeys()
    {
        $this->_loadForeignKeys();
        return parent::getForeignKeys();
    }
        
    /**
     * Gets the index by name or null
     *
     * @param string $name
     * @return Xyster_Db_Index 
     */
    public function getIndex( $name )
    {
        $this->_loadIndexes();
        return parent::getIndex($name);
    }
    
    /**
     * Gets an array of the indexes
     *
     * @return array of {@link Xyster_Db_Index} objects
     */
    public function getIndexes()
    {
        $this->_loadIndexes();
        return parent::getIndexes();
    }
    
    /**
     * Gets the primary key or null if none
     *
     * @return Xyster_Db_PrimaryKey
     */
    public function getPrimaryKey()
    {
        $this->_loadPrimaryKey();
        return parent::getPrimaryKey();
    }

    /**
     * Gets the unique key by name or null
     *
     * @param string $name
     * @return Xyster_Db_UniqueKey
     */
    public function getUniqueKey( $name )
    {
        $this->_loadUniques();
        return parent::getUniqueKey($name);
    }
    
    /**
     * Gets an array of the unique keys
     *
     * @return array of {@link Xyster_Db_UniqueKey} objects
     */
    public function getUniqueKeys()
    {
        $this->_loadUniques();
        return parent::getUniqueKeys();
    }
    
    /**
     * Loads the columns in the table
     */
    protected function _loadColumns()
    {
        if ( $this->_loadedCols ) {
            return;
        }
        
        $description = $this->_db->getAdapter()->describeTable($this->getName(), $this->getSchema());
        
        foreach( $description as $column ) {
            if ( $column['COLUMN_NAME'] !== null ) {
                $col = new Xyster_Db_Column($column['COLUMN_NAME']);
                $col->setDefaultValue($column['DEFAULT'])
                    ->setLength($column['LENGTH'])
                    ->setPrecision($column['PRECISION'])
                    ->setScale($column['SCALE'])
                    ->setType($this->_db->toDataType($column['DATA_TYPE']))
                    ->setNullable($column['NULLABLE']);
                if ( $column['PRIMARY'] ) {
                    if ( isset($column['IDENTITY']) && $column['IDENTITY'] ) {
                        $col->setType(Xyster_Db_DataType::Identity());
                    }
                }
                $this->_columns[] = $col;
                $this->_columnNames[] = $col->getName();
            }
        }
        $this->_loadedCols = true;
    }
    
    /**
     * Loads the foreign keys in the table
     */
    protected function _loadForeignKeys()
    {
        if ( $this->_loadedFks ) {
            return;
        }
        
        $fkeys = $this->_db->getForeignKeys($this->getName(),
            $this->getSchema());
        foreach( $fkeys as $fk ) {
            $this->_foreign[] = $fk->setTable($this);
        }
        $this->_loadedFks = true;
    }
    
    /**
     * Loads the indexes in the table
     */
    protected function _loadIndexes()
    {
        if ( $this->_loadedIxs ) {
            return;
        }
        
        $indexes = $this->_db->getIndexes($this->getName(), $this->getSchema());
        foreach( $indexes as $index ) {
            $this->_indexes[] = $index->setTable($this);
        }
        $this->_loadedIxs = true;
    }
    
    /**
     * Loads the primary key
     */
    protected function _loadPrimaryKey()
    {
        if ( $this->_loadedPk || $this->_pkey ) {
            return;
        }
        
        $this->_pkey = $this->_db->getPrimaryKey($this->getName(),
            $this->getSchema());
        $this->_pkey->setTable($this);
        $this->_loadedPk = true;
    }
    
    /**
     * Loads the unique keys in the table
     */
    protected function _loadUniques()
    {
        if ( $this->_loadedUks ) {
            return;
        }
        
        $uniques = $this->_db->getUniqueKeys($this->getName(),
            $this->getSchema());
        foreach( $uniques as $unique ) {
            $this->_uniques[] = $unique->setTable($this);
        }
        $this->_loadedUks = true;
    }
}