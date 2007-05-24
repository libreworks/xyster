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
    public function testUsing()
    {
        $c = Xyster_Collection::using( array(1,2,3,4,5) );
        $this->assertType('Xyster_Collection_Interface',$c);
        $this->assertEquals($c->count(),5);
    }
    public function testFixedCollection()
    {
        $c = Xyster_Collection::fixedCollection( new Xyster_Collection() );
        try {
            $c->clear();
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Exception not thrown when clearing fixed collection");
    }
    public function testFixedSet()
    {
        $c = Xyster_Collection::fixedSet( new Xyster_Collection_Set() );
        try {
            $c->clear();
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Exception not thrown when clearing fixed set");
    }
    public function testFixedList()
    {
        $c = Xyster_Collection::fixedList( new Xyster_Collection_List() );
        try {
            $c->clear();
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Exception not thrown when clearing fixed list");
    }
    public function testFixedMap()
    {
        $c = Xyster_Collection::fixedMap( new Xyster_Collection_Map() );
        try {
            $c->clear();
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Exception not thrown when clearing fixed map");
    }
}