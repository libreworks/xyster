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
use Xyster\Collection\Collection;
/**
 * Test for Xyster_Collection.
 *
 * I have no idea why I can't name it "CollectionTest.php" ?
 */
class TheCollectionTest extends BaseCollectionTest
{
    /**
     * Tests the empty list method
     */
    public function testEmptyList()
    {
    	$list = Collection::emptyList();
    	$this->assertType('\Xyster\Collection\EmptyList', $list);
    	$this->assertSame($list, Collection::emptyList());
    }

    /**
     * Tests the empty map method
     */
    public function testEmptyMap()
    {
        $map = Collection::emptyMap();
        $this->assertType('\Xyster\Collection\EmptyMap', $map);
        $this->assertSame($map, Collection::emptyMap());
    }

    /**
     * Tests the empty set method
     */
    public function testEmptySet()
    {
        $set = Collection::emptySet();
        $this->assertType('\Xyster\Collection\EmptyList', $set);
        $this->assertSame($set, Collection::emptySet());
    }

    /**
     * Tests the fixed collection method
     * @expectedException \Xyster\Collection\Exception
     */
    public function testFixedCollection()
    {
        $c = Collection::fixedCollection( new Collection() );
        $c->clear();
    }
    
    /**
     * Tests the fixed list method
     * @expectedException \Xyster\Collection\Exception
     */
    public function testFixedList()
    {
        $c = Collection::fixedList( new \Xyster\Collection\ArrayList() );
        $c->clear();
    }
    
    /**
     * Tests the fixed map method
     * @expectedException \Xyster\Collection\Exception
     */
    public function testFixedMap()
    {
        $c = Collection::fixedMap(new \Xyster\Collection\Map());
        $c->clear();
    }
    
    /**
     * Tests the fixed set method
     * @expectedException \Xyster\Collection\Exception
     */
    public function testFixedSet()
    {
        $c = Collection::fixedSet( new \Xyster\Collection\Set() );
        $c->clear();
    }
        
    /**
     * Tests the 'using' method
     */
    public function testUsing()
    {
        $c = Collection::using(array(1, 2, 3, 4, 5));
        $this->assertType('\Xyster\Collection\ICollection', $c);
        $this->assertEquals(5, $c->count());
    }
    
    /**
     * Tests the 'toString' method
     */
    public function testToString()
    {
        $c = Collection::using(array(1, 2, 3, 4, 5));
        $this->assertEquals('[1,2,3,4,5]', (string)$c);
    }
}
