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
 * @subpackage Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * @see Xyster_Data_Expression
 */
require_once 'Xyster/Data/Expression.php';
/**
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * Test for Xyster_Data_Junction
 *
 */
class Xyster_Data_ExpressionTest extends PHPUnit_Framework_TestCase
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

    public function testFactoryWithField()
    {
        $field = Xyster_Data_Field::named('username');
        $expression = Xyster_Data_Expression::eq($field, 'doublecompile');
        $this->assertSame($field, $expression->getLeft());
    }
    public function testFactoryWithString()
    {
        $expression = Xyster_Data_Expression::eq('username', 'doublecompile');
        $this->assertEquals('username', $expression->getLeft()->getName());
    }
    public function testFactoryWithNullString()
    {
        $expression = Xyster_Data_Expression::eq('username', 'NULL');
        $this->assertNull($expression->getRight());
    }
    public function testIsOperator()
    {
        foreach( self::$_operators as $operator ) {
            $this->assertTrue(Xyster_Data_Expression::isOperator($operator));
        }
        $this->assertFalse(Xyster_Data_Expression::isOperator('@1234'));
    }
    public function testGetMethodName()
    {
        foreach( self::$_operators as $name=>$operator ) {
            $this->assertEquals($name, Xyster_Data_Expression::getMethodName($operator));
        }
        $this->assertFalse(Xyster_Data_Expression::getMethodName('@1234'));
    }
    public function testEq()
    {
        $exp = Xyster_Data_Expression::eq('username', 'doublecompile');
        $this->assertEquals('=', $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('username'=>'doublecompile')));
        $this->assertFalse($exp->evaluate(array('username'=>'rspeed')));
    }
    public function testNeq()
    {
        $exp = Xyster_Data_Expression::neq('username', 'doublecompile');
        $this->assertEquals('<>', $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('username'=>'doublecompile')));
        $this->assertTrue($exp->evaluate(array('username'=>'rspeed')));
    }
    public function testGt()
    {
        $exp = Xyster_Data_Expression::gt('age', 18);
        $this->assertEquals('>', $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>18)));
        $this->assertFalse($exp->evaluate(array('age'=>17)));
    }
    public function testLt()
    {
        $exp = Xyster_Data_Expression::lt('age', 18);
        $this->assertEquals('<', $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>18)));
        $this->assertTrue($exp->evaluate(array('age'=>17)));
    }
    public function testGte()
    {
        $exp = Xyster_Data_Expression::gte('age', 18);
        $this->assertEquals('>=', $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertTrue($exp->evaluate(array('age'=>18)));
        $this->assertFalse($exp->evaluate(array('age'=>17)));
    }
    public function testLte()
    {
        $exp = Xyster_Data_Expression::lte('age', 18);
        $this->assertEquals('<=', $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('age'=>19)));
        $this->assertTrue($exp->evaluate(array('age'=>18)));
        $this->assertTrue($exp->evaluate(array('age'=>17)));
    }
    public function testLike()
    {
        $exp = Xyster_Data_Expression::like('city', '%York%');
        $this->assertEquals('LIKE', $exp->getOperator());
        
        $this->assertTrue($exp->evaluate(array('city'=>'New York')));
        $this->assertTrue($exp->evaluate(array('city'=>'York')));
        $this->assertTrue($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertFalse($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Xyster_Data_Expression::like('city', 'York%');
        
        $this->assertFalse($exp->evaluate(array('city'=>'New York')));
        $this->assertTrue($exp->evaluate(array('city'=>'York')));
        $this->assertTrue($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertFalse($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Xyster_Data_Expression::like('city', '%York');
        
        $this->assertTrue($exp->evaluate(array('city'=>'New York')));
        $this->assertTrue($exp->evaluate(array('city'=>'York')));
        $this->assertFalse($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertFalse($exp->evaluate(array('city'=>'Baltimore')));
    }
    public function testNotLike()
    {
        $exp = Xyster_Data_Expression::notLike('city', '%York%');
        $this->assertEquals('NOT LIKE', $exp->getOperator());
        
        $this->assertFalse($exp->evaluate(array('city'=>'New York')));
        $this->assertFalse($exp->evaluate(array('city'=>'York')));
        $this->assertFalse($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertTrue($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Xyster_Data_Expression::notLike('city', 'York%');
        
        $this->assertTrue($exp->evaluate(array('city'=>'New York')));
        $this->assertFalse($exp->evaluate(array('city'=>'York')));
        $this->assertFalse($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertTrue($exp->evaluate(array('city'=>'Baltimore')));
        
        $exp = Xyster_Data_Expression::notLike('city', '%York');
        
        $this->assertFalse($exp->evaluate(array('city'=>'New York')));
        $this->assertFalse($exp->evaluate(array('city'=>'York')));
        $this->assertTrue($exp->evaluate(array('city'=>'Yorkshire')));
        $this->assertTrue($exp->evaluate(array('city'=>'Baltimore')));
    }    
    public function testBetween()
    {
        $exp = Xyster_Data_Expression::between('age', 18, 45);
        $this->assertEquals('BETWEEN', $exp->getOperator());
        
        $this->assertTrue($exp->evaluate(array('age'=>18)));
        $this->assertTrue($exp->evaluate(array('age'=>45)));
        $this->assertTrue($exp->evaluate(array('age'=>32)));
        $this->assertFalse($exp->evaluate(array('age'=>99)));
    }
    public function testNotBetween()
    {
        $exp = Xyster_Data_Expression::notBetween('age', 18, 45);
        $this->assertEquals('NOT BETWEEN', $exp->getOperator());
        
        $this->assertFalse($exp->evaluate(array('age'=>18)));
        $this->assertFalse($exp->evaluate(array('age'=>45)));
        $this->assertFalse($exp->evaluate(array('age'=>32)));
        $this->assertTrue($exp->evaluate(array('age'=>99)));
    }
    public function testIn()
    {
        $exp = Xyster_Data_Expression::in('age', array(18, 19, 20, 21));
        $this->assertEquals('IN', $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>5)));
    }
    public function testNotIn()
    {
        $exp = Xyster_Data_Expression::notIn('age', array(18, 19, 20, 21));
        $this->assertEquals('NOT IN', $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('age'=>19)));
        $this->assertTrue($exp->evaluate(array('age'=>5)));
    }
    public function testToString()
    {
        // null value
	    $this->assertRegExp('/NULL$/', (string)Xyster_Data_Expression::eq('username', null));
	    
	    // field value
	    $field = Xyster_Data_Field::named('username');
	    $this->assertRegExp('/'.$field.'$/', (string)Xyster_Data_Expression::eq('nickname', $field));

	    // operator is between

	    // operator is in

	    // operator is scalar
    }
}