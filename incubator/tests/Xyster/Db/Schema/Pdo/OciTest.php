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
require_once 'Xyster/Db/Schema/Pdo/Oci.php';

/**
 * Test class for Xyster_Db_Schema_Pdo_Oci.
 */
class Xyster_Db_Schema_Pdo_OciTest extends Xyster_Db_Schema_TestCommon
{
    /**
     * @var    Xyster_Db_Schema_Pdo_Oci
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
            'host' => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD,
            'dbname' => TESTS_ZEND_DB_ADAPTER_ORACLE_SID
        );
    }
    
    /**
     * Gets the adapter 
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Oci';
    }

    /**
     * Tests the 'setAdapter' method
     */
    public function testSetAdapter()
    {
        $gateway = new Xyster_Db_Schema_Pdo_Oci;
        $this->assertNull($gateway->getAdapter());
        $gateway->setAdapter($this->_db);
        $this->assertSame($this->_db, $gateway->getAdapter());
    }
    

    /**
     * Tests the 'addColumn' method
     */
    public function testAddColumn()
    {
        $this->_setupTestTable();
        $describe = $this->_db->describeTable('forum');
        $this->assertArrayNotHasKey('category_id', $describe);
        
        $column = new Xyster_Db_Column('category_id');
        $column->setType(Xyster_Db_DataType::Integer());
        $table = new Xyster_Db_Table('forum');
        
        $this->object->addColumn($column, $table);
        
        $describe2 = $this->_db->describeTable('forum');
        $this->assertArrayHasKey('category_id', $describe2);
        $this->assertEquals('category_id', $describe2['category_id']['COLUMN_NAME']);
        $this->assertEquals('number', strtolower($describe2['category_id']['DATA_TYPE']));
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
        $this->assertEquals("'Default title of post'", $describe2['title']['DEFAULT']);
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
        $this->assertNotEquals('VARCHAR2', $describe['message']['DATA_TYPE']);
        $this->object->setType($col, $table, Xyster_Db_DataType::Varchar(), 255);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertEquals('VARCHAR2', $describe2['title']['DATA_TYPE']);
        $this->assertEquals(255, $describe2['title']['LENGTH']);
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DATA_TYPE' && $key != 'LENGTH' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }    
}