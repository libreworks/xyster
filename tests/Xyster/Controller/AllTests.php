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
 * @package   Xyster_Controller
 * @subpackage UnitTests
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Controller_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting(E_ALL | E_STRICT);

require_once 'Xyster/Controller/Action/Helper/CacheTest.php';
require_once 'Xyster/Controller/Action/Helper/FileTest.php';
require_once 'Xyster/Controller/Plugin/AclTest.php';
require_once 'Xyster/Controller/Plugin/AuthTest.php';
require_once 'Xyster/Controller/Plugin/CacheTest.php';
require_once 'Xyster/Controller/Request/ResourceTest.php';

class Xyster_Controller_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Controller');
        $suite->addTestSuite('Xyster_Controller_Action_Helper_CacheTest');
        $suite->addTestSuite('Xyster_Controller_Action_Helper_FileTest');
        $suite->addTestSuite('Xyster_Controller_Plugin_AclTest');
        $suite->addTestSuite('Xyster_Controller_Plugin_AuthTest');
        $suite->addTestSuite('Xyster_Controller_Plugin_CacheTest');
        $suite->addTestSuite('Xyster_Controller_Request_ResourceTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Controller_AllTests::main') {
    Xyster_Controller_AllTests::main();
}
