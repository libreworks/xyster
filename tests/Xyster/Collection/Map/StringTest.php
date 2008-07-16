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
// Call Xyster_Collection_Map_StringTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Collection_Map_StringTest::main');
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Xyster/Collection/Map/String.php';

/**
 * Test for Xyster_Collection_Map
 *
 */
class Xyster_Collection_Map_StringTest extends Xyster_Collection_Map_TestCommon
{
    protected $_className = 'Xyster_Collection_Map_String';

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Collection_Map_StringTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests the constructor
     */
    public function testConstruct()
    {
        $c = $this->_getNewMapWithRandomValues();
        $c2 = new Xyster_Collection_Map_String($c);
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
        $this->assertFalse($c->containsKey(-1)); // non-existant key
        
        $this->setExpectedException('Xyster_Collection_Exception');
        $c->containsKey($this->_getNewValue());
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
        $this->assertNull($map->get(-1)); // non-existant key
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
            $this->assertTrue($c->containsKey($key)) ;
            $this->assertTrue($c->containsValue($value));
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
        
        $this->setExpectedException('Xyster_Collection_Exception');
        $c->set($this->_getNewValue(), $this->_getNewValue());
    }
    
    protected function _getNewKey()
    {
        return rand(1, 1000);
    }
}

// Call Xyster_Collection_Map_StringTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Collection_Map_StringTest::main') {
    Xyster_Collection_Map_StringTest::main();
}
