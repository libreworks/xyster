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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_AllTests::main');
}

require_once 'Xyster/Orm/Meta/EntityBuilderTest.php';
require_once 'Xyster/Orm/Meta/EntityTest.php';
require_once 'Xyster/Orm/Meta/PropertyTest.php';
require_once 'Xyster/Orm/Meta/Value/BasicTest.php';
require_once 'Xyster/Orm/Type/BigIntegerTest.php';
require_once 'Xyster/Orm/Type/BooleanTest.php';
require_once 'Xyster/Orm/Type/DateTest.php';
require_once 'Xyster/Orm/Type/DecimalTest.php';
require_once 'Xyster/Orm/Type/FloatTest.php';
require_once 'Xyster/Orm/Type/IntegerTest.php';
require_once 'Xyster/Orm/Type/RealTest.php';
require_once 'Xyster/Orm/Type/StringTest.php';
require_once 'Xyster/Orm/Type/TextTest.php';
require_once 'Xyster/Orm/Type/TimeTest.php';
require_once 'Xyster/Orm/Type/TimestampTest.php';

class Xyster_Orm_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Orm');
        $suite->setBackupGlobals(false);
        $suite->setBackupStaticAttributes(false);
        $suite->addTestSuite('Xyster_Orm_Meta_EntityBuilderTest');
        $suite->addTestSuite('Xyster_Orm_Meta_EntityTest');
        $suite->addTestSuite('Xyster_Orm_Meta_PropertyTest');
        $suite->addTestSuite('Xyster_Orm_Meta_Value_BasicTest');
        $suite->addTestSuite('Xyster_Orm_Type_BigIntegerTest');
        $suite->addTestSuite('Xyster_Orm_Type_BooleanTest');
        $suite->addTestSuite('Xyster_Orm_Type_DateTest');
        $suite->addTestSuite('Xyster_Orm_Type_DecimalTest');
        $suite->addTestSuite('Xyster_Orm_Type_FloatTest');
        $suite->addTestSuite('Xyster_Orm_Type_IntegerTest');
        $suite->addTestSuite('Xyster_Orm_Type_RealTest');
        $suite->addTestSuite('Xyster_Orm_Type_StringTest');
        $suite->addTestSuite('Xyster_Orm_Type_TextTest');
        $suite->addTestSuite('Xyster_Orm_Type_TimeTest');
        $suite->addTestSuite('Xyster_Orm_Type_TimestampTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_AllTests::main') {
    Xyster_Orm_AllTests::main();
}
