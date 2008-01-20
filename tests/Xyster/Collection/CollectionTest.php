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
require_once 'Xyster/Collection/BaseCollectionTest.php';
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
     * Tests the 'using' method
     *
     */
    public function testUsing()
    {
        $c = Xyster_Collection::using( array(1,2,3,4,5) );
        $this->assertType('Xyster_Collection_Interface',$c);
        $this->assertEquals($c->count(),5);
    }
    
    /**
     * Tests the fixed collection method
     *
     */
    public function testFixedCollection()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $c = Xyster_Collection::fixedCollection( new Xyster_Collection() );
        $c->clear();
    }
    
    /**
     * Tests the fixed set method
     *
     */
    public function testFixedSet()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $c = Xyster_Collection::fixedSet( new Xyster_Collection_Set() );
        $c->clear();
    }
    
    /**
     * Tests the fixed list method
     *
     */
    public function testFixedList()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $c = Xyster_Collection::fixedList( new Xyster_Collection_List() );
        $c->clear();
    }
    
    /**
     * Tests the fixed map method
     *
     */
    public function testFixedMap()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $c = Xyster_Collection::fixedMap( new Xyster_Collection_Map() );
        $c->clear();
    }
}