<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
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
require_once 'Xyster/Orm/QueryParserTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Orm_AllTests
{
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
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_AllTests::main') {
    Xyster_Orm_AllTests::main();
}
