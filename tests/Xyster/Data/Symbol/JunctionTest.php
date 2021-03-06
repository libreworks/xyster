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
use Xyster\Data\Symbol\Junction,
        Xyster\Data\Symbol\Expression,
        Xyster\Data\Symbol\Criterion;
/**
 * Test class for Junction.
 * Generated by PHPUnit on 2008-02-08 at 12:01:57.
 */
class JunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Junction
     */
    protected $object;
    
    protected $expr = array();

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->expr[] = Expression::eq('foo', 'bar');
        $this->expr[] = Expression::eq('meaning', 42);
        $this->expr[] = Expression::eq('capitalOfNebraska', 'Lincoln');
        $this->object = Criterion::fromArray('AND', $this->expr);
    }
    
    /**
     * Tests the 'add' method
     */
    public function testAdd()
    {
        $junc = Junction::all(
            Expression::eq('foo', 'bar'),
            Expression::eq('meaning', 42));
        // test criteria is added to junction
        $junc->add(Expression::eq('capitalOfNebraska', 'Lincoln'));

        $junc2 = Junction::all(
            Expression::eq('username', 'hawk'),
                Expression::gt('posted', '2007-07-07'));
        // if criteria is junction with same operator, just append contents
        $junc->add($junc2);
        $this->assertTrue($junc->getCriteria()->containsAll($junc2->getCriteria()));
    }

    /**
     * Tests the 'count' method
     */
    public function testCount()
    {
        $this->assertEquals(3, count($this->object));
    }

    /**
     * Tests the 'evaluate' method
     */
    public function testEvaluate()
    {
        $all = Junction::all(
                Expression::eq('username', 'hawk'),
                Expression::gt('posted', '2007-07-07')
            );
        $all->add(Expression::like('title', '%unit tests%'));
        // test 'AND' is true
        $this->assertTrue($all->evaluate(array('username'=>'hawk',
            'posted'=>'2007-07-08', 'title'=>'effective unit tests rock')));
        // test 'AND' is false
        $this->assertFalse($all->evaluate(array('username'=>'hawk',
            'posted'=>'2007-07-08', 'title'=>'innefective ones do not rock')));

        $any = Junction::any(
                Expression::eq('category','UnitTests'),
                Expression::like('title', '%unit tests%')
            );
        $any->add(Expression::neq('isTest', null));
        // test 'OR' is true
        $this->assertTrue($any->evaluate(array('category'=>'Misc',
            'title'=>'Testing framework', 'isTest'=>'Yeah')));
        // test 'OR' is false
        $this->assertFalse($any->evaluate(array('category'=>'Misc',
            'title'=>'Testing framework', 'isTest'=>null)));
    }

    /**
     * Tests the 'getCriteria' method
     */
    public function testGetCriteria()
    {
        $this->assertType('\Xyster\Collection\ICollection', $this->object->getCriteria());
        $this->assertTrue($this->object->getCriteria()->containsAll(\Xyster\Collection\Collection::using($this->expr)));
    }

    /**
     * Tests the 'getIterator' method
     */
    public function testGetIterator()
    {
        $this->assertType('\Iterator', $this->object->getIterator());
    }

    /**
     * Tests the 'toArray' method
     */
    public function testToArray()
    {
        $this->assertEquals($this->expr, $this->object->toArray());
    }

    /**
     * Tests the 'toString' method
     */
    public function test__toString()
    {
        $expr1 = Expression::eq('field1', 'foo');
        $expr2 = Expression::like('field2', '%bar');
        $expr3 = Expression::gt('field3', 1);
        $all = Junction::all($expr1, $expr2);
        $all->add($expr3);
        $this->assertEquals("( $expr1 AND $expr2 AND $expr3 )", (string)$all);
    }

    /**
     * Tests the 'any' static method
     */
    public function testAny()
    {
        $expr1 = Expression::eq('field1', 'foo');
        $expr2 = Expression::like('field2', '%bar');
        $any = Junction::any($expr1, $expr2);
        $this->_testStaticFactory($any, 'OR', $expr1, $expr2);
    }

    /**
     * Tests the 'all' static method
     */
    public function testAll()
    {
        $expr1 = Expression::eq('field1', 'foo');
        $expr2 = Expression::like('field2', '%bar');
        $all = Junction::all($expr1, $expr2);
        $this->_testStaticFactory($all, 'AND', $expr1, $expr2);
    }
    
    /**
     * Tests adding the same operator
     */
    public function testAllSameOperator()
    {
        $all = Junction::all(
            Junction::all(
                Expression::eq('field1', 'foo'),
                Expression::like('field2', '%bar')
            ),
            Junction::all(
                Expression::eq('field3', 'foo'),
                Expression::like('field4', '%bar')
            )
        );
        $this->assertEquals(4, count($all->getCriteria()));
    }
    
    /**
     * Runs common tests on a Junction
     *
     * @param Junction $junction
     * @param string $operator
     * @param Criterion $expr1
     * @param Criterion $expr2
     */
    protected function _testStaticFactory( Junction $junction, $operator, Criterion $expr1, Criterion $expr2 )
    {
        $this->assertEquals($operator, $junction->getOperator());
        $this->assertTrue($junction->getCriteria()->contains($expr1));
        $this->assertTrue($junction->getCriteria()->contains($expr2));
    }
}

