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
// Call Xyster_Db_Gateway_TableBuilder_ColumnTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_Gateway_TableBuilder_ColumnTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Db/Gateway/TableBuilder/Column.php';
require_once 'Xyster/Db/Gateway/DataType.php';
require_once 'Xyster/Db/Gateway/ReferentialAction.php';

/**
 * Test class for Xyster_Db_Gateway_TableBuilder_Column.
 * Generated by PHPUnit on 2008-03-18 at 12:33:56.
 */
class Xyster_Db_Gateway_TableBuilder_ColumnTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Db_Gateway_TableBuilder_Column
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_Gateway_TableBuilder_ColumnTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Xyster_Db_Gateway_TableBuilder_Column('example_column',
            Xyster_Db_Gateway_DataType::Varchar(), 255);
    }

    /**
     * Tests the 'getArgument' method
     */
    public function testGetArgument()
    {
        $this->assertEquals(255, $this->object->getArgument());
    }

    /**
     * Tests the 'getName' method
     */
    public function testGetName()
    {
        $this->assertEquals('example_column', $this->object->getName());
    }

    /**
     * Tests the 'getDataType' method
     */
    public function testGetDataType()
    {
        $this->assertSame(Xyster_Db_Gateway_DataType::Varchar(), $this->object->getDataType());
    }

    /**
     * Tests the 'getDefault' method
     */
    public function testGetDefault()
    {
        $this->object->defaultValue(1234);
        $this->assertEquals(1234, $this->object->getDefault());
    }

    /**
     * Tests the foreign key capabilities
     */
    public function testForeignKey()
    {
        $table = 'my_other_table';
        $column = 'my_other_table_id';
        $onDelete = Xyster_Db_Gateway_ReferentialAction::SetNull();
        $onUpdate = Xyster_Db_Gateway_ReferentialAction::Cascade();
        $this->object->foreign($table, $column, $onDelete, $onUpdate);
        $this->assertEquals($table, $this->object->getForeignKeyTable());
        $this->assertEquals($column, $this->object->getForeignKeyColumn());
        $this->assertSame($onDelete, $this->object->getForeignKeyOnDelete());
        $this->assertSame($onUpdate, $this->object->getForeignKeyOnUpdate());
    }

    /**
     * Tests the 'primary' method
     */
    public function testIsPrimary()
    {
        $this->assertFalse($this->object->isPrimary());
        $this->object->primary();
        $this->assertTrue($this->object->isPrimary());
    }
    
    /**
     * Tests the 'isNull' method
     *
     */
    public function testIsNull()
    {
    	$this->assertTrue($this->object->isNull());
        $this->object->null(false);
        $this->assertFalse($this->object->isNull());
        $this->object->null();
        $this->assertTrue($this->object->isNull());
    }

    /**
     * Tests the 'isUnique' method
     */
    public function testIsUnique()
    {
        $this->assertFalse($this->object->isUnique());
        $this->object->unique();
        $this->assertTrue($this->object->isUnique());
    }
}

// Call Xyster_Db_Gateway_TableBuilder_ColumnTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_Gateway_TableBuilder_ColumnTest::main') {
    Xyster_Db_Gateway_TableBuilder_ColumnTest::main();
}
