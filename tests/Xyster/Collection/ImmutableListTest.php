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
require_once 'Xyster/Collection/ImmutableCollectionTest.php';
/**
 * Xyster_Collection_List
 */
require_once 'Xyster/Collection/List.php';
/**
 * Test class for immutable collection
 */
class Xyster_Collection_ImmutableListTest extends Xyster_Collection_ImmutableCollectionTest
{
    /**
     * @var Xyster_List
     */
    protected $_c;
    
    public function setUp()
    {
        $this->_c = new Xyster_Collection_List( $this->_getNewCollectionWithRandomValues(), true );
    }
    public function testInsert()
    {
        try {
            $this->_c->insert( 0, null );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for insert into immutable');
    }
    public function testinsertAll()
    {
        try {
            $this->_c->insertAll( 0, $this->_getNewCollectionWithRandomValues() );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for insert all into immutable');
    }
    public function testRemoveAt()
    {
        try {
            $this->_c->removeAt( 0 );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for remove from immutable');
    }
    public function testSet()
    {
        try {
            $this->_c->set( 0, null );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for set immutable');
    }
    public function testSlice()
    {
        try {
            $this->_c->slice( 0, 1 );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for slice from immutable'); 
    }
}