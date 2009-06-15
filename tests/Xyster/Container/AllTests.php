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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
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

require_once 'Xyster/Container/AutowireTest.php';
require_once 'Xyster/Container/DefinitionTest.php';
require_once 'Xyster/Container/Injector/AutowiringCdiTest.php';
require_once 'Xyster/Container/Injector/AutowiringSdiTest.php';
require_once 'Xyster/Container/Injector/StandardTest.php';
require_once 'Xyster/Container/Injector/StandardSdiTest.php';
require_once 'Xyster/Container/Provider/CachingTest.php';
require_once 'Xyster/Container/Provider/DelegateTest.php';

class Xyster_Container_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Container');

        $suite->addTestSuite('Xyster_Container_AutowireTest');
        $suite->addTestSuite('Xyster_Container_DefinitionTest');
        $suite->addTestSuite('Xyster_Container_Injector_AutowiringCdiTest');
        $suite->addTestSuite('Xyster_Container_Injector_AutowiringSdiTest');
        $suite->addTestSuite('Xyster_Container_Injector_StandardTest');
        $suite->addTestSuite('Xyster_Container_Injector_StandardSdiTest');
        $suite->addTestSuite('Xyster_Container_Provider_CachingTest');
        $suite->addTestSuite('Xyster_Container_Provider_DelegateTest');
    
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Container_AllTests::main') {
    Xyster_Container_AllTests::main();
}
