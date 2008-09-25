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
 * @subpackage Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Data_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Xyster/Data/AggregateTest.php';
require_once 'Xyster/Data/BinderTest.php';
require_once 'Xyster/Data/JunctionTest.php';
require_once 'Xyster/Data/ExpressionTest.php';
require_once 'Xyster/Data/CriterionTest.php';
require_once 'Xyster/Data/FieldTest.php';
require_once 'Xyster/Data/Field/ClauseTest.php';
require_once 'Xyster/Data/FieldAggregateTest.php';
require_once 'Xyster/Data/Operator/ExpressionTest.php';
require_once 'Xyster/Data/SortTest.php';
require_once 'Xyster/Data/Sort/ClauseTest.php';
require_once 'Xyster/Data/SetTest.php';
require_once 'Xyster/Data/TupleTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Data_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Data');
        $suite->addTestSuite('Xyster_Data_AggregateTest');
        $suite->addTestSuite('Xyster_Data_BinderTest');
        $suite->addTestSuite('Xyster_Data_JunctionTest');
        $suite->addTestSuite('Xyster_Data_ExpressionTest');
        $suite->addTestSuite('Xyster_Data_CriterionTest');
        $suite->addTestSuite('Xyster_Data_FieldTest');
        $suite->addTestSuite('Xyster_Data_FieldAggregateTest');
        $suite->addTestSuite('Xyster_Data_Field_ClauseTest');
        $suite->addTestSuite('Xyster_Data_Operator_ExpressionTest');
        $suite->addTestSuite('Xyster_Data_SortTest');
        $suite->addTestSuite('Xyster_Data_Sort_ClauseTest');
        $suite->addTestSuite('Xyster_Data_SetTest');
        $suite->addTestSuite('Xyster_Data_TupleTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Data_AllTests::main') {
    Xyster_Data_AllTests::main();
}
