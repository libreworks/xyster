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

require_once 'Xyster/Db/Gateway/AbstractTest.php';
require_once 'Xyster/Db/Gateway/DataTypeTest.php';
require_once 'Xyster/Db/Gateway/ReferentialActionTest.php';
require_once 'Xyster/Db/Gateway/TableBuilderTest.php';
require_once 'Xyster/Db/Gateway/TableBuilder/ColumnTest.php';
require_once 'Xyster/Db/Gateway/TableBuilder/ForeignKeyTest.php';
require_once 'Xyster/Db/Gateway/TableBuilder/IndexTest.php';
require_once 'Xyster/Db/Gateway/TableBuilder/PrimaryKeyTest.php';
require_once 'Xyster/Db/Gateway/TableBuilder/UniqueTest.php';
require_once 'Xyster/Db/Gateway/Pdo/MysqlTest.php';

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
        $suite->addTestSuite('Xyster_Db_Gateway_AbstractTest');
        $suite->addTestSuite('Xyster_Db_Gateway_DataTypeTest');
        $suite->addTestSuite('Xyster_Db_Gateway_ReferentialActionTest');
        $suite->addTestSuite('Xyster_Db_Gateway_TableBuilderTest');
        $suite->addTestSuite('Xyster_Db_Gateway_TableBuilder_ColumnTest');
        $suite->addTestSuite('Xyster_Db_Gateway_TableBuilder_ForeignKeyTest');
        $suite->addTestSuite('Xyster_Db_Gateway_TableBuilder_IndexTest');
        $suite->addTestSuite('Xyster_Db_Gateway_TableBuilder_PrimaryKeyTest');
        $suite->addTestSuite('Xyster_Db_Gateway_TableBuilder_UniqueTest');
        $suite->addTestSuite('Xyster_Db_Gateway_Pdo_MysqlTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Db_AllTests::main') {
    Xyster_Db_AllTests::main();
}
