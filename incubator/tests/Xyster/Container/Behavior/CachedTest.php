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
 * @subpackage Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Call Xyster_Container_Behavior_CachedTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Container_Behavior_CachedTest::main');
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'StoredTest.php';

require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Container/Behavior/Cached.php';

/**
 * Test class for Xyster_Container_Behavior_Cached.
 * Generated by PHPUnit on 2008-01-06 at 13:10:43.
 */
class Xyster_Container_Behavior_CachedTest extends Xyster_Container_Behavior_StoredTest
{
    /**
     * @var Xyster_Container_Behavior_Cached
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Container_Behavior_CachedTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Xyster_Container_Behavior_Cached($this->delegate, $this->reference);
    }
    
    /**
     * Tests a null argument to the constructor will create a new reference
     */
    public function testConstructor()
    {
    	$this->object = new Xyster_Container_Behavior_Cached($this->delegate);
    	$this->assertAttributeEquals($this->reference, '_instanceReference', $this->object);
    }
    
    /**
     * Tests the 'get' and 'set' methods
     */
    public function testGetAndSet()
    {
        $instance = new SplObjectStorage();
        $this->reference->set($instance);
        $this->assertAttributeSame($instance, '_instance', $this->reference);
        $return = $this->object->getStoredObject();
        $this->assertSame($instance, $return);
    }
}

// Call Xyster_Container_Behavior_CachedTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Container_Behavior_CachedTest::main') {
    Xyster_Container_Behavior_CachedTest::main();
}
