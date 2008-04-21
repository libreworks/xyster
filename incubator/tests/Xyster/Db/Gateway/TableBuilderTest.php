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
// Call Xyster_Db_Gateway_TableBuilderTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_Gateway_TableBuilderTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Db/Gateway/Stub.php';
require_once 'Xyster/Db/Gateway/DataType.php';
require_once 'Xyster/Db/Gateway/TableBuilder.php';
require_once 'Xyster/Db/Gateway/TableBuilder/Column.php';
require_once 'Xyster/Db/Gateway/TableBuilder/ForeignKey.php';
require_once 'Xyster/Db/Gateway/TableBuilder/Unique.php';
require_once 'Xyster/Db/Gateway/TableBuilder/PrimaryKey.php';

/**
 * Test class for Xyster_Db_Gateway_TableBuilder.
 * Generated by PHPUnit on 2008-03-17 at 19:04:52.
 */
class Xyster_Db_Gateway_TableBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Db_Gateway_TableBuilder
     */
    protected $object;

    /**
     * @var Xyster_Db_Gateway_Stub
     */
    protected $gateway;
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_Gateway_TableBuilderTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
    	$this->gateway = new Xyster_Db_Gateway_Stub;
        $this->object = new Xyster_Db_Gateway_TableBuilder('my_table', $this->gateway);
    }

    /**
     * Tests the 'addVarchar' method
     */
    public function testAddVarchar()
    {
        $return = $this->object->addVarchar('disposition', 255);
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Varchar(), $column->getDataType());
        $this->assertEquals('disposition', $column->getName());
        $this->assertEquals('255', $column->getArgument());
    }

    /**
     * Tests the 'addChar' method
     */
    public function testAddChar()
    {
        $return = $this->object->addChar('juxtaposition', 255);
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Char(), $column->getDataType());
        $this->assertEquals('juxtaposition', $column->getName());
        $this->assertEquals(255, $column->getArgument());
    }

    /**
     * Tests the 'addInteger' method
     */
    public function testAddInteger()
    {
        $return = $this->object->addInteger('chronological_offset');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Integer(), $column->getDataType());
        $this->assertEquals('chronological_offset', $column->getName());        
    }

    /**
     * Tests the 'addSmallint' method
     */
    public function testAddSmallint()
    {
        $return = $this->object->addSmallint('occupancy');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Smallint(), $column->getDataType());
        $this->assertEquals('occupancy', $column->getName());
    }

    /**
     * Tests the 'addFloat' method
     */
    public function testAddFloat()
    {
        $return = $this->object->addFloat('interest_compounded');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Float(), $column->getDataType());
        $this->assertEquals('interest_compounded', $column->getName());
    }

    /**
     * Tests the 'addTimestamp' method
     */
    public function testAddTimestamp()
    {
        $return = $this->object->addTimestamp('subjugated_on');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Timestamp(), $column->getDataType());
        $this->assertEquals('subjugated_on', $column->getName());        
    }

    /**
     * Tests the 'addDate' method
     */
    public function testAddDate()
    {
        $return = $this->object->addDate('anniversary');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Date(), $column->getDataType());
        $this->assertEquals('anniversary', $column->getName());
    }

    /**
     * Tests the 'addTime' method
     */
    public function testAddTime()
    {
        $return = $this->object->addTime('time_on_our_hands');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Time(), $column->getDataType());
        $this->assertEquals('time_on_our_hands', $column->getName());        
    }

    /**
     * Tests the 'addClob' method
     */
    public function testAddClob()
    {
        $return = $this->object->addClob('senior_thesis');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Clob(), $column->getDataType());
        $this->assertEquals('senior_thesis', $column->getName());
    }

    /**
     * Tests the 'addBlob' method
     */
    public function testAddBlob()
    {
        $return = $this->object->addBlob('encrypted_spatula');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Blob(), $column->getDataType());
        $this->assertEquals('encrypted_spatula', $column->getName());        
    }

    /**
     * Tests the 'addBoolean' method
     */
    public function testAddBoolean()
    {
        $return = $this->object->addBoolean('bewildering_confirmation');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Boolean(), $column->getDataType());
        $this->assertEquals('bewildering_confirmation', $column->getName());        
    }

    /**
     * Tests the 'addIdentity' method
     */
    public function testAddIdentity()
    {
        $return = $this->object->addIdentity('mistaken_identity');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertSame(Xyster_Db_Gateway_DataType::Identity(), $column->getDataType());
        $this->assertEquals('mistaken_identity', $column->getName());        
    }

    /**
     * Tests the 'defaultValue' method
     */
    public function testDefaultValue()
    {
        $return = $this->object->addVarchar('example_column', 50)->defaultValue('Lebowski');
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertEquals('Lebowski', $column->getDefault());
    }
    
    /**
     * Tests the 'defaultValue' method with no column being defined yet
     */
    public function testDefaultValueNoColumnYet()
    {
        $this->setExpectedException('Xyster_Db_Gateway_TableBuilder_Exception');
        $this->object->defaultValue(1);
    }
    
    /**
     * @todo Implement testExecute().
     */
    public function testExecute()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * Tests the 'foreign' method
     */
    public function testForeign()
    {
    	$table = 'some_foreign';
    	$columnName = 'some_foreign_uid';
    	$onDelete = null;
    	$onUpdate = null;
        $return = $this->object->addInteger('some_foreign_id')->foreign($table, $columnName, $onDelete, $onUpdate);
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertEquals($table, $column->getForeignKeyTable());
        $this->assertEquals($columnName, $column->getForeignKeyColumn());
        $this->assertEquals($onDelete, $column->getForeignKeyOnDelete());
        $this->assertEquals($onUpdate, $column->getForeignKeyOnUpdate());
    }
    
    /**
     * Tests the 'foreign' method with no column being defined yet
     */
    public function testForeignNoColumnYet()
    {
        $this->setExpectedException('Xyster_Db_Gateway_TableBuilder_Exception');
        $this->object->foreign('anothertable', 'column');
    }
    
    /**
     * Tests the 'foreignMulti' method
     */
    public function testForeignMulti()
    {
    	$table = 'another_table';
    	$cols = array('another_name', 'another_city');
    	$onDelete = null;
    	$onUpdate = null;
        $this->object->addVarchar('name', 50)->addVarchar('city', 50);
        $return = $this->object->foreignMulti(array('name', 'city'), $table, $cols, $onDelete, $onUpdate);
        $this->assertSame($this->object, $return);
        $fkey = current($this->object->getForeignKeys());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_ForeignKey', $fkey);
        $this->assertEquals(array('name', 'city'), $fkey->getColumns());
        $this->assertEquals($table, $fkey->getTable());
        $this->assertEquals($cols, $fkey->getForeignColumns());
        $this->assertEquals($onDelete, $fkey->getOnDelete());
        $this->assertEquals($onUpdate, $fkey->getOnUpdate());
    }
    
    /**
     * Tests the 'getName' method
     *
     */
    public function testGetName()
    {
    	$this->assertEquals('my_table', $this->object->getName());
    }
    
    /**
     * Tests the 'index' method
     */
    public function testIndex()
    {
        $return = $this->object->addVarchar('example_column', 50)->index('myIndexName', false);
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $index = current($this->object->getIndexes());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Index', $index);
        $this->assertEquals(array('example_column'), $index->getColumns());
        $this->assertEquals('myIndexName', $index->getName());
        $this->assertFalse($index->isFulltext());
    }
    
    /**
     * Tests the 'index' method with a fulltext flag
     */
    public function testIndexFulltext()
    {
        $return = $this->object->addVarchar('example_column', 50)->index('myIndexName', true);
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $index = current($this->object->getIndexes());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Index', $index);
        $this->assertEquals(array('example_column'), $index->getColumns());
        $this->assertEquals('myIndexName', $index->getName());
        $this->assertTrue($index->isFulltext());
    }
    
    /**
     * Tests the 'index' method with no column being defined yet
     */
    public function testIndexNoColumnYet()
    {
        $this->setExpectedException('Xyster_Db_Gateway_TableBuilder_Exception');
        $this->object->index();
    }
    
    /**
     * Tests the 'indexMult' method
     */
    public function testIndexMulti()
    {
        $this->object->addVarchar('name', 50)->addVarchar('city', 50);
        $return = $this->object->indexMulti(array('name', 'city'), 'MyIndexName');
        $this->assertSame($this->object, $return);
        $index = current($this->object->getIndexes());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Index', $index);
        $this->assertEquals(array('name', 'city'), $index->getColumns());
        $this->assertEquals('MyIndexName', $index->getName());
    }

    /**
     * Tests the 'null' method
     */
    public function testNull()
    {
        $return = $this->object->addVarchar('example_column', 50)->null();
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertTrue($column->isNull());
    }

    /**
     * Tests the 'null' method with a false flag
     */
    public function testNullNot()
    {
        $return = $this->object->addVarchar('example_column', 50)->null(false);
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertFalse($column->isNull());
    }
    
    /**
     * Tests the 'null' method with no column being defined yet
     */
    public function testNullNoColumnYet()
    {
        $this->setExpectedException('Xyster_Db_Gateway_TableBuilder_Exception');
        $this->object->null();
    }
    
    /**
     * Tests the 'primary' method
     */
    public function testPrimary()
    {
        $return = $this->object->addInteger('example_id', 50)->primary();
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertTrue($column->isPrimary());
    }
    
    /**
     * Tests the 'primary' method with no column being defined yet
     */
    public function testPrimaryNoColumnYet()
    {
        $this->setExpectedException('Xyster_Db_Gateway_TableBuilder_Exception');
        $this->object->primary();
    }
    
    /**
     * Tests the 'primaryMulti' method
     */
    public function testPrimaryMulti()
    {
        $this->object->addVarchar('name', 50)->addVarchar('city', 50);
        $return = $this->object->primaryMulti(array('name', 'city'));
        $this->assertSame($this->object, $return);
        $primary = $this->object->getPrimaryKey();
        $this->assertType('Xyster_Db_Gateway_TableBuilder_PrimaryKey', $primary);
        $this->assertEquals(array('name', 'city'), $primary->getColumns());
    }

    /**
     * Tests the 'unique' method
     */
    public function testUnique()
    {
        $return = $this->object->addVarchar('example_column', 50)->unique();
        $this->assertSame($this->object, $return);
        $column = current($this->object->getColumns());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Column', $column);
        $this->assertTrue($column->isUnique());
    }
    
    /**
     * Tests the 'unique' method with no column being defined yet
     */
    public function testUniqueNoColumnYet()
    {
        $this->setExpectedException('Xyster_Db_Gateway_TableBuilder_Exception');
        $this->object->unique();
    }

    /**
     * Tests the 'uniqueMulti' method
     */
    public function testUniqueMulti()
    {
        $this->object->addVarchar('name', 50)->addVarchar('city', 50);
        $return = $this->object->uniqueMulti(array('name', 'city'));
        $this->assertSame($this->object, $return);
        $unique = current($this->object->getUniques());
        $this->assertType('Xyster_Db_Gateway_TableBuilder_Unique', $unique);
        $this->assertEquals(array('name', 'city'), $unique->getColumns());
    }
}

// Call Xyster_Db_Gateway_TableBuilderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_Gateway_TableBuilderTest::main') {
    Xyster_Db_Gateway_TableBuilderTest::main();
}