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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_Orm_Engine_ForeignKeyDirectionTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_Engine_ForeignKeyDirectionTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Orm/Engine/ForeignKeyDirection.php';

/**
 * Test class for Xyster_Orm_Engine_ForeignKeyDirection.
 * Generated by PHPUnit on 2008-09-27 at 18:19:12.
 */
class Xyster_Orm_Engine_ForeignKeyDirectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Orm_Engine_ForeignKeyDirectionTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Tests the 'fromParent' method
     */
    public function testFromParent()
    {
        $enum = Xyster_Orm_Engine_ForeignKeyDirection::FromParent();
        $this->_runTests($enum, 'FromParent', 0);
    }

    /**
     * Tests the 'toParent' method
     */
    public function testToParent()
    {
        $enum = Xyster_Orm_Engine_ForeignKeyDirection::ToParent();
        $this->_runTests($enum, 'ToParent', 1);
    }
    
    /**
     * Runs the unit tests on an enum
     *
     * @param Xyster_Enum $actual
     * @param string $name
     * @param mixed $value
     */
    protected function _runTests( Xyster_Orm_Engine_ForeignKeyDirection $actual, $name, $value )
    {
        $this->assertEquals($name, $actual->getName());
        $this->assertEquals($value, $actual->getValue());
        $this->assertEquals('Xyster_Orm_Engine_ForeignKeyDirection ['.$value.','.$name.']', (string)$actual);
        $this->assertSame($actual, Xyster_Enum::parse('Xyster_Orm_Engine_ForeignKeyDirection', $name));
        $this->assertSame($actual, Xyster_Enum::valueOf('Xyster_Orm_Engine_ForeignKeyDirection', $value));
    }
}

// Call Xyster_Orm_Engine_ForeignKeyDirectionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_Engine_ForeignKeyDirectionTest::main') {
    Xyster_Orm_Engine_ForeignKeyDirectionTest::main();
}
