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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Xyster/Orm/LoaderTest.php';
require_once 'Xyster/Orm/EntityTest.php';
require_once 'Xyster/Orm/EntityFieldTest.php';
require_once 'Xyster/Orm/EntityMetaTest.php';
require_once 'Xyster/Orm/SetTest.php';
require_once 'Xyster/Orm/WorkUnitTest.php';
require_once 'Xyster/Orm/RepositoryTest.php';
require_once 'Xyster/Orm/RelationTest.php';
require_once 'Xyster/Orm/ManagerTest.php';
require_once 'Xyster/Orm/Mapper/FactoryTest.php';
require_once 'Xyster/Orm/Mapper/AbstractTest.php';
require_once 'Xyster/Orm/QueryTest.php';
require_once 'Xyster/Orm/QueryReportTest.php';
require_once 'Xyster/Orm/Query/ParserTest.php';
require_once 'Xyster/Orm/Plugin/BrokerTest.php';
require_once 'Xyster/Orm/Plugin/AclTest.php';
require_once 'Xyster/Orm/XsqlTest.php';
require_once 'Xyster/Orm/Xsql/SplitTest.php';

/**
 * @see Zend_Db_SkipTests
 */
require_once 'Zend/Db/SkipTests.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Orm_AllTests
{
    protected static $_skipTestSuite = null;
    
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Orm');
        $suite->addTestSuite('Xyster_Orm_LoaderTest');
        $suite->addTestSuite('Xyster_Orm_EntityTest');
        $suite->addTestSuite('Xyster_Orm_Entity_FieldTest');
        $suite->addTestSuite('Xyster_Orm_Entity_MetaTest');
        $suite->addTestSuite('Xyster_Orm_SetTest');
        $suite->addTestSuite('Xyster_Orm_WorkUnitTest');
        $suite->addTestSuite('Xyster_Orm_RepositoryTest');
        $suite->addTestSuite('Xyster_Orm_RelationTest');
        $suite->addTestSuite('Xyster_Orm_ManagerTest');
        $suite->addTestSuite('Xyster_Orm_Mapper_FactoryTest');
        $suite->addTestSuite('Xyster_Orm_Mapper_AbstractTest');
        
        $suite->addTestSuite('Xyster_Orm_Query_ParserTest');
        $suite->addTestSuite('Xyster_Orm_QueryTest');
        $suite->addTestSuite('Xyster_Orm_Query_ReportTest');
        $suite->addTestSuite('Xyster_Orm_Plugin_BrokerTest');
        $suite->addTestSuite('Xyster_Orm_Plugin_AclTest');
        
        $suite->addTestSuite('Xyster_Orm_XsqlTest');
        $suite->addTestSuite('Xyster_Orm_Xsql_SplitTest');
        
        //self::_addDbTestSuites($suite, 'Db2');
        self::_addDbTestSuites($suite, 'Mysqli');
        //self::_addDbTestSuites($suite, 'Oracle');

        //self::_addDbTestSuites($suite, 'Pdo_Mssql');
        self::_addDbTestSuites($suite, 'Pdo_Mysql');
        //self::_addDbTestSuites($suite, 'Pdo_Oci');
        self::_addDbTestSuites($suite, 'Pdo_Pgsql');
        self::_addDbTestSuites($suite, 'Pdo_Sqlite');

        if (self::$_skipTestSuite !== null) {
            $suite->addTest(self::$_skipTestSuite);
        }
        
        return $suite;
    }
    
    protected static function _addDbTestSuites($suite, $driver)
    {
        $DRIVER = strtoupper($driver);
        $enabledConst = "TESTS_XYSTER_ORM_ADAPTER_{$DRIVER}_ENABLED";
        if (!defined($enabledConst) || constant($enabledConst) != true) {
            self::_skipTestSuite($driver, "this Adapter is not enabled in TestConfiguration.php");
            return;
        }

        $ext = array(
            'Oracle' => 'oci8',
            'Db2'    => 'ibm_db2',
            'Mysqli' => 'mysqli'
        );

        if (isset($ext[$driver]) && !extension_loaded($ext[$driver])) {
            self::_skipTestSuite($driver, "extension '{$ext[$driver]}' is not loaded");
            return;
        }

        if (preg_match('/^pdo_(.*)/i', $driver, $matches)) {
            // check for PDO extension
            if (!extension_loaded('pdo')) {
                self::_skipTestSuite($driver, "extension 'PDO' is not loaded");
                return;
            }

            // check the PDO driver is available
            $pdo_driver = strtolower($matches[1]);
            if (!in_array($pdo_driver, PDO::getAvailableDrivers())) {
                self::_skipTestSuite($driver, "PDO driver '{$pdo_driver}' is not available");
                return;
            }
        }

        try {

            Zend_Loader::loadClass("Xyster_Orm_Mapper_{$driver}Test");

            // if we get this far, there have been no exceptions loading classes
            // so we can add them as test suites
            $suite->addTestSuite("Xyster_Orm_Mapper_{$driver}Test");
        } catch (Zend_Exception $e) {
            self::_skipTestSuite("cannot load test classes: " . $e->getMessage());
        }
    }
    
    protected static function _skipTestSuite($driver, $message = '')
    {
        $skipTestClass = "Zend_Db_Skip_{$driver}Test";
        $skipTest = new $skipTestClass();
        $skipTest->message = $message;

        if (self::$_skipTestSuite === null) {
            self::$_skipTestSuite = new PHPUnit_Framework_TestSuite('Xyster_Orm skipped test suites');
        }

        self::$_skipTestSuite->addTest($skipTest);
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_AllTests::main') {
    Xyster_Orm_AllTests::main();
}
