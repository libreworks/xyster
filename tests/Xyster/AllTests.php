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
/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)).'/TestHelper.php';
require_once 'Xyster/AclTest.php';
require_once 'Xyster/Acl/AllTests.php';
require_once 'Xyster/Collection/AllTests.php';
require_once 'Xyster/ContainerTest.php';
require_once 'Xyster/Container/AllTests.php';
require_once 'Xyster/Controller/AllTests.php';
require_once 'Xyster/Data/AllTests.php';
require_once 'Xyster/Date/RangeTest.php';
require_once 'Xyster/Db/AllTests.php';
require_once 'Xyster/EnumTest.php';
require_once 'Xyster/Filter/TitleCaseTest.php';
require_once 'Xyster/OrmTest.php';
require_once 'Xyster/Orm/AllTests.php';
require_once 'Xyster/TypeTest.php';
require_once 'Xyster/Type/AllTests.php';
require_once 'Xyster/ValidateTest.php';
require_once 'Xyster/Validate/AllTests.php';

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
        $suite->addTestSuite('Xyster_ContainerTest');
        $suite->addTest( Xyster_Container_AllTests::suite() );
        $suite->addTest( Xyster_Controller_AllTests::suite() );
        $suite->addTestSuite('Xyster_EnumTest');
        $suite->addTestSuite('Xyster_Date_RangeTest');
        $suite->addTest( Xyster_Data_AllTests::suite() );
        $suite->addTest( Xyster_Db_AllTests::suite() );
        $suite->addTestSuite('Xyster_Filter_TitleCaseTest');
        $suite->addTestSuite('Xyster_OrmTest');
        $suite->addTest( Xyster_Orm_AllTests::suite() );
        $suite->addTestSuite('Xyster_TypeTest');
        $suite->addTest( Xyster_Type_AllTests::suite() );
        $suite->addTestSuite('Xyster_ValidateTest');
        $suite->addTest( Xyster_Validate_AllTests::suite() );
        return $suite;
    }
}

if ( PHPUnit_MAIN_METHOD == 'Xyster_AllTests::main' ) {
    Xyster_AllTests::main();
}