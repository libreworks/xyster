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
use Xyster\Data\Symbol\Expression,
        Xyster\Data\Symbol\Operator;
/**
 * Test for Expression
 *
 */
class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    static protected $_operators = array(
            "eq" => "=",
            "neq" => "<>",
            "lt" => "<",
            "gt" => ">",
            "gte" => ">=",
            "lte" => "<=",
            "like" => "LIKE",
            "notLike" => "NOT LIKE",
            "between" => "BETWEEN",
            "notBetween" => "NOT BETWEEN",
            "in" => "IN",
            "notIn" => "NOT IN" );
    
    /**
     * Tests the factory method with a field
     *
     */
    public function testFactoryWithField()
    {
        $field = \Xyster\Data\Symbol\Field::named('username');
        $expression = Expression::eq($field, 'doublecompile');
        $this->assertSame($field, $expression->getLeft());
    }
    
    /**
     * Tests the factory method with a string
     *
     */
    public function testFactoryWithString()
    {
        $expression = Expression::eq('username', 'doublecompile');
        $this->assertEquals('username', $expression->getLeft()->getName());
    }
    
    /**
     * Tests the factory method with a null string
     *
     */
    public function testFactoryWithNullString()
    {
        $expression = Expression::eq('username', 'NULL');
        $this->assertNull($expression->getRight());
    }
    
    /**
     * Tests the 'getMethodName' method
     *
     */
    public function testGetMethodName()
    {
        foreach( self::$_operators as $name => $operator ) {
            $this->assertEquals($name, Expression::getMethodName($operator));
        }
        $this->assertFalse(Expression::getMethodName('@1234'));
    }

    /**
     * Tests the 'eq' static method
     *
     */
    public function testEq()
    {
        $exp = Expression::eq('username', 'doublecompile');
        $this->assertSame(Operator::Eq(), $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('username'=>'doublecompile')));
        $this->assertFalse($exp->evaluate(array('username'=>'rspeed')));
    }

    /**
     * Tests the 'neq' static method
     *
     */
    public function testNeq()
    {
        $exp = Expression::neq('username', 'doublecompile');
        $this->assertSame(Operator::Neq(), $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('username'=>'doublecompile')));
        $this->assertTrue($exp->evaluate(array('username'=>'rspeed')));
    }

    /**
     * Tests the 'gt' static method
     *
     */
    public function testGt()
    {
        $exp = Expression::gt('age', 18);
        $this->assertSame(Operator::Gt(), $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>18)));
        $this->assertFalse($exp->evaluate(array('age'=>17)));
    }

    /**
     * Tests the 'lt' static method
     *
     */
    public function testLt()
    {
        $exp = Expression::lt('age', 18);
        $this->assertSame(Operator::Lt(), $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>18)));
        $this->assertTrue($exp->evaluate(array('age'=>17)));
    }

    /**
     * Tests the 'gte' static method
     *
     */
    public function testGte()
    {
        $exp = Expression::gte('age', 18);
        $this->assertSame(Operator::Gte(), $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertTrue($exp->evaluate(array('age'=>18)));
        $this->assertFalse($exp->evaluate(array('age'=>17)));
    }

    /**
     * Tests the 'lte' static method
     *
     */
    public function testLte()
    {
        $exp = Expression::lte('age', 18);
        $this->assertSame(Operator::Lte(), $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('age'=>19)));
        $this->assertTrue($exp->evaluate(array('age'=>18)));
        $this->assertTrue($exp->evaluate(array('age'=>17)));
    }

    /**
     * Tests the 'like' static method
     *
     */
    public function testLike()
    {
        $exp = Expression::like('city', '%York%');
        $this->assertSame(Operator::Like(), $exp->getOperator());
        
        $this->assertTrue($exp->evaluate(array('city'=>'New York')));
        $this->assertTrue($exp->evaluate(array('city'=>'York')));
        $this->assertTrue($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertFalse($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Expression::like('city', 'York%');
        
        $this->assertFalse($exp->evaluate(array('city'=>'New York')));
        $this->assertTrue($exp->evaluate(array('city'=>'York')));
        $this->assertTrue($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertFalse($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Expression::like('city', '%York');
        
        $this->assertTrue($exp->evaluate(array('city'=>'New York')));
        $this->assertTrue($exp->evaluate(array('city'=>'York')));
        $this->assertFalse($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertFalse($exp->evaluate(array('city'=>'Baltimore')));
    }

    /**
     * Tests the 'notLike' static method
     *
     */
    public function testNotLike()
    {
        $exp = Expression::notLike('city', '%York%');
        $this->assertSame(Operator::NotLike(), $exp->getOperator());
        
        $this->assertFalse($exp->evaluate(array('city'=>'New York')));
        $this->assertFalse($exp->evaluate(array('city'=>'York')));
        $this->assertFalse($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertTrue($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Expression::notLike('city', 'York%');
        
        $this->assertTrue($exp->evaluate(array('city'=>'New York')));
        $this->assertFalse($exp->evaluate(array('city'=>'York')));
        $this->assertFalse($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertTrue($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Expression::notLike('city', '%York');
        
        $this->assertFalse($exp->evaluate(array('city'=>'New York')));
        $this->assertFalse($exp->evaluate(array('city'=>'York')));
        $this->assertTrue($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertTrue($exp->evaluate(array('city'=>'Baltimore')));
    }    

    /**
     * Tests the 'between' static method
     *
     */
    public function testBetween()
    {
        $exp = Expression::between('age', 18, 45);
        $this->assertSame(Operator::Between(), $exp->getOperator());
        
        $this->assertTrue($exp->evaluate(array('age'=>18)));
        $this->assertTrue($exp->evaluate(array('age'=>45)));
        $this->assertTrue($exp->evaluate(array('age'=>32)));
        $this->assertFalse($exp->evaluate(array('age'=>99)));
    }

    /**
     * Tests the 'notBetween' static method
     *
     */
    public function testNotBetween()
    {
        $exp = Expression::notBetween('age', 18, 45);
        $this->assertSame(Operator::NotBetween(), $exp->getOperator());
        
        $this->assertFalse($exp->evaluate(array('age'=>18)));
        $this->assertFalse($exp->evaluate(array('age'=>45)));
        $this->assertFalse($exp->evaluate(array('age'=>32)));
        $this->assertTrue($exp->evaluate(array('age'=>99)));
    }

    /**
     * Tests the 'in' static method
     *
     */
    public function testIn()
    {
        $exp = Expression::in('age', array(18, 19, 20, 21));
        $this->assertSame(Operator::In(), $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>5)));
    }

    /**
     * Tests the 'notIn' static method
     *
     */
    public function testNotIn()
    {
        $exp = Expression::notIn('age', array(18, 19, 20, 21));
        $this->assertSame(Operator::NotIn(), $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('age'=>19)));
        $this->assertTrue($exp->evaluate(array('age'=>5)));
    }
    
    /**
     * Tests the '__toString' method
     *
     */
    public function testToString()
    {
        // null value
	    $this->assertRegExp('/NULL$/', (string)Expression::eq('username', null));
	    
	    // field value
	    $field = \Xyster\Data\Symbol\Field::named('username');
	    $this->assertRegExp('/'.$field.'$/', (string)Expression::eq('nickname', $field));

	    // operator is between
	    $this->assertRegExp('/BETWEEN \'a\' AND \'z\'$/', (string)Expression::between('username', 'a', 'z'));
	    
	    // operator is in
	    $this->assertRegExp('/IN \([\d, ]+\)$/', (string)Expression::in('age', range(13,21)));
    }
}
