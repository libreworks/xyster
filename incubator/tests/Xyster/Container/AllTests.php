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
 * @subpackage Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Container_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Xyster/Container/FeaturesTest.php';
require_once 'Xyster/Container/Adapter/AbstractTest.php';
require_once 'Xyster/Container/Adapter/InstanceTest.php';
require_once 'Xyster/Container/Behavior/AbstractTest.php';
require_once 'Xyster/Container/Behavior/AutomatedTest.php';
require_once 'Xyster/Container/Behavior/CachedTest.php';
require_once 'Xyster/Container/Behavior/PropertyApplicatorTest.php';
require_once 'Xyster/Container/Behavior/StoredTest.php';
require_once 'Xyster/Container/Behavior/Factory/AdaptiveTest.php';
require_once 'Xyster/Container/Behavior/Factory/AutomatedTest.php';
require_once 'Xyster/Container/Behavior/Factory/PropertyApplicatorTest.php';
require_once 'Xyster/Container/Injection/ConstructorTest.php';
require_once 'Xyster/Container/Injection/MethodTest.php';
require_once 'Xyster/Container/Injection/SetterTest.php';
require_once 'Xyster/Container/Injection/Factory/AdaptiveTest.php';
require_once 'Xyster/Container/Injection/Factory/ConstructorTest.php';
require_once 'Xyster/Container/Injection/Factory/MethodTest.php';
require_once 'Xyster/Container/Injection/Factory/SetterTest.php';
require_once 'Xyster/Container/Parameter/BasicTest.php';
require_once 'Xyster/Container/Parameter/ConstantTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Container_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Container');
        $suite->addTestSuite('Xyster_Container_FeaturesTest');
        $suite->addTestSuite('Xyster_Container_Adapter_AbstractTest');
        $suite->addTestSuite('Xyster_Container_Adapter_InstanceTest');
        $suite->addTestSuite('Xyster_Container_Behavior_AbstractTest');
        $suite->addTestSuite('Xyster_Container_Behavior_AutomatedTest');
        $suite->addTestSuite('Xyster_Container_Behavior_CachedTest');
        $suite->addTestSuite('Xyster_Container_Behavior_PropertyApplicatorTest');
        $suite->addTestSuite('Xyster_Container_Behavior_StoredTest');
        $suite->addTestSuite('Xyster_Container_Behavior_Factory_AdaptiveTest');
        $suite->addTestSuite('Xyster_Container_Behavior_Factory_AutomatedTest');
        $suite->addTestSuite('Xyster_Container_Behavior_Factory_PropertyApplicatorTest');
        $suite->addTestSuite('Xyster_Container_Injection_ConstructorTest');
        $suite->addTestSuite('Xyster_Container_Injection_MethodTest');
        $suite->addTestSuite('Xyster_Container_Injection_SetterTest');
        $suite->addTestSuite('Xyster_Container_Injection_Factory_AdaptiveTest');
        $suite->addTestSuite('Xyster_Container_Injection_Factory_ConstructorTest');
        $suite->addTestSuite('Xyster_Container_Injection_Factory_MethodTest');
        $suite->addTestSuite('Xyster_Container_Injection_Factory_SetterTest');
        $suite->addTestSuite('Xyster_Container_Parameter_BasicTest');
        $suite->addTestSuite('Xyster_Container_Parameter_ConstantTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Container_AllTests::main') {
    Xyster_Container_AllTests::main();
}
