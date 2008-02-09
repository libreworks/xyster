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
 * @subpackage Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Collection_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Xyster/Collection/CollectionTest.php';
require_once 'Xyster/Collection/SetTest.php';
require_once 'Xyster/Collection/SortableSetTest.php';
require_once 'Xyster/Collection/ListTest.php';
require_once 'Xyster/Collection/EmptyTest.php';
require_once 'Xyster/Collection/MapTest.php';
require_once 'Xyster/Collection/MapStringTest.php';
require_once 'Xyster/Collection/IteratorTest.php';
require_once 'Xyster/Collection/ImmutableCollectionTest.php';
require_once 'Xyster/Collection/ImmutableSetTest.php';
require_once 'Xyster/Collection/ImmutableListTest.php';
require_once 'Xyster/Collection/ImmutableMapTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Collection_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Collection');
        $suite->addTestSuite('Xyster_Collection_CollectionTest');
        $suite->addTestSuite('Xyster_Collection_SetTest');
        $suite->addTestSuite('Xyster_Collection_SortableSetTest');
        $suite->addTestSuite('Xyster_Collection_ListTest');
        $suite->addTestSuite('Xyster_Collection_List_EmptyTest');
        $suite->addTestSuite('Xyster_Collection_MapTest');
        $suite->addTestSuite('Xyster_Collection_MapStringTest');
        $suite->addTestSuite('Xyster_Collection_IteratorTest');
        $suite->addTestSuite('Xyster_Collection_ImmutableCollectionTest');
        $suite->addTestSuite('Xyster_Collection_ImmutableSetTest');
        $suite->addTestSuite('Xyster_Collection_ImmutableListTest');
        $suite->addTestSuite('Xyster_Collection_ImmutableMapTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Collection_AllTests::main') {
    Xyster_Collection_AllTests::main();
}
