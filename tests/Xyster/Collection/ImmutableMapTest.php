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
 * Test class for immutable map
 */
class Xyster_Collection_ImmutableMapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Collection_Map
     */
    protected $_c;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        $this->_c = Xyster_Collection::fixedMap($this->_getNewMapWithRandomValues());
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
        $this->_c->merge($this->_getNewMapWithRandomValues());
    }
    
    /**
     * Tests the 'remove' method
     *
     */
    public function testRemove()
    {
        $pre = $this->_c->count();
            
        try {
            $this->_c->remove($this->_getNewKey());
            $this->fail('No exception thrown for remove from immutable');
        } catch ( Exception $thrown ) {
            $this->assertEquals($pre, $this->_c->count());
        }
    }
    
    /**
     * Tests the 'set' method
     *
     */
    public function testSet()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->set($this->_getNewKey(), $this->_getNewValue());
    }
    
    protected function _addRandomValues( Xyster_Collection_Map $c )
    {
        for( $i=0; $i<rand(2, 10); $i++ ) {
            $c->set($this->_getNewKey(), $this->_getNewValue());
        }
    }
    
    protected function _getNewKey()
    {
        return new Xyster_Collection_Test_Key(md5(rand(101, 200)));
    }
    
    protected function _getNewValue()
    {
        return new Xyster_Collection_Test_Value(md5(rand(0, 100)));
    }
    
    /**
     * @return Xyster_Collection_Map
     */
    protected function _getNewMap( $arg = null )
    {
        $class = 'Xyster_Collection_Map';
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