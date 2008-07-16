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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * PHPUnit test case
 */
require_once dirname(__FILE__) . '/BaseCollectionTest.php';
/**
 * Xyster_Collection
 */
require_once 'Xyster/Collection.php';
/**
 * Xyster_Collection_Map
 */
require_once 'Xyster/Collection/Map.php';
/**
 * Xyster_Collection_List
 */
require_once 'Xyster/Collection/List.php';
/**
 * Xyster_Collection_Set
 */
require_once 'Xyster/Collection/Set.php';
/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_CollectionTest extends Xyster_Collection_BaseCollectionTest
{
    /**
     * Tests the empty list method
     */
    public function testEmptyList()
    {
    	$list = Xyster_Collection::emptyList();
    	$this->assertType('Xyster_Collection_List_Empty', $list);
    	$this->assertSame($list, Xyster_Collection::emptyList());
    }
    
    /**
     * Tests the fixed collection method
     */
    public function testFixedCollection()
    {
        $c = Xyster_Collection::fixedCollection( new Xyster_Collection() );
        $this->setExpectedException('Xyster_Collection_Exception');
        $c->clear();
    }
    
    /**
     * Tests the fixed list method
     */
    public function testFixedList()
    {
        $c = Xyster_Collection::fixedList( new Xyster_Collection_List() );
        $this->setExpectedException('Xyster_Collection_Exception');
        $c->clear();
    }
    
    /**
     * Tests the fixed map method
     */
    public function testFixedMap()
    {
        $c = Xyster_Collection::fixedMap(new Xyster_Collection_Map());
        $this->setExpectedException('Xyster_Collection_Exception');
        $c->clear();
    }
    
    /**
     * Tests the fixed set method
     */
    public function testFixedSet()
    {
        $c = Xyster_Collection::fixedSet( new Xyster_Collection_Set() );
        $this->setExpectedException('Xyster_Collection_Exception');
        $c->clear();
    }
        
    /**
     * Tests the 'using' method
     */
    public function testUsing()
    {
        $c = Xyster_Collection::using(array(1, 2, 3, 4, 5));
        $this->assertType('Xyster_Collection_Interface', $c);
        $this->assertEquals(5, $c->count());
    }
    
    /**
     * Tests the 'toString' method
     */
    public function testToString()
    {
        $c = Xyster_Collection::using(array(1, 2, 3, 4, 5));
        $this->assertEquals('[1,2,3,4,5]', (string)$c);
    }
}