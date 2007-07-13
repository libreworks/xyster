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
require_once 'Xyster/Collection/SortableSetTest.php';
/**
 * @see Xyster_Data_Set
 */
require_once 'Xyster/Data/Set.php';
/**
 * @see Xyster_Data_Aggregate
 */
require_once 'Xyster/Data/Aggregate.php';
/**
 * @see Xyster_Data_Comparator
 */
require_once 'Xyster/Data/Comparator.php';
/**
 * Test for Xyster_Data_Set
 *
 */
class Xyster_Data_SetTest extends Xyster_Collection_SortableSetTest
{
    protected $_className = 'Xyster_Data_Set';
    
    protected $_items = array(
            array('foo'=>2),
            array('foo'=>12),
            array('foo'=>5),
            array('foo'=>7),
            array('foo'=>19));
            
    protected $_presidents = array(
        array('first'=>'George', 'last'=>'Washington'),
        array('first'=>'Abraham', 'last'=>'Lincoln'),
        array('first'=>'Thomas', 'last'=>'Jefferson'),
        array('first'=>'Andrew', 'last'=>'Jackson'));

    public function testAdd()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $set->add(array('foo'=>'bar', 'answer'=>42));
        
        $this->assertEquals(2, $set->getColumns()->count());
        $this->assertEquals('[foo,answer]', (string)$set->getColumns());
    }
    public function testAddBadItem()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        try {
            $set->add(1234); 
        } catch ( Exception $thrown ) {
            // okay
	        return;
        }
        $this->fail("Exception not thrown");
    }
    
    public function testAddColumn()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $set->addColumn('foo');
        
        $this->assertEquals(1, $set->getColumns()->count());
        $this->assertEquals('[foo]', (string)$set->getColumns());
    }
    public function testAddColumnIfItems()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $set->add(array('foo'=>'bar', 'answer'=>42));
        try {
            $set->addColumn('city');
        } catch ( Exception $thrown ) {
            // okay
	        return;
        }
        $this->fail("Exception not thrown");
    }
    public function testAggregateCount()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Xyster_Data_Set */
        $this->assertEquals($set->count(), $set->aggregate(Xyster_Data_Field::count('foo')));
    }
    public function testAggregateAvg()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $total = 0;
        foreach( $this->_items as $item ) {
            $set->add($item);
            $total += $item['foo'];
        }
        $avg = $total / count($this->_items);
        $this->assertEquals($avg, $set->aggregate(Xyster_Data_Field::avg('foo')));
    }
    public function testAggregateMax()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $max = 0;
        foreach( $this->_items as $item ) {
            $set->add($item);
            if ( $item['foo'] > $max ) {
                $max = $item['foo'];
            }
        }
        $this->assertEquals($max, $set->aggregate(Xyster_Data_Field::max('foo')));
    }
    public function testAggregateMin()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $min = 19;
        foreach( $this->_items as $item ) {
            $set->add($item);
            if ( $item['foo'] < $min ) {
                $min = $item['foo'];
            }
        }
        $this->assertEquals($min, $set->aggregate(Xyster_Data_Field::min('foo')));
    }
    public function testAggregateSum()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */
        $total = 0;
        foreach( $this->_items as $item ) {
            $set->add($item);
            $total += $item['foo'];
        }
        $this->assertEquals($total, $set->aggregate(Xyster_Data_Field::sum('foo')));
    }
    public function testFetchColumn()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Xyster_Data_Set */
        $fetched = $set->fetchColumn('foo');
        $this->assertEquals($set->count(), count($fetched));
        $expected = array();
        foreach( $set as $item ) {
            $expected[] = $item->foo;
        }
        $this->assertEquals($expected, $fetched);
    }
    public function testFetchColumnWithField()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Xyster_Data_Set */
        $fetched = $set->fetchColumn(Xyster_Data_Field::named('foo'));
        $this->assertEquals($set->count(), count($fetched));
        $expected = array();
        foreach( $set as $item ) {
            $expected[] = $item->foo;
        }
        $this->assertEquals($expected, $fetched);
    }
    public function testFetchOne()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Xyster_Data_Set */
        $expected = null;
        foreach( $set as $item ) {
            foreach( $item as $k=>$v ) {
                $expected = $v;
                break;
            }
            break;
        }
        $this->assertEquals($expected, $set->fetchOne());
    }
    public function testFetchPairs()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */

        $expected = array();
        foreach( $this->_presidents as $pres ) {
            $set->add($pres);
            $expected[$pres['last']] = $pres['first'];
        }
        
        $this->assertEquals($expected, $set->fetchPairs('last', 'first'));
    }
    public function testFetchPairsWithFields()
    {
        $set = $this->_getNewCollection();
        /* @var $set Xyster_Data_Set */

        $expected = array();
        foreach( $this->_presidents as $pres ) {
            $set->add($pres);
            $expected[$pres['last']] = $pres['first'];
        }
        
        $this->assertEquals($expected, $set->fetchPairs(Xyster_Data_Field::named('last'),
             Xyster_Data_Field::named('first')));
    }
    public function testFilter()
    {
        
    }
    public function testSortBy()
    {
        
    }
}