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
 * @package   UnitTests
 * @subpackage Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * PHPUnit_Util_Filter
 */
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';
/**
 * @see Xyster_Db_Statement_Stub
 */
require_once 'Xyster/Db/Statement/Stub.php';

/**
 * Stub db adapter
 * 
 */
class Xyster_Db_Adapter_Stub extends Zend_Db_Adapter_Abstract
{
    public $config = null;

    /**
     * Rewriting constructor
     *
     */
    public function __construct()
    {
    }
    
    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return array('stub_table');
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable( $tableName, $schemaName = null )
    {
        return array(array(
            'SCHEMA_NAME'      => $schemaName,
            'TABLE_NAME'       => $tableName,
            'COLUMN_NAME'      => null,
            'COLUMN_POSITION'  => null,
            'DATA_TYPE'        => null,
            'DEFAULT'          => null,
            'NULLABLE'         => null,
            'LENGTH'           => null,
            'SCALE'            => null,
            'PRECISION'        => null,
            'UNSIGNED'         => null,
            'PRIMARY'          => null,
            'PRIMARY_POSITION' => null,
        ));
    }

    /**
     * Creates a connection to the database.
     *
     */
    protected function _connect()
    {
        $this->_connection = $this;
    }

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection()
    {
        $this->_connection = null;
    }

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param  string|Zend_Db_Select $sql SQL query
     * @return Xyster_Db_Statement_Stub
     */
    public function prepare( $sql )
    {
        return new Xyster_Db_Statement_Stub;
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return integer
     */
    public function lastInsertId( $tableName = null, $primaryKey = null )
    {
        return null;
    }

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
        return true;
    }

    /**
     * Commit a transaction.
     */
    protected function _commit()
    {
        return true;
    }

    /**
     * Roll-back a transaction.
     */
    protected function _rollBack()
    {
        return true;
    }

    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     */
    public function setFetchMode($mode)
    {
        return;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param mixed $sql
     * @param integer $count
     * @param integer $offset
     * @return string
     */
    public function limit( $sql, $count, $offset = 0 )
    {
        return $sql . " LIMIT $count OFFSET $offset";
    }

    /**
     * Check if the adapter supports real SQL parameters.
     *
     * @param string $type
     * @return bool
     */
    public function supportsParameters($type)
    {
        return true;
    }

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _checkRequiredOptions(array $config)
    {
        if (! array_key_exists('dbname', $config)) {
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration must have a key for 'dbname' that names the database instance");
        }
        $this->config = $config;
    }
}