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
 * @subpackage Xyster_Enum
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Enum;
use Xyster\Enum\Enum;
/**
 * Test for Xyster_Enum
 *
 */
class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests basic operation
     *
     */
    public function testEnums()
    {
        $names = array('Posix','Windows','Mac');
        $this->assertEquals($names, Enum::values('\XysterTest\Enum\TestEnum'));
        foreach( $names as $value => $name ) {
            $this->_runTests(TestEnum::$name(), $name, $value);
        }
    }
    
    /**
     * Tests no cloning
     *
     * @expectedException \Exception
     */
    public function testClone()
    {
        $posix = TestEnum::Posix();
        $linux = clone $posix;
    }
    
    /**
     * Tests the values method
     * @expectedException \Exception
     */
    public function testValues()
    {
        $values = Enum::values('\XysterTest\Enum\NotTestEnum');
    }
    
    /**
     * Tests a bad parse
     * @expectedException \Exception
     */
    public function testBadParse()
    {
        $enum = Enum::parse('\XysterTest\Enum\TestEnum', 'Invalid');
    }
    
    /**
     * Tests a bad valueOf
     * @expectedException \Exception
     */
    public function testBadValueOf()
    {
        $enum = Enum::valueOf('\XysterTest\Enum\TestEnum', 4);
    }

    /**
     * Runs basic tests
     *
     * @param Xyster_Enum $actual
     * @param string $name
     * @param mixed $value
     */
    protected function _runTests( $actual, $name, $value )
    {
        $this->assertEquals($name, $actual->getName());
        $this->assertEquals($value, $actual->getValue());
        $this->assertEquals('XysterTest\Enum\TestEnum [' . $value . ',' . $name . ']', (string)$actual);
        $this->assertSame($actual, Enum::parse('\XysterTest\Enum\TestEnum', $name));
        $this->assertSame($actual, Enum::valueOf('\XysterTest\Enum\TestEnum', $value));
    }
}

class NotTestEnum { }

class TestEnum extends Enum
{
	const Posix = 0;
	const Windows = 1;
	const Mac = 2;

	/**
	 * @return TestEnum
	 */
	static public function Posix() { return Enum::_factory(); }
	/**
	 * @return TestEnum
	 */
	static public function Windows() { return Enum::_factory(); }
	/**
	 * @return TestEnum
	 */
	static public function Mac() { return Enum::_factory(); }    
}