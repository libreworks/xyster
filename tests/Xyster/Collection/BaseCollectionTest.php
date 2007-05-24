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
 * @subpackage Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Xyster_Collection
 */
require_once 'Xyster/Collection.php';
/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_BaseCollectionTest extends PHPUnit_Framework_TestCase
{
    protected $_className = 'Xyster_Collection';
    
    public function testContains()
    {
        $c = $this->_getNewCollection();
        $value = $this->_getNewValue();
        $c->add($value);
        $this->assertTrue($c->contains($value));
    }
    public function testContainsAll()
    {
        $c = $this->_getNewCollection();
        $this->_addRandomValues($c);
        $coll = $this->_getNewCollection($c);
        $this->assertTrue( $c->containsAll($coll) );
    }
    public function testContainsAny()
    {
        $c = $this->_getNewCollection();
        $this->_addRandomValues($c);
        $value = $this->_getNewValue();
        $c->add($value);
        $coll = $this->_getNewCollection();
        $coll->add($value);
        $coll->add( $this->_getNewValue() );
        $this->assertTrue( $c->containsAny($coll) );
    }
    public function testCount()
    {
        $c = $this->_getNewCollection();
        $c->add(123);
        $c->add(456);
        $c->add(789);
        $this->assertEquals(3,$c->count());
    }
    public function testAdd()
    {
        $c = $this->_getNewCollection();
        $pre = $c->count();
        $c->add( $this->_getNewValue() );
        $post = $c->count();
        $this->assertTrue( $pre < $post );
    }
    public function testMerge()
    {
        $c = $this->_getNewCollection();
        $this->_addRandomValues($c);
        $coll = $this->_getNewCollection();
        $this->_addRandomValues($coll);
        $c->merge($coll);
        $this->assertTrue( $c->containsAll($coll) );
    }
    public function testIsEmpty()
    {
        $c = $this->_getNewCollection();
        $this->assertEquals($c->count()==0,$c->isEmpty());
    }
    public function testClear()
    {
        $c = $this->_getNewCollection();
        $this->_addRandomValues($c);
        $c->clear();
        $this->assertEquals(0,$c->count());
    }
    public function testRemove()
    {
        $c = $this->_getNewCollection();
        $value = $this->_getNewValue();
        $c->add($value);
        $pre = $c->count();
        $c->remove($value);
        $post = $c->count();
        $this->assertTrue( $pre > $post );
    }
    public function testRemoveAll()
    {
        $c = $this->_getNewCollection();
        $val = $this->_getNewValue();
        $val2 = $this->_getNewValue();
        $val3 = $this->_getNewValue();
        $c->add( $val );
        $c->add( $val2 );
        $c->add( $val3 );
        $coll = $this->_getNewCollection();
        $coll->add( $val2 );
        $coll->add( $val3 );
        $c->removeAll($coll);
        $this->assertFalse($c->containsAny($coll));
    }
    public function testRetainAll()
    {
        $c = $this->_getNewCollection();
        $val = $this->_getNewValue();
        $val2 = $this->_getNewValue();
        $val3 = $this->_getNewValue();
        $val4 = 42; // the answer to everything
        $c->add( $val );
        $c->add( $val2 );
        $c->add( $val3 );
        $coll = $this->_getNewCollection();
        $coll->add( $val2 );
        $coll->add( $val3 );
        $c->retainAll($coll);
        $this->assertTrue($c->containsAll($coll));
        $coll2 = $this->_getNewCollection();
        $coll2->add( $val4 );
        $c->retainAll($coll2);
        $this->assertTrue($c->isEmpty());
    }
    public function testIterator()
    {
        $c = $this->_getNewCollection();
        $this->_addRandomValues($c);
        $count = $c->count();
        $it = $c->getIterator();
        $this->assertType('Iterator',$it);
        $idx = 0;
        foreach( $it as $v ) {
            $idx++;
        }
        $this->assertEquals($count,$idx);
    }
    public function testToArray()
    {
        $c = $this->_getNewCollection();
        $c->add(123);
        $c->add(456);
        $c->add(789);
        $this->assertArrayHasKey(2,$c->toArray()); // 3 elements in array
    }
    protected function _addRandomValues( Xyster_Collection_Interface $c )
    {
        for( $i=0; $i<rand(3,10); $i++ ) {
            $c->add( $this->_getNewValue() );
        }
    }
    protected function _getNewValue()
    {
        return new Xyster_Collection_Test_Value(md5(rand(0,100)));
    }
    /**
     * @return Xyster_Collection
     */
    protected function _getNewCollection( $arg = null )
    {
        $class = $this->_className;
        return new $class( $arg );    
    }
    /**
     * @return Xyster_Collection
     */
    protected function _getNewCollectionWithRandomValues()
    {
        $c = $this->_getNewCollection();
        $this->_addRandomValues($c);
        return $c;
    }
}
/**
 * A simple test class for collection items
 *
 */
class Xyster_Collection_Test_Value
{
    public $foo;
   
    public function __construct( $value )
    {
        $this->foo = $value;
    }
}