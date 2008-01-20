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
 * @see Xyster_Data_Aggregate
 */
require_once 'Xyster/Data/Aggregate.php';
/**
 * Test for Xyster_Data_Aggregate
 *
 */
class Xyster_Data_AggregateTest extends PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        $count = Xyster_Data_Aggregate::Count();
        $this->_runTests( $count, 'Count', 'COUNT' );
    }
    public function testAvg()
    {
        $avg = Xyster_Data_Aggregate::Average();
        $this->_runTests( $avg, 'Average', 'AVG' );
    }
    public function testSum()
    {
        $sum = Xyster_Data_Aggregate::Sum();
        $this->_runTests( $sum, 'Sum', 'SUM' );
    }
    public function testMax()
    {
        $max = Xyster_Data_Aggregate::Maximum();
        $this->_runTests( $max, 'Maximum', 'MAX' );
    }
    public function testMin()
    {
        $min = Xyster_Data_Aggregate::Minimum();
        $this->_runTests( $min, 'Minimum', 'MIN' );
    }

    protected function _runTests( $actual, $name, $value )
    {
        $this->assertEquals($name,$actual->getName());
        $this->assertEquals($value,$actual->getValue());
        $this->assertEquals('Xyster_Data_Aggregate ['.$value.','.$name.']',(string)$actual);
        $this->assertEquals($actual,Xyster_Enum::parse('Xyster_Data_Aggregate',$name));
        $this->assertEquals($actual,Xyster_Enum::valueOf('Xyster_Data_Aggregate',$value));
    }
}