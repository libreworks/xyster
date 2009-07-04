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
// Call Xyster_Db_Schema_Pdo_Ibm_Db2Test::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_Schema_Pdo_Ibm_Db2Test::main');
}
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Xyster/Db/Schema/Pdo/Ibm/Db2.php';

/**
 * Test class for Xyster_Db_Schema_Pdo_Pgsql.
 */
class Xyster_Db_Schema_Pdo_Ibm_Db2Test extends Xyster_Db_Schema_TestCommon
{
    /**
     * @var    Xyster_Db_Schema_Pdo_Pgsql
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_Schema_Pdo_Ibm_Db2Test');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the test
     */
    protected function setUp()
    {
        $this->_db = Zend_Db::factory($this->getDriver(), $this->getConfig());
        $className = 'Xyster_Db_Schema_Pdo_Ibm_Db2';
        try {
            $conn = $this->_db->getConnection();
            $this->object = new $className($this->_db);
        } catch (Zend_Exception $e) {
            $this->_db = null;
            echo $e;
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting Zend_Db_Adapter_Exception, got ' . get_class($e));
            $this->markTestSkipped($e->getMessage());
        }
    }
    
    /**
     * Gets the configuration for the database adapter
     * 
     * @return array
     */
    public function getConfig()
    {
        return array(
            'host' => TESTS_ZEND_DB_ADAPTER_DB2_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_DB2_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_DB2_PASSWORD,
            'dbname' => TESTS_ZEND_DB_ADAPTER_DB2_DATABASE,
            'port' => TESTS_ZEND_DB_ADAPTER_DB2_PORT
        );
    }
    
    /**
     * Gets the adapter 
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Ibm';
    }
    
    /**
     * Tests the 'setAdapter' method
     */
    public function testSetAdapter()
    {
        $gateway = new Xyster_Db_Schema_Pdo_Ibm_Db2;
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
        $this->assertEquals("'Default title of post'", $describe2['title']['DEFAULT']);
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DEFAULT' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }
    
    /**
     * Renames a sequence
     */
    public function testRenameSequence()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->renameSequence('foo', 'bar', null);
    }
    
    /**
     * Renames a column
     */
    public function testRenameColumn()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->renameColumn(new Xyster_Db_Column, 'username2', new Xyster_Db_Table);
    }
    
    /**
     * Tests the 'setType' method
     */
    public function testSetType()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(2);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertNotEquals('varchar', $describe['title']['DATA_TYPE']);
        $this->object->setType($col, $table, Xyster_Db_DataType::Char(), 254);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertEquals('char', strtolower(substr($describe2['title']['DATA_TYPE'], 0, 4)));
        $this->assertEquals(254, $describe2['title']['LENGTH']);
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DATA_TYPE' && $key != 'LENGTH' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }
}

// Call Xyster_Db_Schema_Pdo_Ibm_Db2Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_Schema_Pdo_Ibm_Db2Test::main') {
    Xyster_Db_Schema_Pdo_Ibm_Db2Test::main();
}
