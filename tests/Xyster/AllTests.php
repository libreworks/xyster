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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)).'/TestHelper.php';
               
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Xyster/AclTest.php';
require_once 'Xyster/Acl/AllTests.php';
require_once 'Xyster/Collection/AllTests.php';
require_once 'Xyster/Controller/AllTests.php';
require_once 'Xyster/Data/AllTests.php';
require_once 'Xyster/Db/AllTests.php';
require_once 'Xyster/EnumTest.php';
require_once 'Xyster/OrmTest.php';
require_once 'Xyster/Orm/AllTests.php';
require_once 'Xyster/StringTest.php';

class Xyster_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster');
        $suite->addTestSuite('Xyster_AclTest');
        $suite->addTest( Xyster_Acl_AllTests::suite() );
        $suite->addTest( Xyster_Collection_AllTests::suite() );
        $suite->addTest( Xyster_Controller_AllTests::suite() );
        $suite->addTestSuite('Xyster_EnumTest');
        $suite->addTest( Xyster_Data_AllTests::suite() );
        $suite->addTest( Xyster_Db_AllTests::suite() );
        $suite->addTestSuite('Xyster_OrmTest');
        $suite->addTest( Xyster_Orm_AllTests::suite() );
        $suite->addTestSuite('Xyster_StringTest');
        return $suite;
    }
}

if ( PHPUnit_MAIN_METHOD == 'Xyster_AllTests::main' ) {
    Xyster_AllTests::main();
}