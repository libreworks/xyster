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
// Call Xyster_Db_Schema_Pdo_MysqlTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_Schema_Pdo_MysqlTest::main');
}
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Xyster/Db/Schema/Pdo/Mysql.php';

/**
 * Test class for Xyster_Db_Schema_Pdo_Mysql.
 */
class Xyster_Db_Schema_Pdo_MysqlTest extends Xyster_Db_Schema_TestCommon
{
    /**
     * @var    Xyster_Db_Schema_Pdo_Mysql
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_Schema_Pdo_MysqlTest');
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
            'host' => TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD,
            'dbname' => TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE,
            'port' => TESTS_ZEND_DB_ADAPTER_MYSQL_PORT
        );
    }
    
    /**
     * Gets the adapter 
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Mysql';
    }
    
    /**
     * Gets the options for the TableBuilder
     *
     * @return array
     */
    public function getOptions()
    {
        return array('TYPE'=>'InnoDB');
    }
    
    /**
     * Tests the 'setAdapter' method
     */
    public function testSetAdapter()
    {
        $gateway = new Xyster_Db_Schema_Pdo_Mysql;
        $this->assertNull($gateway->getAdapter());
        $gateway->setAdapter($this->_db);
        $this->assertSame($this->_db, $gateway->getAdapter());
    }
}

// Call Xyster_Db_Schema_Pdo_MysqlTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_Schema_Pdo_MysqlTest::main') {
    Xyster_Db_Schema_Pdo_MysqlTest::main();
}
