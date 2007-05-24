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
 * Test class for immutable collection
 */
class Xyster_Collection_ImmutableCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Collection
     */
    protected $_c;
    
    public function setUp()
    {
        $this->_c = Xyster_Collection::fixedCollection($this->_getNewCollectionWithRandomValues());
    }
    public function testAdd()
    {
        try {
            $this->_c->add( null );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for add to immutable');
    }
    public function testClear()
    {
        try {
            $this->_c->clear();
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for clear of immutable');
    }
    public function testMerge()
    {
        try {
            $this->_c->merge( $this->_getNewCollectionWithRandomValues() );
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail('No exception thrown for merge with immutable');
    }
    public function testRemove()
    {
        try {
            $pre = $this->_c->count();
            $this->_c->remove( null );
        } catch ( Exception $thrown ) {
            $this->assertEquals($pre,$this->_c->count());
            return;
        }
        $this->fail('No exception thrown for remove from immutable');
    }
    public function testRemoveAll()
    {
        try {
            $pre = $this->_c->count();
            $this->_c->removeAll( $this->_getNewCollectionWithRandomValues() );
        } catch ( Exception $thrown ) {
            $this->assertEquals($pre,$this->_c->count());
            return;
        }
        $this->fail('No exception thrown for remove all from immutable');
    }
    public function testRetainAll()
    {
        try {
            $pre = $this->_c->count();
            $this->_c->retainAll( $this->_getNewCollectionWithRandomValues() );
        } catch ( Exception $thrown ) {
            $this->assertEquals($pre,$this->_c->count());
            return;
        }
        $this->fail('No exception thrown for retain all in immutable');
    }
    
    protected function _addRandomValues( Xyster_Collection $c )
    {
        for( $i=0; $i<rand(2,10); $i++ ) {
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
        $class = 'Xyster_Collection';
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