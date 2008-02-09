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

// Call Xyster_Data_ExpressionTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Data_ExpressionTest::main');
}

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Data/Expression.php';
require_once 'Xyster/Data/Field.php';

/**
 * Test for Xyster_Data_Expression
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
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Data_ExpressionTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests the factory method with a field
     *
     */
    public function testFactoryWithField()
    {
        $field = Xyster_Data_Field::named('username');
        $expression = Xyster_Data_Expression::eq($field, 'doublecompile');
        $this->assertSame($field, $expression->getLeft());
    }
    
    /**
     * Tests the factory method with a string
     *
     */
    public function testFactoryWithString()
    {
        $expression = Xyster_Data_Expression::eq('username', 'doublecompile');
        $this->assertEquals('username', $expression->getLeft()->getName());
    }
    
    /**
     * Tests the factory method with a null string
     *
     */
    public function testFactoryWithNullString()
    {
        $expression = Xyster_Data_Expression::eq('username', 'NULL');
        $this->assertNull($expression->getRight());
    }
    
    /**
     * Tests the 'getMethodName' method
     *
     */
    public function testGetMethodName()
    {
        foreach( self::$_operators as $name => $operator ) {
            $this->assertEquals($name, Xyster_Data_Expression::getMethodName($operator));
        }
        $this->assertFalse(Xyster_Data_Expression::getMethodName('@1234'));
    }

    /**
     * Tests the 'eq' static method
     *
     */
    public function testEq()
    {
        $exp = Xyster_Data_Expression::eq('username', 'doublecompile');
        $this->assertSame(Xyster_Data_Operator_Expression::Eq(), $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('username'=>'doublecompile')));
        $this->assertFalse($exp->evaluate(array('username'=>'rspeed')));
    }

    /**
     * Tests the 'neq' static method
     *
     */
    public function testNeq()
    {
        $exp = Xyster_Data_Expression::neq('username', 'doublecompile');
        $this->assertSame(Xyster_Data_Operator_Expression::Neq(), $exp->getOperator());
        $this->assertFalse($exp->evaluate(array('username'=>'doublecompile')));
        $this->assertTrue($exp->evaluate(array('username'=>'rspeed')));
    }

    /**
     * Tests the 'gt' static method
     *
     */
    public function testGt()
    {
        $exp = Xyster_Data_Expression::gt('age', 18);
        $this->assertSame(Xyster_Data_Operator_Expression::Gt(), $exp->getOperator());
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
        $exp = Xyster_Data_Expression::lt('age', 18);
        $this->assertSame(Xyster_Data_Operator_Expression::Lt(), $exp->getOperator());
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
        $exp = Xyster_Data_Expression::gte('age', 18);
        $this->assertSame(Xyster_Data_Operator_Expression::Gte(), $exp->getOperator());
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
        $exp = Xyster_Data_Expression::lte('age', 18);
        $this->assertSame(Xyster_Data_Operator_Expression::Lte(), $exp->getOperator());
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
        $exp = Xyster_Data_Expression::like('city', '%York%');
        $this->assertSame(Xyster_Data_Operator_Expression::Like(), $exp->getOperator());
        
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

    /**
     * Tests the 'notLike' static method
     *
     */
    public function testNotLike()
    {
        $exp = Xyster_Data_Expression::notLike('city', '%York%');
        $this->assertSame(Xyster_Data_Operator_Expression::NotLike(), $exp->getOperator());
        
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

    /**
     * Tests the 'between' static method
     *
     */
    public function testBetween()
    {
        $exp = Xyster_Data_Expression::between('age', 18, 45);
        $this->assertSame(Xyster_Data_Operator_Expression::Between(), $exp->getOperator());
        
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
        $exp = Xyster_Data_Expression::notBetween('age', 18, 45);
        $this->assertSame(Xyster_Data_Operator_Expression::NotBetween(), $exp->getOperator());
        
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
        $exp = Xyster_Data_Expression::in('age', array(18, 19, 20, 21));
        $this->assertSame(Xyster_Data_Operator_Expression::In(), $exp->getOperator());
        $this->assertTrue($exp->evaluate(array('age'=>19)));
        $this->assertFalse($exp->evaluate(array('age'=>5)));
    }

    /**
     * Tests the 'notIn' static method
     *
     */
    public function testNotIn()
    {
        $exp = Xyster_Data_Expression::notIn('age', array(18, 19, 20, 21));
        $this->assertSame(Xyster_Data_Operator_Expression::NotIn(), $exp->getOperator());
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
	    $this->assertRegExp('/NULL$/', (string)Xyster_Data_Expression::eq('username', null));
	    
	    // field value
	    $field = Xyster_Data_Field::named('username');
	    $this->assertRegExp('/'.$field.'$/', (string)Xyster_Data_Expression::eq('nickname', $field));

	    // operator is between
	    $this->assertRegExp('/BETWEEN \'a\' AND \'z\'$/', (string)Xyster_Data_Expression::between('username', 'a', 'z'));
	    
	    // operator is in
	    $this->assertRegExp('/IN \([\d, ]+\)$/', (string)Xyster_Data_Expression::in('age', range(13,21)));
    }
}

// Call Xyster_Data_ExpressionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Data_ExpressionTest::main') {
    Xyster_Data_ExpressionTest::main();
}
