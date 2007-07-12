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
 * @see Xyster_Data_Aggregate
 */
require_once 'Xyster/Data/Aggregate.php';
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
        
        /**
         * @todo do something with dataset
         */
    }
    public function testToString()
    {
        $this->assertEquals($this->_commonField->getFunction()->getValue() . '(' . 
            $this->_commonField->getName() . ')', (string)$this->_commonField);
    }
}