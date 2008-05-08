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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Call Xyster_Orm_Entity_TypeTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_Entity_TypeTest::main');
}

/**
 * PHPUnit test case
 */
require_once dirname(__FILE__) . '/TestSetup.php';
require_once 'Xyster/Orm/Entity/Type.php';

/**
 * Test for Xyster_Orm_Entity_Field
 *
 */
class Xyster_Orm_Entity_TypeTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Entity_Type
     */
    protected $_meta;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Orm_Entity_TypeTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Sets up the tests
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $map = $this->_mockFactory()->get('MockBug');
        $this->_meta = $map->getEntityMeta();
    }

    
    /**
     * Tests the 'assertValidFieldForClass' method
     *
     */
    public function testValidField()
    {
        $this->_meta->assertValidField('bugDescription');
        // should not throw exception
        
        $this->setExpectedException('Xyster_Orm_Entity_Exception');
        $this->assertFalse($this->_meta->assertValidField('doesntExist'));
    }
    
    /**
     * Tests the 'assertValidField' method with a relation
     *
     * No operations in this method should throw exceptions
     */
    public function testValidFieldRelation()
    {
        $this->_meta->assertValidField('max(reporter->accountName)');
        $this->_meta->assertValidField('products->count()');
    }
    
    /**
     * Tests the 'assertValidField' method with a method call
     *
     */
    public function testValidFieldMethod()
    {
        $this->_meta->assertValidField('getCapitalOfNebraska(1,"test",createdOn)');
        // should not throw exception
        
        $this->setExpectedException('Xyster_Orm_Entity_Exception');
        $this->_meta->assertValidField('nonExistantMethod()');
    }
    
    /**
     * Tests the 'isRuntime' method
     *
     */
    public function testIsRuntime()
    {
    	$this->assertFalse($this->_meta->isRuntime(Xyster_Data_Field::named('MAX(bugId)')));
        $this->assertFalse($this->_meta->isRuntime(Xyster_Data_Field::named('bugDescription')));
        $this->assertFalse($this->_meta->isRuntime(Xyster_Data_Field::named('reporter->accountName')));
        $this->assertFalse($this->_meta->isRuntime(Xyster_Data_Field::named('MAX(reporter->accountName)')));
        
        $this->assertTrue($this->_meta->isRuntime(Xyster_Data_Field::named('reporter->accountName->somePublicField')));
        $this->assertTrue($this->_meta->isRuntime(Xyster_Data_Field::named('assignee->isDirty()')));
        $this->assertTrue($this->_meta->isRuntime(Xyster_Data_Field::named('getCapitalOfNebraska()')));
    }
    
    /**
     * Tests the 'isRuntime' method with criteria
     *
     */
    public function testIsRuntimeCriteria()
    {
        $field = Xyster_Data_Field::named('bugDescription');
        $criteria = Xyster_Data_Junction::all($field->notLike('foo%'),
            $field->notLike('bar%'));
            
        $this->assertFalse($this->_meta->isRuntime($criteria));
        
        $field = Xyster_Data_Field::named('getCapitalOfNebraska()');
        $criteria = Xyster_Data_Junction::all($field->notLike('foo%'),
            $field->notLike('bar%'));
            
        $this->assertTrue($this->_meta->isRuntime($criteria));
    }
    
    /**
     * Tests the 'isRuntime' method with sorts
     *
     */
    public function testIsRuntimeSort()
    {
        require_once 'Xyster/Data/Sort.php';
        
        $this->assertFalse($this->_meta->isRuntime(Xyster_Data_Sort::asc('bugDescription')));
        
        $this->assertTrue($this->_meta->isRuntime(Xyster_Data_Sort::asc('getCapitalOfNebraska()')));
    }
    
    /**
     * Tests the 'isRuntime' method
     *
     */
    public function testIsRuntimeBadType()
    {
    	require_once 'Xyster/Data/Field/Clause.php';
        $this->setExpectedException('Xyster_Orm_Exception');
        $this->_meta->isRuntime(new Xyster_Data_Field_Clause);
    }
    
    /**
     * Tests the 'getEntityName' method
     *
     */
    public function testGetEntityName()
    {
        $this->_meta->getEntityName();
    }
    
    /**
     * Tests the 'getFields' method
     *
     */
    public function testGetFields()
    {
        $fields = $this->_meta->getFields();
        $this->assertType('array', $fields);
        foreach( $fields as $field ) {
            $this->assertType('Xyster_Orm_Entity_Field', $field);
        }
    }
    
    /**
     * Tests the 'getFieldNames' method
     *
     */
    public function testGetFieldNames()
    {
        $names = $this->_meta->getFieldNames();
        $this->assertEquals(array_keys($this->_meta->getFields()), $names);
    }
    
    /**
     * Tests the 'getMapperFactory' method
     *
     */
    public function testGetMapperFactory()
    {
        $this->assertSame($this->_mockFactory(), $this->_meta->getMapperFactory());
    }
    
    /**
     * Tests the 'getMembers' method
     *
     */
    public function testGetMembers()
    {
        $membs = array();
        $membs = array_merge($membs, $this->_meta->getFieldNames());
        $membs = array_merge($membs, $this->_meta->getRelationNames());
        $membs = array_merge($membs, get_class_methods($this->_meta->getEntityName()));
        
        $this->assertEquals($membs, $this->_meta->getMembers());
    }
    
    /**
     * Tests the 'getPrimary' method
     *
     */
    public function testGetPrimary()
    {
        $primary = $this->_meta->getPrimary();
        $this->assertEquals(array('bugId'), $primary);
    }
    
    /**
     * Tests the 'getRelation' method
     *
     */
    public function testGetRelation()
    {
        $rel = $this->_meta->getRelation('reporter');
        $this->assertType('Xyster_Orm_Relation', $rel);
        $this->assertEquals('reporter', $rel->getName());
    }
    
    /**
     * Tests that 'getRelation' throws an exception for a bad name
     *
     */
    public function testGetRelationWithBadName()
    {
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $this->_meta->getRelation('foobar');
    }
    
    /**
     * Tests the 'getRelations' method
     *
     */
    public function testGetRelations()
    {
        $relations = $this->_meta->getRelations();
        $this->assertType('array', $relations);
        foreach( $relations as $rel ) {
            $this->assertType('Xyster_Orm_Relation', $rel);
        }
    }
    
    /**
     * Tests the 'getRelationNames' method
     *
     */
    public function testGetRelationNames()
    {
        $names = $this->_meta->getRelationNames();
        $this->assertType('array', $names);
        $this->assertEquals($names, array_keys($this->_meta->getRelations()));
    }
    
    /**
     * Tests the 'isRelation' method
     *
     */
    public function testIsRelation()
    {
        $this->assertTrue($this->_meta->isRelation('assignee'));
        $this->assertFalse($this->_meta->isRelation('foobar'));
    }
    
    public function testBelongsTo()
    {
        
    }

    public function testHasJoined()
    {
        
    }
    
    public function testHasMany()
    {
        
    }
    
    public function testHasOne()
    {
        $this->_meta->hasOne('reportingAccount', array('class'=>'MockAccount','id'=>'reportedBy'));
    }

    /**
     * Tests the creating an existing relation throws an exception
     *
     */
    public function testCreateExistingRelation()
    {
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $this->_meta->belongsTo('reporter', array('class'=>'MockAccount','id'=>'reportedBy'));
    }
}

// Call Xyster_Orm_Entity_TypeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_Entity_TypeTest::main') {
    Xyster_Orm_Entity_TypeTest::main();
}
