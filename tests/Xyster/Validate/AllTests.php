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
 * @subpackage Xyster_Validate
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Validate_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Validate/ErrorTest.php';
require_once 'Xyster/Validate/ErrorsTest.php';
require_once 'Xyster/Validate/IntTest.php';
require_once 'Xyster/Validate/NotNullTest.php';
require_once 'Xyster/Validate/UriTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Validate_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Validate');
        $suite->addTestSuite('Xyster_Validate_ErrorTest');
        $suite->addTestSuite('Xyster_Validate_ErrorsTest');
        $suite->addTestSuite('Xyster_Validate_IntTest');
        $suite->addTestSuite('Xyster_Validate_NotNullTest');
        $suite->addTestSuite('Xyster_Validate_UriTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Validate_AllTests::main') {
    Xyster_Validate_AllTests::main();
}
