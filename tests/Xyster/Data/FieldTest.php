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
 * @subpackage Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
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
     
    public function setUp()
    {
        $this->_commonField = Xyster_Data_Field::named('username');
    }
    public function testCount()
    {
        $field = Xyster_Data_Field::count('id', 'ids');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Count(), $field->getFunction());
    }
    public function testSum()
    {
        $field = Xyster_Data_Field::sum('id', 'sumofid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Sum(), $field->getFunction());
    }
    public function testMax()
    {
        $field = Xyster_Data_Field::max('id', 'maxid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Maximum(), $field->getFunction());
    }
    public function testMin()
    {
        $field = Xyster_Data_Field::min('id', 'minid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Minimum(), $field->getFunction());
    }
    public function testAvg()
    {
        $field = Xyster_Data_Field::avg('id', 'avgid');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertSame(Xyster_Data_Aggregate::Average(), $field->getFunction());
    }
    public function testGroup()
    {
        $field = Xyster_Data_Field::group('city', 'groupCity');
        $this->assertType('Xyster_Data_Field_Group', $field);
    }
    public function testNameAndAlias()
    {
        $field = Xyster_Data_Field::named('username', 'theusername');
        $this->assertEquals('username', $field->getName());
        $this->assertEquals('theusername', $field->getAlias());
    }
    public function testBadName()
    {
        try {
            Xyster_Data_Field::named(null);
        } catch( Exception $thrown ) {
            // okay
	        return;
        }
        $this->fail('Exception not thrown');
    }
    public function testSetAlias()
    {
        $field = Xyster_Data_Field::named('country');
        $field->setAlias('myCountry');
        try {
            $field->setAlias('');
        } catch ( Exception $thrown ) {
            // okay
	        return;
        }
        $this->fail('Exception not thrown');
    }
    public function testToString()
    {
        $this->assertEquals($this->_commonField->getName(), (string)$this->_commonField);
    }
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
    public function testEvaluateBadName()
    {
        $field = Xyster_Data_Field::named('lastname', 'sn');
        try { 
            $field->evaluate(array('firstname'=>'Bob'));
        } catch ( Exception $thrown ) {
            // okay
	        return; 
        }
        $this->fail('Exception not thrown');
    }
    public function testEvaluateBadParam()
    {
        try {
            $this->_commonField->evaluate(1234);
        } catch ( Exception $thrown ) {
            // okay 
	        return;
        }
        $this->fail('Exception not thrown');
    }
    public function testAsc()
    {
        $sort = $this->_commonField->asc();
        $this->assertType('Xyster_Data_Sort', $sort);
        $this->assertSame($this->_commonField, $sort->getField());
        $this->assertEquals('ASC', $sort->getDirection());
    }
    public function testDesc()
    {
        $sort = $this->_commonField->desc();
        $this->assertType('Xyster_Data_Sort', $sort);
        $this->assertSame($this->_commonField, $sort->getField());
        $this->assertEquals('DESC', $sort->getDirection());
    }
    public function testEq()
    {
        $exp = $this->_commonField->eq('doublecompile');
        $this->_testExpression($exp, '=');
    }
    public function testNeq()
    {
        $exp = $this->_commonField->neq('doublecompile');
        $this->_testExpression($exp, '<>');
    }
    public function testLt()
    {
        $exp = $this->_commonField->lt('doublecompile');
        $this->_testExpression($exp, '<');
    }
    public function testLte()
    {
        $exp = $this->_commonField->lte('doublecompile');
        $this->_testExpression($exp, '<=');
    }
    public function testGt()
    {
        $exp = $this->_commonField->gt('doublecompile');
        $this->_testExpression($exp, '>');
    }
    public function testGte()
    {
        $exp = $this->_commonField->gte('doublecompile');
        $this->_testExpression($exp, '>=');
    }
    public function testLike()
    {
        $exp = $this->_commonField->like('doublecompile');
        $this->_testExpression($exp, 'LIKE');
    }
    public function testNotLike()
    {
        $exp = $this->_commonField->notLike('doublecompile');
        $this->_testExpression($exp, 'NOT LIKE');
    }
    public function testBetween()
    {
        $exp = $this->_commonField->between('dou', 'doz');
        $this->_testExpression($exp, 'BETWEEN');
    }
    public function testNotBetween()
    {
        $exp = $this->_commonField->notBetween('dou', 'doz');
        $this->_testExpression($exp, 'NOT BETWEEN');
    }
    public function testIn()
    {
        $exp = $this->_commonField->in(range(0,10));
        $this->_testExpression($exp, 'IN');
    }
    public function testNotIn()
    {
        $exp = $this->_commonField->notIn(range(0,10));
        $this->_testExpression($exp, 'NOT IN');
    }
    
    protected function _testExpression( $exp, $operator )
    {
        /* @var $exp Xyster_Data_Expression */
        $this->assertType('Xyster_Data_Expression', $exp);
        $this->assertSame($this->_commonField, $exp->getLeft());
        $this->assertEquals($operator, $exp->getOperator());
    }
}
class Xyster_Data_FieldTestObject
{
    public $lastname;
    
    public function getLastname()
    {
        return $this->lastname;
    }
}