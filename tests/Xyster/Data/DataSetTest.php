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
namespace XysterTest\Data;
use Xyster\Data\Set;
/**
 * Test for Set
 *
 */
class DataSetTest extends \XysterTest\Collection\SortableSetTest
{
    protected $_className = '\Xyster\Data\Set';
    
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

    /**
     * Tests using the constructor with bad items
     * @expectedException \Xyster\Data\DataException
     */
    public function testConstructBadItem()
    {
        $badItems = \Xyster\Collection\Collection::using(array(1,2,3,4));
        $set = $this->_getNewCollection($badItems);
    }
    
    /**
     * Tests the 'add' method
     *
     */
    public function testAdd()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $set->add(array('foo'=>'bar', 'answer'=>42));
        
        $this->assertEquals(2, $set->getColumns()->count());
        $this->assertEquals('[foo,answer]', (string)$set->getColumns());
    }
    
    /**
     * Tests the 'add' method with a bad argument
     * @expectedException \Xyster\Data\DataException
     */
    public function testAddBadItem()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $set->add(1234); 
    }
    
    /**
     * Tests the 'addColumn' method
     *
     */
    public function testAddColumn()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $set->addColumn('foo');
        
        $this->assertEquals(1, $set->getColumns()->count());
        $this->assertEquals('[foo]', (string)$set->getColumns());
    }
    
    /**
     * Tests the 'addColumn' method 
     * @expectedException \Xyster\Data\DataException
     */
    public function testAddColumnIfItems()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $set->add(array('foo'=>'bar', 'answer'=>42));
        $set->addColumn('city');
    }
    
    /**
     * Tests the 'aggregate' method with count
     *
     */
    public function testAggregateCount()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        $this->assertEquals($set->count(), $set->aggregate(\Xyster\Data\Symbol\Field::count('foo')));
    }
    
    /**
     * Tests the 'aggregate' method with avg
     *
     */
    public function testAggregateAvg()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $total = 0;
        foreach( $this->_items as $item ) {
            $set->add($item);
            $total += $item['foo'];
        }
        $avg = $total / count($this->_items);
        $this->assertEquals($avg, $set->aggregate(\Xyster\Data\Symbol\Field::avg('foo')));
    }
    
    /**
     * Tests the 'aggregate' method with max
     *
     */
    public function testAggregateMax()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $max = 0;
        foreach( $this->_items as $item ) {
            $set->add($item);
            if ( $item['foo'] > $max ) {
                $max = $item['foo'];
            }
        }
        $this->assertEquals($max, $set->aggregate(\Xyster\Data\Symbol\Field::max('foo')));
    }
    
    /**
     * Tests the 'aggregate' method with min
     *
     */
    public function testAggregateMin()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $min = 19;
        foreach( $this->_items as $item ) {
            $set->add($item);
            if ( $item['foo'] < $min ) {
                $min = $item['foo'];
            }
        }
        $this->assertEquals($min, $set->aggregate(\Xyster\Data\Symbol\Field::min('foo')));
    }
    
    /**
     * Tests the 'aggregate' method with sum
     *
     */
    public function testAggregateSum()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */
        $total = 0;
        foreach( $this->_items as $item ) {
            $set->add($item);
            $total += $item['foo'];
        }
        $this->assertEquals($total, $set->aggregate(\Xyster\Data\Symbol\Field::sum('foo')));
    }
    
    /**
     * Tests the 'fetchColumn' method
     *
     */
    public function testFetchColumn()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        $fetched = $set->fetchColumn('foo');
        $this->assertEquals($set->count(), count($fetched));
        $expected = array();
        foreach( $set as $item ) {
            $expected[] = $item->foo;
        }
        $this->assertEquals($expected, $fetched);
    }
    
    /**
     * Tests the 'fetchColumn' method with a field
     *
     */
    public function testFetchColumnWithField()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        $fetched = $set->fetchColumn(\Xyster\Data\Symbol\Field::named('foo'));
        $this->assertEquals($set->count(), count($fetched));
        $expected = array();
        foreach( $set as $item ) {
            $expected[] = $item->foo;
        }
        $this->assertEquals($expected, $fetched);
    }
    
    /**
     * Tests the 'fetchOne' method
     *
     */
    public function testFetchOne()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
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
    
    /**
     * Tests the 'fetchPairs' method
     *
     */
    public function testFetchPairs()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */

        $expected = array();
        foreach( $this->_presidents as $pres ) {
            $set->add($pres);
            $expected[$pres['last']] = $pres['first'];
        }
        
        $this->assertEquals($expected, $set->fetchPairs('last', 'first'));
    }
    
    /**
     * Tests the 'fetchPairs' method with fields
     *
     */
    public function testFetchPairsWithFields()
    {
        $set = $this->_getNewCollection();
        /* @var $set Set */

        $expected = array();
        foreach( $this->_presidents as $pres ) {
            $set->add($pres);
            $expected[$pres['last']] = $pres['first'];
        }
        
        $this->assertEquals($expected, $set->fetchPairs(\Xyster\Data\Symbol\Field::named('last'),
             \Xyster\Data\Symbol\Field::named('first')));
    }
    
    /**
     * Tests the 'filter' method
     *
     */
    public function testFilter()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        
        $values = $set->toArray();
        $filter = \Xyster\Data\Symbol\Expression::gt('foo',5);
        $filtered = array_filter($values, array($filter,'evaluate'));
        
        $set->filter($filter);
        
        $this->assertSame(array_values($filtered), $set->toArray());
    }
    
    /**
     * Tests passing a sort object to the 'sortBy' method
     *
     */
    public function testSortByOneSort()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        
        $sort = \Xyster\Data\Symbol\Sort::asc('foo');
        $sorts = new \Xyster\Data\Symbol\SortClause($sort);
        
        $values = $set->toArray();
        $set->sortBy($sort);
        
        $comparator = new \Xyster\Data\Comparator($sorts);
        usort($values, array($comparator, 'compare'));
        $this->assertSame($values, $set->toArray());
    }
    
    /**
     * Tests passing an array of sort objects
     *
     */
    public function testSortByArrayOfSorts()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        $first = $set->getIterator()->current();
        $set->add( clone $first );
        
        $array = array(\Xyster\Data\Symbol\Sort::asc('foo'));
        $sorts = new \Xyster\Data\Symbol\SortClause($array[0]);
        
        $values = $set->toArray();
        $set->sortBy($array);
        
        $comparator = new \Xyster\Data\Comparator($sorts);
        usort($values, array($comparator, 'compare'));
        $this->assertSame($values, $set->toArray());
    }
    
    /**
     * Tests passing a bad type to the 'sortBy' method
     * @expectedException \Xyster\Data\DataException
     */
    public function testSortByBadType()
    {
        $set = $this->_getNewCollectionWithRandomValues();
        /* @var $set Set */
        $set->sortBy('abc123');
    }
}