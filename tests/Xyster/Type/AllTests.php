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
 * @package   Xyster_Type
 * @subpackage UnitTests
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Type_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

error_reporting(E_ALL | E_STRICT);
require_once 'Xyster/Type/Property/DirectTest.php';
require_once 'Xyster/Type/Property/MapTest.php';
require_once 'Xyster/Type/Property/MethodTest.php';
require_once 'Xyster/Type/Proxy/BuilderTest.php';

class Xyster_Type_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Type');
        $suite->addTestSuite('Xyster_Type_Property_DirectTest');
        $suite->addTestSuite('Xyster_Type_Property_MapTest');
        $suite->addTestSuite('Xyster_Type_Property_MethodTest');
        $suite->addTestSuite('Xyster_Type_Proxy_BuilderTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Type_AllTests::main') {
    Xyster_Type_AllTests::main();
}
