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
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * @see Xyster_Data_Aggregate
 */
require_once 'Xyster/Data/Aggregate.php';
/**
 * @see Xyster_Data_Set
 */
require_once 'Xyster/Data/Set.php';
/**
 * Test for Xyster_Data_Field
 *
 */
class Xyster_Data_FieldAggregateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Data_Field_Aggregate
     */
    protected $_commonField;
     
    public function setUp()
    {
        $this->_commonField = Xyster_Data_Field::count('userid', 'users');
    }
    public function testEvaluate()
    {
        $this->assertEquals('Smith', $this->_commonField->evaluate(array('userid'=>'Smith')));
        
        $set = new Xyster_Data_Set();
        $set->add(array('userid'=>'Smith'));
        $set->add(array('userid'=>'Jones'));
        $set->add(array('userid'=>'Brown'));
        // test Field_Aggregate passes evaluation to the Data_Set
        $this->assertEquals(3, $this->_commonField->evaluate($set));
    }
    public function testToString()
    {
        $this->assertEquals($this->_commonField->getFunction()->getValue() . '(' . 
            $this->_commonField->getName() . ')', (string)$this->_commonField);
    }
}