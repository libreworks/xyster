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
 * @see Xyster_Data_Junction
 */
require_once 'Xyster/Data/Junction.php';
/**
 * @see Xyster_Data_Expression
 */
require_once 'Xyster/Data/Expression.php';
/**
 * Test for Xyster_Data_Junction
 *
 */
class Xyster_Data_JunctionTest extends PHPUnit_Framework_TestCase
{
    public function testAny()
    {
        $expr1 = Xyster_Data_Expression::eq('field1', 'foo');
        $expr2 = Xyster_Data_Expression::like('field2', '%bar');
        $any = Xyster_Data_Junction::any($expr1, $expr2);
        $this->_testStaticFactory($any, 'OR', $expr1, $expr2);
    }
    public function testAll()
    {
        $expr1 = Xyster_Data_Expression::eq('field1', 'foo');
        $expr2 = Xyster_Data_Expression::like('field2', '%bar');
        $all = Xyster_Data_Junction::all($expr1, $expr2);
        $this->_testStaticFactory($all, 'AND', $expr1, $expr2);
    }
    public function testSameOperator()
    {
        $all = Xyster_Data_Junction::all(
            Xyster_Data_Junction::all(
                Xyster_Data_Expression::eq('field1', 'foo'),
                Xyster_Data_Expression::like('field2', '%bar')
            ),
            Xyster_Data_Junction::all(
                Xyster_Data_Expression::eq('field3', 'foo'),
                Xyster_Data_Expression::like('field4', '%bar')
            )
        );
        $this->assertEquals(4, count($all->getCriteria()));
    }
    public function testToString()
    {
        $expr1 = Xyster_Data_Expression::eq('field1', 'foo');
        $expr2 = Xyster_Data_Expression::like('field2', '%bar');
        $expr3 = Xyster_Data_Expression::gt('field3', 1);
        $all = Xyster_Data_Junction::all($expr1, $expr2);
        $all->add($expr3);
        $this->assertEquals("( $expr1 AND $expr2 AND $expr3 )", (string)$all);
    }
    public function testAdd()
    {
        $junc = Xyster_Data_Junction::all( 
            Xyster_Data_Expression::eq('foo', 'bar'),
            Xyster_Data_Expression::eq('meaning', 42));
        // test criteria is added to junction
        $junc->add(Xyster_Data_Expression::eq('capitalOfNebraska', 'Lincoln'));

        $junc2 = Xyster_Data_Junction::all(
	        Xyster_Data_Expression::eq('username', 'hawk'),
                Xyster_Data_Expression::gt('posted', '2007-07-07'));
	    // if criteria is junction with same operator, just append contents
	    $junc->add($junc2);
        $this->assertTrue($junc->getCriteria()->containsAll($junc2->getCriteria()));
    }
    public function testGetCriteria()
    {
        $expr = array();
        $expr[] = Xyster_Data_Expression::eq('foo', 'bar');
        $expr[] = Xyster_Data_Expression::eq('meaning', 42);
        $expr[] = Xyster_Data_Expression::eq('capitalOfNebraska', 'Lincoln');
        $junc = Xyster_Data_Criterion::fromArray('AND', $expr);
        /* @var $junc Xyster_Data_Junction */
        $this->assertType('Xyster_Collection_Interface', $junc->getCriteria());
        $this->assertTrue($junc->getCriteria()->containsAll(Xyster_Collection::using($expr)));
    }
    public function testEvaluate()
    {
        $all = Xyster_Data_Junction::all(
                Xyster_Data_Expression::eq('username', 'hawk'),
                Xyster_Data_Expression::gt('posted', '2007-07-07')
            );
        $all->add(Xyster_Data_Expression::like('title', '%unit tests%'));
        // test 'AND' is true
	    $this->assertTrue($all->evaluate(array('username'=>'hawk',
	        'posted'=>'2007-07-08', 'title'=>'effective unit tests rock')));
	    // test 'AND' is false
	    $this->assertFalse($all->evaluate(array('username'=>'hawk',
	        'posted'=>'2007-07-08', 'title'=>'innefective ones do not rock')));

	    $any = Xyster_Data_Junction::any(
	            Xyster_Data_Expression::eq('category','UnitTests'),
	            Xyster_Data_Expression::like('title', '%unit tests%')
	        );
	    $any->add(Xyster_Data_Expression::neq('isTest', null));
	    // test 'OR' is true
	    $this->assertTrue($any->evaluate(array('category'=>'Misc',
	        'title'=>'Testing framework', 'isTest'=>'Yeah')));
	    // test 'OR' is false
	    $this->assertFalse($any->evaluate(array('category'=>'Misc',
	        'title'=>'Testing framework', 'isTest'=>null)));
    }
    
    protected function _testStaticFactory( Xyster_Data_Junction $junction, $operator, Xyster_Data_Criterion $expr1, Xyster_Data_Criterion $expr2 )
    {
        $this->assertEquals($operator, $junction->getOperator());
        $this->assertTrue($junction->getCriteria()->contains($expr1));
        $this->assertTrue($junction->getCriteria()->contains($expr2));
    }
}