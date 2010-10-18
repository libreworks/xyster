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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Data\Symbol;
use Xyster\Data\Symbol\Field,
        Xyster\Data\Symbol\Aggregate;
/**
 * Test for Field
 *
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Field
     */
    protected $_commonField;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        $this->_commonField = Field::named('username');
    }
    
    /**
     * Tests the 'count' method
     *
     */
    public function testCount()
    {
        $field = Field::count('id', 'ids');
        $this->assertType('\Xyster\Data\Symbol\AggregateField', $field);
        $this->assertSame(Aggregate::Count(), $field->getFunction());
    }
    
    /**
     * Tests the 'sum' method
     *
     */
    public function testSum()
    {
        $field = Field::sum('id', 'sumofid');
        $this->assertType('\Xyster\Data\Symbol\AggregateField', $field);
        $this->assertSame(Aggregate::Sum(), $field->getFunction());
    }
    
    /**
     * Tests the 'max' method
     *
     */
    public function testMax()
    {
        $field = Field::max('id', 'maxid');
        $this->assertType('\Xyster\Data\Symbol\AggregateField', $field);
        $this->assertSame(Aggregate::Maximum(), $field->getFunction());
    }
    
    /**
     * Tests the 'min' method
     *
     */
    public function testMin()
    {
        $field = Field::min('id', 'minid');
        $this->assertType('\Xyster\Data\Symbol\AggregateField', $field);
        $this->assertSame(Aggregate::Minimum(), $field->getFunction());
    }
    
    /**
     * Tests the 'avg' method
     *
     */
    public function testAvg()
    {
        $field = Field::avg('id', 'avgid');
        $this->assertType('\Xyster\Data\Symbol\AggregateField', $field);
        $this->assertSame(Aggregate::Average(), $field->getFunction());
    }
    
    /**
     * Tests the 'group' method
     *
     */
    public function testGroup()
    {
        $field = Field::group('city', 'groupCity');
        $this->assertType('\Xyster\Data\Symbol\GroupField', $field);
    }
    
    /**
     * Tests the 'named' method
     *
     */
    public function testNameAndAlias()
    {
        $field = Field::named('username', 'theusername');
        $this->assertEquals('username', $field->getName());
        $this->assertEquals('theusername', $field->getAlias());
    }
    
    /**
     * Tests passing an aggregate to the 'name' method
     *
     */
    public function testNameAggregate()
    {
        $field = Field::named('COUNT(testing)', 'countOfTesting');
        $this->assertType('\Xyster\Data\Symbol\AggregateField', $field);
    }
    
    /**
     * Tests the 'named' method with a bad name
     * @expectedException Xyster\Data\Symbol\InvalidArgumentException
     */
    public function testBadName()
    {
        Field::named(null);
    }
    
    /**
     * Tests the 'setAlias' method
     * @expectedException Xyster\Data\Symbol\InvalidArgumentException
     */
    public function testSetAlias()
    {
        $field = Field::named('country');
        $field->setAlias('myCountry');
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
     * Tests the 'asc' method
     *
     */    
    public function testAsc()
    {
        $sort = $this->_commonField->asc();
        $this->assertType('\Xyster\Data\Symbol\Sort', $sort);
        $this->assertSame($this->_commonField, $sort->getField());
        $this->assertTrue($sort->isAscending());
    }
    
    /**
     * Tests the 'desc' method
     *
     */
    public function testDesc()
    {
        $sort = $this->_commonField->desc();
        $this->assertType('\Xyster\Data\Symbol\Sort', $sort);
        $this->assertSame($this->_commonField, $sort->getField());
        $this->assertFalse($sort->isAscending());
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
        /* @var $exp \Xyster\Data\Symbol\Expression */
        $this->assertType('\Xyster\Data\Symbol\Expression', $exp);
        $this->assertSame($this->_commonField, $exp->getLeft());
        $enum = \Xyster\Enum\Enum::valueOf('\Xyster\Data\Symbol\Operator', $operator);
        $this->assertEquals($enum, $exp->getOperator());
    }
}

/**
 * A stub object to test getting values from an object
 *
 */
class FieldTestObject
{
    public $lastname;
    
    public function getLastname()
    {
        return $this->lastname;
    }
}