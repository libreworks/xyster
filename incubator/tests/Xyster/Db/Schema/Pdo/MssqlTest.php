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
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Xyster/Db/Schema/Pdo/Mssql.php';

/**
 * Test class for Xyster_Db_Schema_Pdo_Mssql.
 */
class Xyster_Db_Schema_Pdo_MssqlTest extends Xyster_Db_Schema_TestCommon
{
    /**
     * @var    Xyster_Db_Schema_Pdo_Mssql
     */
    protected $object;

    /**
     * Gets the configuration for the database adapter
     * 
     * @return array
     */
    public function getConfig()
    {
        return array(
            'host' => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PASSWORD,
            'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_DATABASE
        );
    }
    
    /**
     * Gets the adapter 
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Mssql';
    }
    
    /**
     * Tests the 'setAdapter' method
     */
    public function testSetAdapter()
    {
        $gateway = new Xyster_Db_Schema_Pdo_Mssql;
        $this->assertNull($gateway->getAdapter());
        $gateway->setAdapter($this->_db);
        $this->assertSame($this->_db, $gateway->getAdapter());
    }

    /**
     * Sets the default value for a column
     */
    public function testSetDefaultValue()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(2);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertEquals('', $describe['title']['DEFAULT']);
        $this->object->setDefaultValue($col, 'Default title of post', $table);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertEquals("('Default title of post')", $describe2['title']['DEFAULT']);
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DEFAULT' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }
    
    /**
     * Tests the 'setType' method
     */
    public function testSetType()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(2);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertNotEquals('nvarchar', $describe['message']['DATA_TYPE']);
        $this->object->setType($col, $table, Xyster_Db_DataType::Varchar(), 255);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertEquals('nvarchar', $describe2['title']['DATA_TYPE']);
        $this->assertEquals(510, $describe2['title']['LENGTH']); // NVARCHAR shows up as 2x bigger
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DATA_TYPE' && $key != 'LENGTH' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }
}
