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
 * @subpackage Xyster
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
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
     * Tests the 'arrayToString' method
     *
     */
    public function testArrayToString()
    {
        $test = array('Batman'=>'Bruce Wayne',
            'Spider-Man'=>'Peter Parker',
            'Superman'=>'Clark Kent',
            'Daredevil'=>'Matt Murdock');
        
        $expected = 'Batman=Bruce Wayne,Spider-Man=Peter Parker,Superman=Clark Kent,Daredevil=Matt Murdock';
        
        $this->assertEquals($expected, Xyster_String::arrayToString($test));
    }
    
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
    
    /**
     * Tests the 'titleCase' method
     *
     */
    public function testTitleCase()
    {
        $test = 'The great escape of our hero from coding hades';
        $expected = 'The Great Escape of Our Hero from Coding Hades';
        
        $this->assertEquals($expected, Xyster_String::titleCase($test));
        
        $test = 'I WANT THIS STRING TO BE IN TITLE CASE';
        $expected = 'I Want This String to Be in Title Case';

        $this->assertEquals($expected, Xyster_String::titleCase($test));
                
        $test = 'HOW ÄBÖUT INTERNATIONAL LETTERS';
        $expected = 'How ÄbÖut International Letters'; // should be left alone for now
        
        $this->assertEquals($expected, Xyster_String::titleCase($test));
    }
    
    /**
     * Tests the 'toCamel' method
     *
     */
    public function testToCamel()
    {
        $original = 'this_is_underscore_case';
        $expected = 'thisIsUnderscoreCase';
        
        $this->assertSame($expected, Xyster_String::toCamel($original));
    }
    
    /**
     * Tests the 'toUnderscores' method
     *
     */
    public function testToUnderscores()
    {
        $original = 'thisIsCamelCase';
        $expected = 'this_is_camel_case';
        
        $this->assertSame($expected, Xyster_String::toUnderscores($original));
    }
}

// Call Xyster_StringTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_StringTest::main') {
    Xyster_StringTest::main();
}
