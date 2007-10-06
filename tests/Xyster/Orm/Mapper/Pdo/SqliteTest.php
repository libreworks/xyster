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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Call Xyster_Orm_Mapper_Pdo_SqliteTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_Mapper_Pdo_SqliteTest::main');
}

/**
 * @see Xyster_Orm_Mapper_TestCommon
 */
require_once dirname(dirname(__FILE__)) . '/TestCommon.php';

/**
 * A Pdo_Sqlite tester
 *
 */
class Xyster_Orm_Mapper_Pdo_SqliteTest extends Xyster_Orm_Mapper_TestCommon
{
    /**
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Gets the name of the driver
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Sqlite';
    }
}

// Call Xyster_Orm_Mapper_Pdo_SqliteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_Mapper_Pdo_SqliteTest::main') {
    Xyster_Orm_Mapper_Pdo_SqliteTest::main();
}