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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_AllTests::main');
}

require_once dirname(dirname(__FILE__)).'/TestHelper.php';
               
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'Xyster/ApplicationTest.php';
require_once 'Xyster/Application/AllTests.php';
require_once 'Xyster/ContainerTest.php';
require_once 'Xyster/Container/AllTests.php';
require_once 'Xyster/Db/AllTests.php';


class Xyster_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster');
        $suite->addTestSuite('Xyster_ApplicationTest');
        $suite->addTest( Xyster_Application_AllTests::suite() );
        $suite->addTestSuite('Xyster_ContainerTest');
        $suite->addTest( Xyster_Container_AllTests::suite() );
        $suite->addTest( Xyster_Db_AllTests::suite() );
        return $suite;
    }
}

if ( PHPUnit_MAIN_METHOD == 'Xyster_AllTests::main' ) {
    Xyster_AllTests::main();
}