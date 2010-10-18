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
use Xyster\Collection\Map;
/**
 * Test for Xyster_Collection_Map
 *
 */
class MapTest extends MapTestCommon
{
    /**
     * Tests the basic constructor
     */
    public function testConstruct()
    {
        $c = $this->_getNewMapWithRandomValues();
        $map = new Map($c);
        $this->assertEquals($map->keys(), $c->keys());
    }
    
    /**
     * Tests the 'containsKey' method
     */
    public function testContainsKey()
    {
        $c = $this->_getNewMap();
        $key = $this->_getNewKey();
        $c->set($key, $this->_getNewValue());
        $this->assertTrue($c->containsKey($key));
        $this->assertFalse($c->containsKey($this->_getNewValue())); // non-existant key
    }
    
    /**
     * Tests the 'entry' method
     */
    public function testEntry()
    {
        $c = $this->_getNewMapWithRandomValues();
        foreach( $c as $value ) {
            (string)$value;
        }
    }
    
    /**
     * Tests the 'get' method
     */
    public function testGet()
    {
        $map = $this->_getNewMap();
        $key = $this->_getNewKey();
        $value = $this->_getNewValue();
        $map->set($key, $value);
        $this->assertEquals($value, $map->get($key));
        $this->assertNull($map->get($value)); // non-existant key
    }
    
    /**
     * Tests the 'merge' method
     */
    public function testMerge()
    {
        $c = $this->_getNewMapWithRandomValues();
        $coll = $this->_getNewMapWithRandomValues();
        $c->merge($coll);
        foreach( $coll as $key=>$value ) {
            $this->assertTrue($c->containsKey($value->getKey())) ;
            $this->assertTrue($c->containsValue($value->getValue()));
        }
    }
    
    /**
     * Tests the 'set' method
     */
    public function testSet()
    {
        $c = $this->_getNewMap();
        $key = $this->_getNewKey();
        $value = $this->_getNewValue();
        $pre = $c->count();
        $c->set($key, $value);
        $post = $c->count();
        $this->assertTrue($pre < $post);
        $this->assertTrue($c->containsKey($key));
        $this->assertTrue($c->containsValue($value));
        $c->set($key, $this->_getNewValue()); // setting a pre-existing key
    }
}
