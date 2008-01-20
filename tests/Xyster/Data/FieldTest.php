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

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * @see Xyster_Data_Aggregate
 */
require_once 'Xyster/Data/Aggregate.php';
/**
 * Test for Xyster_Data_Field
 *
 */
class Xyster_Data_FieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Data_Field
     */
    protected $_commonField;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        $this->_commonField = Xyster_Data_Field::named('username');
    }
    
    /**
     * Tests the 'count' method
     *
     */
    public function testCount()
    {
        $field = Xyster_Data_Field::count('id', 'ids');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Count(), $field->getFunction());
    }
    
    /**
     * Tests the 'sum' method
     *
     */
    public function testSum()
    {
        $field = Xyster_Data_Field::sum('id', 'sumofid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Sum(), $field->getFunction());
    }
    
    /**
     * Tests the 'max' method
     *
     */
    public function testMax()
    {
        $field = Xyster_Data_Field::max('id', 'maxid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Maximum(), $field->getFunction());
    }
    
    /**
     * Tests the 'min' method
     *
     */
    public function testMin()
    {
        $field = Xyster_Data_Field::min('id', 'minid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Minimum(), $field->getFunction());
    }
    
    /**
     * Tests the 'avg' method
     *
     */
    public function testAvg()
    {
        $field = Xyster_Data_Field::avg('id', 'avgid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Average(), $field->getFunction());
    }
    
    /**
     * Tests the 'group' method
     *
     */
    public function testGroup()
    {
        $field = Xyster_Data_Field::group('city', 'groupCity');
        $this->assertType('Xyster_Data_Field_Group', $field);
    }
    
    /**
     * Tests the 'named' method
     *
     */
    public function testNameAndAlias()
    {
        $field = Xyster_Data_Field::named('username', 'theusername');
        $this->assertEquals('username', $field->getName());
        $this->assertEquals('theusername', $field->getAlias());
    }
    
    /**
     * Tests passing an aggregate to the 'name' method
     *
     */
    public function testNameAggregate()
    {
        $field = Xyster_Data_Field::named('COUNT(testing)', 'countOfTesting');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
    }
    
    /**
     * Tests the 'named' method with a bad name
     *
     */
    public function testBadName()
    {
        $this->setExpectedException('Xyster_Data_Exception');
        Xyster_Data_Field::named(null);
    }
    
    /**
     * Tests the 'setAlias' method
     *
     */
    public function testSetAlias()
    {
        $field = Xyster_Data_Field::named('country');
        $field->setAlias('myCountry');
        
        $this->setExpectedException('Xyster_Data_Exception');
        $field->setAlias('');
    }
    
    /**
     * Tests the '__toString' method
     *
     */
    public function testToString()
    {
        $this->assertEquals($this->_commonField->getName(), (string)$this->_commonField);
    }
    
    /**
     * Tests the 'evaluate' method
     *
     */
    public function testEvaluate()
    {
        $field = Xyster_Data_Field::named('lastname', 'sn');

        // array
        $this->assertEquals('Smith', $field->evaluate(array('lastname'=>'Smith')));
	    // arrayaccess
	    $this->assertEquals('Smith', $field->evaluate(new ArrayObject(array('lastname'=>'Smith'))));

	    $obj = new Xyster_Data_FieldTestObject;
	    $obj->lastname = 'Smith';
	    // object
	    $this->assertEquals('Smith', $field->evaluate($obj));
	    // object method call
	    $field2 = Xyster_Data_Field::named('getLastname()', 'sn');
	    $this->assertEquals($obj->getLastname(), $field2->evaluate($obj));
    }
    
    /**
     * Tests the 'evaluate' method with a bad name
     *
     */
    public function testEvaluateBadName()
    {
        $this->setExpectedException('Xyster_Data_Exception');
        $field = Xyster_Data_Field::named('lastname', 'sn');
        $field->evaluate(array('firstname'=>'Bob'));
    }
    
    /**
     * Tests the evaluate method with a bad param
     *
     */
    public function testEvaluateBadParam()
    {
        $this->setExpectedException('Xyster_Data_Exception');
        $this->_commonField->evaluate(1234);
    }
    
    /**
     * Tests the 'asc' method
     *
     */    
    public function testAsc()
    {
        $sort = $this->_commonField->asc();
        $this->assertType('Xyster_Data_Sort', $sort);
        $this->assertSame($this->_commonField, $sort->getField());
        $this->assertEquals('ASC', $sort->getDirection());
    }
    
    /**
     * Tests the 'desc' method
     *
     */
    public function testDesc()
    {
        $sort = $this->_commonField->desc();
        $this->assertType('Xyster_Data_Sort', $sort);
        $this->assertSame($this->_commonField, $sort->getField());
        $this->assertEquals('DESC', $sort->getDirection());
    }
    
    /**
     * Tests the 'eq' method
     *
     */
    public function testEq()
    {
        $exp = $this->_commonField->eq('doublecompile');
        $this->_testExpression($exp, '=');
    }
    
    /**
     * Tests the 'neq' method
     *
     */
    public function testNeq()
    {
        $exp = $this->_commonField->neq('doublecompile');
        $this->_testExpression($exp, '<>');
    }
    
    /**
     * Tests the 'lt' method
     *
     */
    public function testLt()
    {
        $exp = $this->_commonField->lt('doublecompile');
        $this->_testExpression($exp, '<');
    }
    
    /**
     * Tests the 'lte' method
     *
     */
    public function testLte()
    {
        $exp = $this->_commonField->lte('doublecompile');
        $this->_testExpression($exp, '<=');
    }
    
    /**
     * Tests the 'gt' method
     *
     */
    public function testGt()
    {
        $exp = $this->_commonField->gt('doublecompile');
        $this->_testExpression($exp, '>');
    }
    
    /**
     * Tests the 'gte' method
     *
     */
    public function testGte()
    {
        $exp = $this->_commonField->gte('doublecompile');
        $this->_testExpression($exp, '>=');
    }
    
    /**
     * Tests the 'like' method
     *
     */
    public function testLike()
    {
        $exp = $this->_commonField->like('doublecompile');
        $this->_testExpression($exp, 'LIKE');
    }
    
    /**
     * Tests the 'notLike' method
     *
     */
    public function testNotLike()
    {
        $exp = $this->_commonField->notLike('doublecompile');
        $this->_testExpression($exp, 'NOT LIKE');
    }
    
    /**
     * Tests the 'between' method
     *
     */
    public function testBetween()
    {
        $exp = $this->_commonField->between('dou', 'doz');
        $this->_testExpression($exp, 'BETWEEN');
    }
    
    /**
     * Tests the 'notBetween' method
     *
     */
    public function testNotBetween()
    {
        $exp = $this->_commonField->notBetween('dou', 'doz');
        $this->_testExpression($exp, 'NOT BETWEEN');
    }
    
    /**
     * Tests the 'in' method
     *
     */
    public function testIn()
    {
        $exp = $this->_commonField->in(range(0, 10));
        $this->_testExpression($exp, 'IN');
    }
    
    /**
     * Tests the 'notIn' method
     *
     */
    public function testNotIn()
    {
        $exp = $this->_commonField->notIn(range(0, 10));
        $this->_testExpression($exp, 'NOT IN');
    }
    
    /**
     * Convenience method to test an Expression
     *
     * @param Xyster_Data_Expression $exp
     * @param string $operator
     */
    protected function _testExpression( $exp, $operator )
    {
        /* @var $exp Xyster_Data_Expression */
        $this->assertType('Xyster_Data_Expression', $exp);
        $this->assertSame($this->_commonField, $exp->getLeft());
        $this->assertEquals($operator, $exp->getOperator());
    }
}

/**
 * A stub object to test getting values from an object
 *
 */
class Xyster_Data_FieldTestObject
{
    public $lastname;
    
    public function getLastname()
    {
        return $this->lastname;
    }
}