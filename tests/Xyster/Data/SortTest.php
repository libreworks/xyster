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
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * @see Xyster_Data_Sort
 */
require_once 'Xyster/Data/Sort.php';
/**
 * Test for Xyster_Data_Sort
 *
 */
class Xyster_Data_SortTest extends PHPUnit_Framework_TestCase
{
    public function testStringField()
    {
        $sort = Xyster_Data_Sort::asc('username');
        $this->assertEquals('username', $sort->getField()->getName());
    }
    public function testFieldField()
    {
        $field = Xyster_Data_Field::named('username');
        $sort = Xyster_Data_Sort::asc($field);
        $this->assertSame($field, $sort->getField());
    }
    public function testToString()
    {
        $field = Xyster_Data_Field::named('username');
        $sort = Xyster_Data_Sort::asc($field);
        $this->assertEquals($field . ' ' . $sort->getDirection(), (string)$sort);
    }
    public function testAsc()
    {
        $sort = Xyster_Data_Sort::asc('username');
        $this->assertEquals('ASC', $sort->getDirection());
    }
    public function testDesc()
    {
        $sort = Xyster_Data_Sort::desc('username');
        $this->assertEquals('DESC', $sort->getDirection());
    }
}