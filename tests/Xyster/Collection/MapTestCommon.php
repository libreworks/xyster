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
 * @subpackage Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Collection;
use \Xyster\Collection\Map;
/**
 * Base tests for Xyster_Collection_Map* classes
 */
abstract class MapTestCommon extends \PHPUnit_Framework_TestCase
{
    protected $_className = '\Xyster\Collection\Map';
    
    public function testContainsValue()
    {
        $c = $this->_getNewMap();
        $value = $this->_getNewValue();
        $c->set($this->_getNewKey(),$value);
        $this->assertTrue($c->containsValue($value));
        $this->assertFalse($c->containsValue($this->_getNewKey())); // non-existant value
    }
    public function testKeys()
    {
        $c = $this->_getNewMapWithRandomValues();
        $this->assertEquals( $c->keys()->count(), $c->count() );
    }
    public function testValues()
    {
        $c = $this->_getNewMapWithRandomValues();
        $this->assertEquals( $c->values()->count(), $c->count() );
    }
    public function testKeyFor()
    {
        $map = $this->_getNewMap();
        $key = $this->_getNewKey();
        $value = $this->_getNewValue();
        $map->set($key,$value);
        $this->assertEquals($key,$map->keyFor($value));
        $this->assertFalse($map->keyFor($this->_getNewKey()));  // non-existant value
    }
    public function testKeysFor()
    {
        $map = $this->_getNewMap();
        $key = $this->_getNewKey();
        $value = $this->_getNewValue();
        $map->set($key,$value);
        $keys = $map->keysFor($value);
        $this->assertType('\Xyster\Collection\ISet',$keys);
        $map->keysFor( $this->_getNewKey() ); // non-existant value
    }
    public function testCount()
    {
        $c = $this->_getNewMap();
        $c->set($this->_getNewKey(),123);
        $c->set($this->_getNewKey(),456);
        $c->set($this->_getNewKey(),789);
        $this->assertEquals(3,$c->count());
    }
    public function testIsEmpty()
    {
        $c = $this->_getNewMap();
        $this->assertEquals($c->count()==0,$c->isEmpty());
    }
    public function testClear()
    {
        $c = $this->_getNewMapWithRandomValues();
        $c->clear();
        $this->assertEquals(0,$c->count());
    }
    public function testRemove()
    {
        $c = $this->_getNewMap();
        $key = $this->_getNewKey();
        $value = $this->_getNewValue();
        $c->set($key,$value);
        $pre = $c->count();
        $c->remove($key);
        $post = $c->count();
        $this->assertTrue( $pre > $post );
    }
    public function testIterator()
    {
        $c = $this->_getNewMapWithRandomValues();
        $count = $c->count();
        $it = $c->getIterator();
        $this->assertType('\Iterator',$it);
        $idx = 0;
        foreach( $it as $k=>$v ) {
            $idx++;
        }
        $this->assertEquals($count,$idx);
    }
    public function testToArray()
    {
        $c = $this->_getNewMap();
        $c->set($this->_getNewKey(),123);
        $c->set($this->_getNewKey(),456);
        $c->set($this->_getNewKey(),789);
        $this->assertEquals(3,count($c->toArray())); // 3 elements in array
    }
    protected function _addRandomValues( \Xyster\Collection\IMap $c )
    {
        for( $i=0; $i<rand(3,10); $i++ ) {
            $c->set( $this->_getNewKey(), $this->_getNewValue() );
        }
    }
    protected function _getNewKey()
    {
        return new MapTestKey(md5(rand(101,200)));
    }
    protected function _getNewValue()
    {
        return new MapTestValue(md5(rand(0,100)));
    }
    /**
     * @return Xyster_Collection_Map
     */
    protected function _getNewMap( $arg = null )
    {
        $class = $this->_className;
        return new $class( $arg );    
    }
    /**
     * @return Xyster_Collection_Map
     */
    protected function _getNewMapWithRandomValues()
    {
        $c = $this->_getNewMap();
        $this->_addRandomValues($c);
        return $c;
    }
}
/**
 * A simple test class for map keys
 *
 */
class MapTestKey
{
    public $foo;
   
    public function __construct( $value )
    {
        $this->foo = $value;
    }
    public function __toString()
    {
        return $this->foo;
    }
}
/**
 * A simple test class for collection items
 *
 */
class MapTestValue
{
    public $foo;
   
    public function __construct( $value )
    {
        $this->foo = $value;
    }
    public function __toString()
    {
        return $this->foo;
    }
}