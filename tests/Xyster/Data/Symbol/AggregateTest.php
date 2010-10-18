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
use Xyster\Data\Symbol\Aggregate;
use Xyster\Enum\Enum;
/**
 * Test for Aggregate
 *
 */
class AggregateTest extends \PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        $count = Aggregate::Count();
        $this->_runTests( $count, 'Count', 'COUNT' );
    }
    public function testAvg()
    {
        $avg = Aggregate::Average();
        $this->_runTests( $avg, 'Average', 'AVG' );
    }
    public function testSum()
    {
        $sum = Aggregate::Sum();
        $this->_runTests( $sum, 'Sum', 'SUM' );
    }
    public function testMax()
    {
        $max = Aggregate::Maximum();
        $this->_runTests( $max, 'Maximum', 'MAX' );
    }
    public function testMin()
    {
        $min = Aggregate::Minimum();
        $this->_runTests( $min, 'Minimum', 'MIN' );
    }

    protected function _runTests( $actual, $name, $value )
    {
        $this->assertEquals($name,$actual->getName());
        $this->assertEquals($value,$actual->getValue());
        $this->assertEquals('Xyster\Data\Symbol\Aggregate ['.$value.','.$name.']',(string)$actual);
        $this->assertEquals($actual,Enum::parse('\Xyster\Data\Symbol\Aggregate',$name));
        $this->assertEquals($actual,Enum::valueOf('\Xyster\Data\Symbol\Aggregate',$value));
    }
}