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
 * @subpackage Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_Db_TokenTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_TokenTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Xyster_Db_Token
 */
require_once 'Xyster/Db/Token.php';

/**
 * Test for Xyster_Db_Token
 *
 */
class Xyster_Db_TokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_TokenTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests basic operation of the class
     *
     */
    public function testBasic()
    {
        $sql = 'WHERE somethingCool = :value';
        $bind = array('value'=>'Coffee');
        
        $token = new Xyster_Db_Token($sql, $bind);
        $this->assertEquals($sql, $token->getSql());
        $this->assertEquals($bind, $token->getBindValues());
    }
    
    /**
     * Tests the addBindValues method
     *
     */
    public function testAddBindValues()
    {
        $sql = 'WHERE somethingCool = :value';
        $bind = array('value'=>'Coffee');
        $token = new Xyster_Db_Token($sql, $bind);
        
        $sql2 = 'WHERE somethingCool = :value AND somethingBogus = :value2';
        $bind2 = array('value2'=>'Decaf');
        $token2 = new Xyster_Db_Token($sql2, $bind2);
        
        $token2->addBindValues($token);
        
        $combined = array_merge($bind, $bind2);
        $this->assertEquals($combined, $token2->getBindValues());
    }
}

// Call Xyster_Db_TokenTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_TokenTest::main') {
    Xyster_Db_TokenTest::main();
}
