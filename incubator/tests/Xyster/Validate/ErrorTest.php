<?php
// Call Xyster_Validate_ErrorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Validate_ErrorTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Xyster/Validate/Error.php';

/**
 * Test class for Xyster_Validate_Error.
 * Generated by PHPUnit on 2007-09-11 at 19:28:58.
 */
class Xyster_Validate_ErrorTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Validate_ErrorTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Tests the basic operation of the class
     *
     */
    public function testBasic()
    {
        $error = new Xyster_Validate_Error('This is my message', 'myField');
        
        $this->assertEquals('myField', $error->getField());
        $this->assertEquals('This is my message', $error->getMessage());
        $this->assertEquals('This is my message', (string)$error);
    }
}

// Call Xyster_Validate_ErrorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Validate_ErrorTest::main') {
    Xyster_Validate_ErrorTest::main();
}