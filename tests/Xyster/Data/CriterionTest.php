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
class Xyster_Data_CriterionTest extends PHPUnit_Framework_TestCase
{
    public function testFromArrayEmpty()
    {
        $crit = Xyster_Data_Criterion::fromArray('AND', array());
        $this->assertNull($crit);
    }
    public function testFromArrayOne()
    {
        $exp = Xyster_Data_Expression::eq('username', 'doublecompile');
        $crit = Xyster_Data_Criterion::fromArray('AND', array($exp));
        $this->assertNotNull($crit);
        $this->assertSame($exp, $crit);
    }
    public function testFromArrayMulti()
    {
        $crit = Xyster_Data_Criterion::fromArray('OR',
                array(Xyster_Data_Expression::eq('sn', 'Smith'),
                    Xyster_Data_Expression::eq('gn', 'Bob')));
        $this->assertNotNull($crit);
        $this->assertType('Xyster_Data_Junction', $crit);
    }
    public function testFromArrayMultiBadOperator()
    {
        try { 
            Xyster_Data_Criterion::fromArray('BAD',
                array(Xyster_Data_Expression::eq('sn', 'Smith'),
                    Xyster_Data_Expression::eq('gn', 'Bob')));
        } catch ( Exception $thrown ) {
            // okay
	        return;
        }
        $this->fail('Exception not thrown');
    }
    public function testGetFields()
    {
        $field = Xyster_Data_Field::named('sn');
        $field2 = Xyster_Data_Field::named('lastname');
        $exp1 = Xyster_Data_Expression::eq($field, 'Smith');
        $exp2 = Xyster_Data_Expression::eq($field2, $field);
        $junc = Xyster_Data_Junction::all($exp1, $exp2);
        
        $fields = Xyster_Data_Criterion::getFields($junc);
        $this->assertType('Xyster_Collection_Interface', $fields);
        $this->assertTrue($fields->contains($field));
        $this->assertTrue($fields->contains($field2));
    }
}