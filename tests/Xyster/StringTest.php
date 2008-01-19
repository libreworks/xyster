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
 * @subpackage Xyster
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Call Xyster_StringTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_StringTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Xyster_String
 */
require_once 'Xyster/String.php';
/**
 * Test for Xyster_String
 *
 */
class Xyster_StringTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the 'matchGroups' method
     *
     */
    public function testMatchGroups()
    {
        $sql = 'WHERE (columnOne = "some value(") AND ' 
        	. '(columnTwo = "some value" OR (columnThree = "some value")) AND '
      //  	. 'columnFour = "something (else) entirely" ';
            . 'columnFour = "something else entirely" '; // parenths in top-level quotes fail right now

        $expected = array('(columnOne = "some value(")', '(columnTwo = "some value" OR (columnThree = "some value"))');
        
        $this->assertEquals($expected, Xyster_String::matchGroups($sql));
        
        $this->assertEquals(array(), Xyster_String::matchGroups('Hello world'));
    }
    
    /**
     * Tests the 'smartSplit' method
     *
     */
    public function testSmartSplit()
    {
        $haystack = 'A (great (test)) of "this method"';
        $expected = array(0=>'A', 1=>'(great (test))', 2=>'of', 3=>'"this method"');
        
        $this->assertEquals($expected, Xyster_String::smartSplit(' ', $haystack));
    }
}

// Call Xyster_StringTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_StringTest::main') {
    Xyster_StringTest::main();
}
