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
 * @see Xyster_Data_SetTest
 */
require_once 'Xyster/Data/SetTest.php';
/**
 * @see Xyster_Data_Tuple
 */
require_once 'Xyster/Data/Tuple.php';
/**
 * Test for Xyster_Data_Tuple
 *
 */
class Xyster_Data_TupleTest extends Xyster_Data_SetTest
{
    /**
     * Tests creating a tuple
     *
     */
    public function testCreate()
    {
        $groups = array('city'=>'Baltimore', 'state'=>'Maryland', 'country'=>'United States');
        $c = $this->_getNewCollectionWithRandomValues();
        
        $tuple = new Xyster_Data_Tuple($groups, $c);
        $this->assertEquals(array_keys($groups), $tuple->getNames());
        $this->assertEquals($groups, $tuple->getValues());
        $this->assertEquals($groups['city'], $tuple->getValue('city'));
    }
    
    /**
     * Tests the 'toRow' method
     *
     */
    public function testToRow()
    {
        $groups = array('city'=>'Baltimore', 'state'=>'Maryland', 'country'=>'United States');
        $c = $this->_getNewCollectionWithRandomValues();
        $count = count($c);
        
        $tuple = new Xyster_Data_Tuple($groups, $c);
        
        $fields = array(Xyster_Data_Field::named('city'),
            Xyster_Data_Field::named('state'), Xyster_Data_Field::count('foo'));
        
        $row = array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>$count);
        $this->assertEquals($row, $tuple->toRow($fields));
    }
    
    /**
     * Tests the 'makeTuples' static method
     *
     */
    public function testMakeTuples()
    {
        $set = new Xyster_Data_Set();
        
        $fields = array(Xyster_Data_Field::group('city'),
            Xyster_Data_Field::group('state'), Xyster_Data_Field::count('foo'));
            
        $collection = array(
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'baz'),
            array('city'=>'Philadelphia', 'state'=>'Pennsylvania', 'foo'=>'lol'),
            array('city'=>'Columbia', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'wtf'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'lol')
            );
            
        $having = array(Xyster_Data_Expression::eq('max(state)','Maryland'));
        Xyster_Data_Tuple::makeTuples($set, $collection, $fields, $having, 2, 1 );
        
        $this->assertEquals(2, count($set));
    }
    
    /**
     * Tests the 'makeTuples' static method with bad types
     *
     */
    public function testMakeTuplesBadFields()
    {
        $set = new Xyster_Data_Set();
        
        $fields = array(Xyster_Data_Field::group('city'),
            Xyster_Data_Field::group('state'), Xyster_Data_Sort::asc('foo'));
            
        $collection = array(
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'baz'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'wtf'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'lol')
            );
        
        $this->setExpectedException('Xyster_Data_Set_Exception');
        $tuples = Xyster_Data_Tuple::makeTuples($set, $collection, $fields);
    }
    
    /**
     * Tests the 'makeTuples' static method with no grouped field
     *
     */
    public function testMakeTuplesNoGroup()
    {
        $set = new Xyster_Data_Set();
        
        $fields = array(Xyster_Data_Field::count('city'));
            
        $collection = array(
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'baz'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'wtf'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'lol')
            );
        
        $this->setExpectedException('Xyster_Data_Set_Exception');
        $tuples = Xyster_Data_Tuple::makeTuples($set, $collection, $fields);
    }
}