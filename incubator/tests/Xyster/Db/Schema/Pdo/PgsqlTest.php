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
// Call Xyster_Db_Schema_Pdo_PgsqlTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_Schema_Pdo_PgsqlTest::main');
}
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Xyster/Db/Schema/Pdo/Pgsql.php';

/**
 * Test class for Xyster_Db_Schema_Pdo_Pgsql.
 */
class Xyster_Db_Schema_Pdo_PgsqlTest extends Xyster_Db_Schema_TestCommon
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
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_Schema_Pdo_PgsqlTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Gets the configuration for the database adapter
     * 
     * @return array
     */
    public function getConfig()
    {
        return array(
            'host' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD,
            'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE
        );
    }
    
    /**
     * Gets the adapter 
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Pgsql';
    }
    
    /**
     * Tests the 'setAdapter' method
     */
    public function testSetAdapter()
    {
        $gateway = new Xyster_Db_Schema_Pdo_Pgsql;
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
        $this->assertEquals("'Default title of post'::character varying", $describe2['title']['DEFAULT']);
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DEFAULT' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }
}

// Call Xyster_Db_Schema_Pdo_PgsqlTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_Schema_Pdo_PgsqlTest::main') {
    Xyster_Db_Schema_Pdo_PgsqlTest::main();
}
