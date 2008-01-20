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
    /**
     * Tests 'fromArray' method with no items
     *
     */
    public function testFromArrayEmpty()
    {
        $crit = Xyster_Data_Criterion::fromArray('AND', array());
        
        $this->assertNull($crit);
    }
    
    /**
     * Tests 'fromArray' with one item
     *
     */
    public function testFromArrayOne()
    {
        $exp = Xyster_Data_Expression::eq('username', 'doublecompile');
        $crit = Xyster_Data_Criterion::fromArray('AND', array($exp));
        
        $this->assertNotNull($crit);
        $this->assertSame($exp, $crit);
    }
    
    /**
     * Tests 'fromArray' with several items
     *
     */
    public function testFromArrayMulti()
    {
        $crit = Xyster_Data_Criterion::fromArray('OR',
                array(Xyster_Data_Expression::eq('sn', 'Smith'),
                    Xyster_Data_Expression::eq('gn', 'Bob')));
                    
        $this->assertNotNull($crit);
        $this->assertType('Xyster_Data_Junction', $crit);
    }
    
    /**
     * Tests 'fromArray' with a bad operator
     *
     */
    public function testFromArrayMultiBadOperator()
    {
        $this->setExpectedException('Xyster_Data_Exception');
        Xyster_Data_Criterion::fromArray('BAD',
            array(Xyster_Data_Expression::eq('sn', 'Smith'),
                Xyster_Data_Expression::eq('gn', 'Bob')));
    }
    
    /**
     * Tests the 'getFields' method
     *
     */
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