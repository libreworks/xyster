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
    
    /**
     * Sets up the tests
     *
     */
    public function setUp()
    {
        $this->_c = Xyster_Collection::fixedCollection($this->_getNewCollectionWithRandomValues());
    }
    
    /**
     * Tests the 'add' method
     *
     */
    public function testAdd()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->add(null);
    }
    
    /**
     * Tests the 'clear' method
     *
     */
    public function testClear()
    {
        $this->setExpectedException('Xyster_Collection_Exception');        
        $this->_c->clear();
    }
    
    /**
     * Tests the 'merge' method
     *
     */
    public function testMerge()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->merge($this->_getNewCollectionWithRandomValues());
    }
    
    /**
     * Tests the 'remove' method
     *
     */
    public function testRemove()
    {
        $pre = $this->_c->count();
        
        try {
            $this->_c->remove(null);
            $this->fail('No exception thrown for remove from immutable');
        } catch ( Xyster_Collection_Exception $thrown ) {
            $this->assertEquals($pre, $this->_c->count());
        }
    }
    
    /**
     * Tests the 'removeAll' method
     *
     */
    public function testRemoveAll()
    {
        $pre = $this->_c->count();
        
        try {
            $this->_c->removeAll($this->_getNewCollectionWithRandomValues());
            $this->fail('No exception thrown for remove all from immutable');
        } catch ( Xyster_Collection_Exception $thrown ) {
            $this->assertEquals($pre,$this->_c->count());
        }
    }
    
    /**
     * Tests the 'retainAll' method
     *
     */
    public function testRetainAll()
    {
        $pre = $this->_c->count();

        try {
            $this->_c->retainAll($this->_getNewCollectionWithRandomValues());
            $this->fail('No exception thrown for retain all in immutable');
        } catch ( Xyster_Collection_Exception $thrown ) {
            $this->assertEquals($pre, $this->_c->count());
        }
    }
    
    protected function _addRandomValues( Xyster_Collection $c )
    {
        for( $i=0; $i<rand(2,10); $i++ ) {
            $c->add($this->_getNewValue());
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
        return new $class($arg);    
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