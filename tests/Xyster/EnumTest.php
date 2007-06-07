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

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * @see Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Test for Xyster_Enum
 *
 */
class Xyster_EnumTest extends PHPUnit_Framework_TestCase
{
    public function testEnums()
    {
        $names = array('Posix','Windows','Mac');
        $this->assertEquals( $names, Xyster_Enum::values('TestEnum') );
        foreach( $names as $value => $name ) {
            $this->_runTests( TestEnum::$name(), $name, $value );
        }
    }
    public function testClone()
    {
        $posix = TestEnum::Posix();
        try { 
            $linux = clone $posix;
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Unthrown exception when cloning enum");
    }
    public function testValues()
    {
        try {
            $values = Xyster_Enum::values('NotTestEnum');
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Unthrown exception when getting values for unknown class");
    }
    public function testBadParse()
    {
        try {
            $enum = Xyster_Enum::parse('TestEnum','Invalid');
        } catch ( Exception $thrown ) {
            return; 
        }
        $this->fail("Unthrown exception when parsing invalid name");
    }
    public function testBadValueOf()
    {
        try {
            $enum = Xyster_Enum::valueOf('TestEnum',4);
        } catch ( Exception $thrown ) {
            return; 
        }
        $this->fail("Unthrown exception when parsing invalid value");        
    }

    protected function _runTests( $actual, $name, $value )
    {
        $this->assertEquals($name,$actual->getName());
        $this->assertEquals($value,$actual->getValue());
        $this->assertEquals('TestEnum ['.$value.','.$name.']',(string)$actual);
        $this->assertEquals($actual,Xyster_Enum::parse('TestEnum',$name));
        $this->assertEquals($actual,Xyster_Enum::valueOf('TestEnum',$value));
    }
}

class NotTestEnum { }

class TestEnum extends Xyster_Enum
{
	const Posix = 0;
	const Windows = 1;
	const Mac = 2;

	/**
	 * @return TestEnum
	 */
	static public function Posix() { return Xyster_Enum::_factory(); }
	/**
	 * @return TestEnum
	 */
	static public function Windows() { return Xyster_Enum::_factory(); }
	/**
	 * @return TestEnum
	 */
	static public function Mac() { return Xyster_Enum::_factory(); }    
}