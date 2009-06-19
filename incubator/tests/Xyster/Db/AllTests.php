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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Xyster/Db/Table/BuilderTest.php';
require_once 'Xyster/Db/ColumnTest.php';
require_once 'Xyster/Db/ColumnOwnerTest.php';
require_once 'Xyster/Db/ForeignKeyTest.php';
require_once 'Xyster/Db/IndexTest.php';
require_once 'Xyster/Db/ConstraintTest.php';
require_once 'Xyster/Db/TableTest.php';
require_once 'Xyster/Db/Schema/AbstractTest.php';
require_once 'Xyster/Db/Schema/MysqliTest.php';
require_once 'Xyster/Db/Schema/Pdo/MssqlTest.php';
require_once 'Xyster/Db/Schema/Pdo/MysqlTest.php';
require_once 'Xyster/Db/Schema/Pdo/PgsqlTest.php';
require_once 'Xyster/Db/Schema/Pdo/SqliteTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Db');
        $suite->addTestSuite('Xyster_Db_Table_BuilderTest');
        $suite->addTestSuite('Xyster_Db_ColumnTest');
        $suite->addTestSuite('Xyster_Db_ColumnOwnerTest');
        $suite->addTestSuite('Xyster_Db_ForeignKeyTest');
        $suite->addTestSuite('Xyster_Db_IndexTest');
        $suite->addTestSuite('Xyster_Db_ConstraintTest');
        $suite->addTestSuite('Xyster_Db_TableTest');
        $suite->addTestSuite('Xyster_Db_Schema_AbstractTest');
        $suite->addTestSuite('Xyster_Db_Schema_MysqliTest');
        $suite->addTestSuite('Xyster_Db_Schema_Pdo_MssqlTest');
        $suite->addTestSuite('Xyster_Db_Schema_Pdo_MysqlTest');
        $suite->addTestSuite('Xyster_Db_Schema_Pdo_PgsqlTest');
        $suite->addTestSuite('Xyster_Db_Schema_Pdo_SqliteTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Db_AllTests::main') {
    Xyster_Db_AllTests::main();
}
