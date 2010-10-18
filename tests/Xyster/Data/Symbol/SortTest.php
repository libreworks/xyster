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
use Xyster\Data\Symbol\Sort;
/**
 * Test for Xyster_Data_Sort
 *
 */
class SortTest extends \PHPUnit_Framework_TestCase
{
    public function testStringField()
    {
        $sort = Sort::asc('username');
        $this->assertEquals('username', $sort->getField()->getName());
    }
    public function testFieldField()
    {
        $field = \Xyster\Data\Symbol\Field::named('username');
        $sort = Sort::asc($field);
        $this->assertSame($field, $sort->getField());
    }
    public function testToString()
    {
        $field = \Xyster\Data\Symbol\Field::named('username');
        $sort = Sort::asc($field);
        $this->assertEquals($field . ' ' . $sort->getDirection(), (string)$sort);
    }
    public function testAsc()
    {
        $sort = Sort::asc('username');
        $this->assertEquals('ASC', $sort->getDirection());
    }
    public function testDesc()
    {
        $sort = Sort::desc('username');
        $this->assertEquals('DESC', $sort->getDirection());
    }
}