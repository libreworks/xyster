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
 * @subpackage Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_Db_ReferentialActionTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_ReferentialActionTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Db/ReferentialAction.php';

/**
 * Test class for Xyster_Db_ReferentialAction.
 * Generated by PHPUnit on 2008-03-12 at 20:28:24.
 */
class Xyster_Db_ReferentialActionTest extends PHPUnit_Framework_TestCase
{
	static protected $_actions = array(
	       'Cascade', 'Restrict', 'NoAction', 'SetNull', 'SetDefault'
	   );
	   
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_ReferentialActionTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests the datatype enum methods
     *
     */
    public function testMain()
    {
    	foreach( self::$_actions as $value => $name ) {
            $enum = Xyster_Enum::valueOf('Xyster_Db_ReferentialAction', $value);
            $this->_runTests($enum, $name, $value);
    	}
    }

    /**
     * Runs the unit tests on an enum
     *
     * @param Xyster_Enum $actual
     * @param string $name
     * @param mixed $value
     */
    protected function _runTests( Xyster_Db_ReferentialAction $actual, $name, $value )
    {
        $this->assertEquals($name, $actual->getName());
        $this->assertEquals($value, $actual->getValue());
        $this->assertEquals('Xyster_Db_ReferentialAction ['.$value.','.$name.']', (string)$actual);
        $this->assertSame($actual, Xyster_Enum::parse('Xyster_Db_ReferentialAction', $name));
        $this->assertSame($actual, Xyster_Enum::valueOf('Xyster_Db_ReferentialAction', $value));
    }
}

// Call Xyster_Db_ReferentialActionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_ReferentialActionTest::main') {
    Xyster_Db_ReferentialActionTest::main();
}
