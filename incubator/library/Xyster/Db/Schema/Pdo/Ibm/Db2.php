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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_Schema_Abstract
 */
require_once 'Xyster/Db/Schema/Abstract.php';
/**
 * An abstraction layer for schema manipulation in IBM DB2.
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Schema_Pdo_Ibm_Db2 extends Xyster_Db_Schema_Abstract
{
    /**
     * Creates a new SQL Server schema adapter
     *
     * @param Zend_Db_Adapter_Pdo_Ibm $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Ibm $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Ibm $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Ibm $db )
    {
        $this->_setAdapter($db);
    }

    /**
     * Creates a new index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function createIndex( Xyster_Db_Index $index )
    {
    	$tableName = $this->_tableName($index->getTable());
        $sql = "CREATE INDEX " . $this->_quote($index->getName()) . " ON " .
            $tableName . " ";
        $columns = array();
        foreach( $index->getSortedColumns() as $sort ) {
            /* @var $sort Xyster_Data_Sort */
            $vdir = $sort->getDirection(); 
            $columns[] = $this->_quote($sort->getField()->getName()) . $vdir;
        }
        $sql .= "( " . implode(', ', $columns) . " )";
        $this->getAdapter()->query($sql);
    }
        
    /**
     * Drops an index
     *
     * @param Xyster_Db_Index $index
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function dropIndex( Xyster_Db_Index $index )
    {
    	$sql = "DROP INDEX ";
        if ( $index->getTable()->getSchema() ) {
            $sql .= $this->_quote($index->getTable()->getSchema()) . '.';
        }
        $sql .= $this->_quote($index->getName());
        $this->getAdapter()->query($sql);
    }
    
    /**
     * Gets all foreign keys (optionally in a table and/or schema)
     * 
     * The method will return an array of {@link Xyster_Db_ForeignKey} objects.
     * 
     * If the table parameter is specified, this method should return all 
     * foreign keys for the table given.
     * 
     * If the schema is specified but not the table, the method will return the
     * foreign keys for all tables in the schema.
     * 
     * If both are specified, the method will return the foreign keys for the
     * table in the given schema.
     * 
     * If neither are specified, the list should return all foreign keys in the
     * database. 
     *
     * @param string $table Optional. The table name
     * @param string $schema Optional. The schema name
     * @return array of {@link Xyster_Db_ForeignKey} objects
     */
    public function getForeignKeys( $table = null, $schema = null )
    {
    	$sql = 'SELECT c.constname as "keyname", updaterule as "onupdate", deleterule as "ondelete", ' .
                ' k.tabschema as "schemaname", k.tabname as "tablename", k.colname as "colname",' .
                ' rk.tabname AS "reftablename", rk.colname AS "refcolname"';
        $sql .= " FROM syscat.references c INNER JOIN" .
                " syscat.keycoluse k ON" .
                " c.constname = k.constname AND" .
                " c.tabschema = k.tabschema INNER JOIN" .
                " syscat.keycoluse rk ON" .
                " c.refkeyname = rk.constname AND" .
                " c.tabschema = rk.tabschema";
        if ( $schema !== null ) {
            $sql .= " and c.tabschema = '" . $schema . "'";
        }
        if ( $table !== null ) {
            $sql .= " and c.tabname = '" . $table . "'";
        }
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $fks = array();
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $row['schemaname']);
            $refTable = $this->_getLazyTable($row['reftablename']);
            
            $name = $row['keyname'];
            if ( !array_key_exists($name, $fks) ) {
                $fk = new Xyster_Db_ForeignKey;
                $fk->setReferencedTable($refTable)
                    ->setTable($table)
                    ->setName($row['keyname']);
                if ( isset($row['ondelete']) && $row['ondelete'] ) {
                	$refAction = null;
                	switch($row['ondelete']) {
                		case 'A': $refAction = Xyster_Db_ReferentialAction::NoAction(); break;
                		case 'C': $refAction = Xyster_Db_ReferentialAction::Cascade(); break;
                		case 'N': $refAction = Xyster_Db_ReferentialAction::SetNull(); break;
                		case 'R': $refAction = Xyster_Db_ReferentialAction::Restrict(); break;
                	}
                    $fk->setOnDelete($refAction);
                }
                if ( isset($row['onupdate']) && $row['onupdate'] ) {
                    $refAction = null;
                    switch($row['onupdate']) {
                        case 'A': $refAction = Xyster_Db_ReferentialAction::NoAction(); break;
                        case 'R': $refAction = Xyster_Db_ReferentialAction::Restrict(); break;
                    }
                    $fk->setOnUpdate($refAction);
                }
                $fks[$name] = $fk;
            }
            foreach( $table->getColumns() as $column ) {
                if ( $column->getName() == $row['colname'] ) {
                    $fks[$name]->addColumn($column);
                }
            }
            foreach( $refTable->getColumns() as $column ) {
                if ( $column->getName() == $row['refcolname'] ) {
                    $fks[$name]->addReferencedColumn($column);
                }
            }
        }

        return $fks;
    }
    
    /**
     * Gets all indexes (optionally for a table and/or schema)
     * 
     * The method will return an array of {@link Xyster_Db_Index} objects.  It
     * should NOT return unique keys or primary keys, even if the RDBMS lists
     * them as indexes.
     * 
     * If the table parameter is specified, this method should return all 
     * indexes for the table given.
     * 
     * If the schema is specified but not the table, the method will return the
     * indexes for all tables in the schema.
     * 
     * If both are specified, the method will return the indexes for the table
     * in the given schema.
     * 
     * If neither are specified, the list should return all indexes in the
     * database. 
     *
     * @param string $table Optional. The table name
     * @param string $schema Optional. The schema name
     * @return array of {@link Xyster_Db_Index} objects
     */
    public function getIndexes( $table = null, $schema = null )
    {
        $sql = 'select i.tabschema as "schemaname", i.indname as "indexname",' .
                ' i.tabname as "tablename", c.colname as "colname", c.colorder as "colorder" from' .
                ' syscat.indexes i inner join syscat.indexcoluse c on' .
                ' i.indschema = c.indschema and i.indname = c.indname' .
                " where i.user_defined = 1 and uniquerule = 'D'";
        if ( $table !== null ) {
            $sql .= " and i.tabname = '" . $table . "' ";
        }
        if ( $schema != null ) {
            $sql .= " and i.tabschema = '" . $schema . "'";
        }
        $sql .= " order by c.colseq";
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $indexes = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $row['schemaname']);
     
            $key = $row['schemaname'] . '.' . $row['indexname'];
            
            $column = null;
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $column = $tcolumn;
                    break;
                }
            }
            
            $sortCol = $row['colorder'] == 'D' ? $column->desc() : $column->asc();
            if ( array_key_exists($key, $indexes) ) {
                $indexes[$key]->addSortedColumn($sortCol);
            } else {
                $index = new Xyster_Db_Index;
                $index->setTable($table)
                    ->setName($row['indexname'])
                    ->addSortedColumn($sortCol);
                $indexes[$key] = $index;
            }
        }
        
        return $indexes;
    }
    
    /**
     * Gets the primary keys for a specific table if one exists 
     *
     * @param string $table The table name
     * @param string $schema Optional. The schema name
     * @return Xyster_Db_PrimaryKey or null if one doesn't exist
     */
    public function getPrimaryKey( $table, $schema = null )
    {
    	$sql = 'select i.constname as "indexname", k.colname as "colname"' .
    	    " from syscat.tabconst i inner join syscat.keycoluse k on " .
    	    " i.constname = k.constname and i.tabname = k.tabname and " .
    	    " i.tabschema = k.tabschema " . 
            " where i.type = 'P' and i.tabname = '" . $table . "'";
        if ( $schema != null ) {
            $sql .= " AND i.tabschema = '" . $schema . "'";
        }
    	$sql .= " order by k.colseq";
        $statement = $this->getAdapter()->fetchAll($sql);

        $primary = new Xyster_Db_PrimaryKey;
        $table = $this->_getLazyTable($table, $schema);
        $primary->setTable($table);
        
        foreach( $statement as $row ) {
            $primary->setName($row['indexname']);
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $primary->addColumn($tcolumn);
                }
            }
        }
        
        return $primary;
    }
    
    /**
     * Gets the unique keys for a specific table (and optionally schema)
     *
     * @param string $table The table name
     * @param string $schema
     * @return array of {@link Xyster_Db_UniqueKey} objects
     */
    public function getUniqueKeys( $table, $schema = null )
    {
    	$sql = 'select i.tabschema as "schemaname", i.indname as "indexname",' .
                ' i.tabname as "tablename", c.colname as "colname", c.colorder as "colorder" from' .
                ' syscat.indexes i inner join syscat.indexcoluse c on' .
                ' i.indschema = c.indschema and i.indname = c.indname' .
                " where uniquerule = 'U'";
        $sql .= " and i.tabname = '" . $table . "' ";
        if ( $schema != null ) {
            $sql .= " and i.tabschema = '" . $schema . "'";
        }
        $sql .= " order by c.colseq";
        $statement = $this->getAdapter()->fetchAll($sql);
        
        $indexes = array();
        
        foreach( $statement as $row ) {
            $table = $this->_getLazyTable($row['tablename'], $row['schemaname']);
     
            $key = $row['schemaname'] . '.' . $row['indexname'];
            
            $column = null;
            foreach( $table->getColumns() as $tcolumn ) {
                if ( $tcolumn->getName() == $row['colname'] ) {
                    $column = $tcolumn;
                    break;
                }
            }
            
            if ( array_key_exists($key, $indexes) ) {
                $indexes[$key]->addColumn($column);
            } else {
                $index = new Xyster_Db_UniqueKey;
                $index->setTable($table)
                    ->setName($row['indexname'])
                    ->addColumn($column);
                $indexes[$key] = $index;
            }
        }
        
        return $indexes;
    }    

    /**
     * Lists the sequence names in the given database (or schema)
     *
     * @param string $schema Optional. The schema used to locate sequences.
     * @return array of sequence names
     */
    public function listSequences( $schema = null )
    {
    	$sql = "SELECT " . $this->_quote('SEQNAME') . ' FROM syscat.sequences';
        if ( $schema !== null ) {
            $sql .= " WHERE SEQSCHEMA = '" . $schema . "'";
        }
        return $this->getAdapter()->fetchCol($sql);
    }
        
    /**
     * Renames a sequence
     * 
     * The SQL-2003 standard doesn't allow for renaming sequences, not all
     * databases support this capability.
     *
     * @param string $old The current sequence name
     * @param string $new The new sequence name
     * @param string $schema The schema name
     * @throws Xyster_Db_Schema_Exception if the database doesn't support sequences
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameSequence( $old, $new, $schema = null )
    {
    	require_once 'Xyster/Db/Schema/Exception.php';
        throw new Xyster_Db_Schema_Exception('This version of IBM DB2 does not support renaming sequences');
    }
        
    /**
     * Renames a column
     * 
     * This method will call the {@link Xyster_Db_Column::setName} method with
     * the new name.  There is no need to call it separately.
     *
     * @param Xyster_Db_Column $column The column to rename
     * @param string $newName The new index name
     * @param Xyster_Db_Table $table The table
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameColumn( Xyster_Db_Column $column, $newName, Xyster_Db_Table $table )
    {
    	// we're going to try the syntax.  Works for DB2 9 for z/OS. others fail
        $sql = "ALTER TABLE " . $this->_tableName($table) . " RENAME COLUMN " . 
            $this->_quote($column->getName()) . ' TO ' . $this->_quote($newName);
    	try {
            $this->getAdapter()->query($sql);
    	} catch ( Exception $e ) {
    		require_once 'Xyster/Db/Schema/Exception.php';
            throw new Xyster_Db_Schema_Exception('This version of IBM DB2 does not support renaming columns');
    	}
        $column->setName($newName);
    }
    
    /**
     * Renames an index
     * 
     * This method will call the {@link Xyster_Db_Index::setName} method with
     * the new name.  There is no need to call it separately.
     *
     * @param Xyster_Db_Index $index The index to rename
     * @param string $newName The new index name
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameIndex( Xyster_Db_Index $index, $newName )
    {
    	$sql = 'RENAME INDEX ';
        if ( $index->getTable()->getSchema() ) {
            $sql .= $this->_quote($index->getTable()->getSchema()) . '.';
        }
        $sql .= $this->_quote($index->getName()) . ' TO ' .
            $this->_quote($newName);
        $this->getAdapter()->query($sql);
        $index->setName($newName);
    }
    
    /**
     * Renames a table
     * 
     * This method will call the {@link Xyster_Db_Table::setName} method with
     * the new name.  There is no need to call it separately.
     *
     * @param Xyster_Db_Table $table The table to rename
     * @param string $newName Its new name
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function renameTable( Xyster_Db_Table $table, $newName )
    {
    	$sql = "RENAME TABLE " . $this->_tableName($table) .
            " TO " . $this->_quote($newName);
        $this->getAdapter()->query($sql);
        $table->setName($newName);
    }
    
    /**
     * Sets whether or not the column will accept null
     * 
     * This method will call the {@link Xyster_Db_Column::setNull} method.
     * There is no need to call this method separately.
     *
     * @param Xyster_Db_Column $column
     * @param Xyster_Db_Table $table The table
     * @param boolean $null Optional. Defaults to true.
     * @throws Zend_Db_Exception if an error occurs
     */
    public function setNull( Xyster_Db_Column $column, Xyster_Db_Table $table, $null = true )
    {
    	$sql = "ALTER TABLE " . $this->_tableName($table) . " ALTER COLUMN " . 
           $this->_quote($column->getName()) . ' ';
        $sql .= ( $null ) ? ' DROP NOT NULL' : ' SET NOT NULL';
        $this->getAdapter()->query($sql);
        $column->setNullable($null);
    }
    
    /**
     * Changes the type of a column
     * 
     * This method will call the {@link Xyster_Db_Column::setType} method (as
     * well as the setLength, setPrecision, and setScale methods if appropriate)
     * so there is no need to call these separately.
     *
     * @param Xyster_Db_Column $column
     * @param Xyster_Db_Table $table The table
     * @param Xyster_Db_DataType $type
     * @param int $length Optional. A length parameter
     * @param int $precision Optional. A precision parameter
     * @param int $scale Optional. A scale parameter 
     * @throws Zend_Db_Exception if a database error occurs
     */
    public function setType( Xyster_Db_Column $column, Xyster_Db_Table $table, Xyster_Db_DataType $type, $length=null, $precision=null, $scale=null )
    {
    	$newcol = new Xyster_Db_Column($column->getName());
        $newcol->setLength($length)->setPrecision($precision)->setScale($scale)
            ->setType($type);
        $sql = "ALTER TABLE " . $this->_tableName($table) . " ALTER COLUMN " . 
            $this->_quote($column->getName()) . " SET DATA TYPE " .
            $this->toSqlType($newcol);
        $this->getAdapter()->query($sql);
        $column->setType($type)->setLength($length)->setPrecision($precision)->setScale($scale);
    }
    
    /**
     * Whether the database supports sequences
     *
     * @return boolean
     */
    public function supportsSequences()
    {
        return true;
    }
    
    /**
     * Converts the database-reported data type into a DataType enum
     *
     * @param string $sqlType The database data type
     * @return Xyster_Db_DataType
     */
    public function toDataType( $sqlType )
    {
        return parent::toDataType($sqlType);
    }
    
    /**
     * Translates a column into the SQL syntax for its data type
     * 
     * For example, a column with the VARCHAR type and a length of 255 would 
     * return "VARCHAR(255)". 
     *
     * @param Xyster_Db_Column $column
     * @return string
     */
    public function toSqlType( Xyster_Db_Column $column )
    {
        $type = $column->getType();
        $sql = '';
        if ( $type === Xyster_Db_DataType::Identity() ) {
            $sql = 'INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY';
        } else if ( $type === Xyster_Db_DataType::Boolean() ) {
        	$sql = 'SMALLINT';
        } else {
            $sql = parent::toSqlType($column); 
        }
        return $sql;
    }
}