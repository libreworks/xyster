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
// Call Xyster_Db_ColumnTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_ColumnTest::main');
}
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Db/Column.php';
require_once 'Xyster/Db/DataType.php';

/**
 * Test class for Xyster_Db_Column.
 * Generated by PHPUnit on 2008-07-08 at 21:55:10.
 */
class Xyster_Db_ColumnTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Db_Column
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_ColumnTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Xyster_Db_Column('Foobar');
    }

    /**
     * Tests the 'equals' method
     */
    public function testEquals()
    {
        $this->assertTrue($this->object->equals($this->object));
        $column2 = new Xyster_Db_Column('foobar');
        $this->assertTrue($this->object->equals($column2));
        $column3 = new Xyster_Db_Column('loremipsum');
        $this->assertFalse($this->object->equals($column3));
    }

    /**
     * Tests the 'getDefaultValue' and 'setDefaultValue' methods
     */
    public function testGetAndSetDefaultValue()
    {
        $this->assertSame($this->object, $this->object->setDefaultValue('test'));
        $this->assertEquals('test', $this->object->getDefaultValue());
    }

    /**
     * Tests the 'getLength' and 'setLength' methods
     */
    public function testGetLength()
    {
        $this->assertSame($this->object, $this->object->setLength(255));
        $this->assertEquals(255, $this->object->getLength());
    }

    /**
     * Tests the 'getName' and 'setName' methods
     */
    public function testGetAndSetName()
    {
        $this->assertSame($this->object, $this->object->setName('loremipsum'));
        $this->assertEquals('loremipsum', $this->object->getName());
    }

    /**
     * Tests the 'getPrecision' and 'setPrecision' methods
     */
    public function testGetAndSetPrecision()
    {
        $this->assertSame($this->object, $this->object->setPrecision(2));
        $this->assertEquals(2, $this->object->getPrecision());
    }

    /**
     * Tests the 'getScale' and 'setScale' methods
     */
    public function testGetAndSetScale()
    {
        $this->assertSame($this->object, $this->object->setScale(4));
        $this->assertEquals(4, $this->object->getScale());
    }

    /**
     * Tests the 'getType' and 'setType' methods
     */
    public function testGetAndSetType()
    {
        $type = Xyster_Db_DataType::Timestamp();
        $this->assertSame($this->object, $this->object->setType($type));
        $this->assertSame($type, $this->object->getType());
    }

    /**
     * Tests the 'hashCode' method
     */
    public function testHashCode()
    {
        $hash = Xyster_Type::hash('foobar');
        $this->assertEquals($hash, $this->object->hashCode());
    }
    
    /**
     * Tests the 'isNullable' and 'setNullable' methods
     */
    public function testIsAndSetNullable()
    {
        $this->assertSame($this->object, $this->object->setNullable());
        $this->assertTrue($this->object->isNullable());
        $this->object->setNullable(false);
        $this->assertFalse($this->object->isNullable());
    }

    /**
     * Tests the 'isUnique' and 'setUnique' methods
     */
    public function testIsAndSetUnique() {
        $this->assertSame($this->object, $this->object->setUnique());
        $this->assertTrue($this->object->isUnique());
        $this->object->setUnique(false);
        $this->assertFalse($this->object->isUnique());
    }

    /**
     * Tests the '__toString()' method
     */
    public function test__toString()
    {
        $this->assertEquals('Foobar', $this->object->__toString());
    }
}

// Call Xyster_Db_ColumnTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_ColumnTest::main') {
    Xyster_Db_ColumnTest::main();
}