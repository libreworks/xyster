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
use Xyster\Data\Symbol\Criterion,
        Xyster\Data\Symbol\Expression,
        Xyster\Data\Symbol\Junction;
/**
 * Test for Xyster_Data_Junction
 *
 */
class CriterionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests 'fromArray' method with no items
     *
     */
    public function testFromArrayEmpty()
    {
        $crit = Criterion::fromArray('AND', array());
        
        $this->assertNull($crit);
    }
    
    /**
     * Tests 'fromArray' with one item
     *
     */
    public function testFromArrayOne()
    {
        $exp = Expression::eq('username', 'doublecompile');
        $crit = Criterion::fromArray('AND', array($exp));
        
        $this->assertNotNull($crit);
        $this->assertSame($exp, $crit);
    }
    
    /**
     * Tests 'fromArray' with several items
     *
     */
    public function testFromArrayMulti()
    {
        $crit = Criterion::fromArray('OR',
                array(Expression::eq('sn', 'Smith'),
                    Expression::eq('gn', 'Bob')));
                    
        $this->assertNotNull($crit);
        $this->assertType('\Xyster\Data\Symbol\Junction', $crit);
    }
    
    /**
     * Tests 'fromArray' with a bad operator
     * @expectedException \Xyster\Data\Symbol\InvalidArgumentException
     */
    public function testFromArrayMultiBadOperator()
    {
        Criterion::fromArray('BAD',
            array(Expression::eq('sn', 'Smith'),
                Expression::eq('gn', 'Bob')));
    }
    
//    public function testGetFields()
//    {
//        $field = \Xyster\Data\Symbol\Field::named('sn');
//        $field2 = \Xyster\Data\Symbol\Field::named('lastname');
//        $exp1 = Expression::eq($field, 'Smith');
//        $exp2 = Expression::eq($field2, $field);
//        $junc = \Xyster\Data\Symbol\Junction::all($exp1, $exp2);
//
//        $fields = Criterion::getFields($junc);
//        $this->assertType('\Xyster\Collection\ICollection', $fields);
//        $this->assertTrue($fields->contains($field));
//        $this->assertTrue($fields->contains($field2));
//    }
}