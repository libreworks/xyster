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
 * @copyright Copyright (c) 2007 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Call Xyster_Container_Parameter_BasicTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Container_Parameter_BasicTest::main');
}

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Container/Parameter/Basic.php';

/**
 * Test class for Xyster_Container_Parameter_Basic.
 * Generated by PHPUnit on 2007-12-19 at 20:02:43.
 */
class Xyster_Container_Parameter_BasicTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container_Parameter_Basic
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Container_Parameter_BasicTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Xyster_Container_Parameter_Basic();
    }

    /**
     * Tests the 'accept' method
     */
    public function testAccept()
    {
        require_once 'Xyster/Container/Visitor/Mock.php';
        $visitor = new Xyster_Container_Visitor_Mock;
        $this->object->accept($visitor);
        $this->assertEquals(1, $visitor->getCalled('visitParameter'));
    }

    /**
     * Tests the 'isResolvable' method
     */
    public function testIsResolvable()
    {
        require_once 'Xyster/Container.php';
        $container = new Xyster_Container;
        $class = new Xyster_Type('TestControllerAction');
        $container->addComponent($class);
        require_once 'Zend/Controller/Response/Http.php';
        $container->addComponent(new Xyster_Type('Zend_Controller_Response_Http'));
        $constructor = $class->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $parameters = $constructor->getParameters();
        $parameter = $parameters[1];
        $return = $this->object->isResolvable($container, null, $parameter);
        $this->assertTrue($return);
    }

    /**
     * Tests the 'resolveInstance' method
     */
    public function testResolveInstance()
    {
        require_once 'Xyster/Container.php';
        $container = new Xyster_Container;
        $class = new Xyster_Type('TestControllerAction');
        $container->addComponent($class);
        require_once 'Zend/Controller/Response/Http.php';
        $container->addComponent(new Xyster_Type('Zend_Controller_Response_Http'));
        $constructor = $class->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $parameters = $constructor->getParameters();
        $parameter = $parameters[1];
        $return = $this->object->resolveInstance($container, null, $parameter);
        $this->assertType('Zend_Controller_Response_Abstract', $return);
    }
    
    /**
     * @todo Implement testResolveInstanceArray().
     */
    public function testResolveInstanceArray()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    /**
     * @todo Implement testResolveInstanceScalar().
     */
    public function testResolveInstanceScalar()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    /**
     * @todo Implement testVerify().
     */
    public function testVerify()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}

require_once 'Zend/Controller/Action.php';

class TestControllerAction extends Zend_Controller_Action
{
        
}

// Call Xyster_Container_Parameter_BasicTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Container_Parameter_BasicTest::main') {
    Xyster_Container_Parameter_BasicTest::main();
}