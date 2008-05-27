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
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Cdi.php';
require_once 'Xyster/Container.php';
require_once 'Xyster/Container/NameBinding/Parameter.php';
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
     * @var Xyster_Type
     */
    protected $key;
    
    /**
     * @var Xyster_Container
     */
    protected $container;

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
        $this->key = new Xyster_Type('Submarine');
        $this->container = new Xyster_Container;
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
        $this->container->addComponent('SubFuel');
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $return = $this->object->isResolvable($this->container, null, new Xyster_Type('SubFuel'), $nameBinding, true);
        $this->assertTrue($return);
    }

    /**
     * Tests the 'resolveInstance' method
     */
    public function testResolveInstance()
    {
        $this->container->addComponent('SubFuel');
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $return = $this->object->resolveInstance($this->container, null, new Xyster_Type('SubFuel'), $nameBinding, true);
        $this->assertType('SubFuel', $return);
    }
    
    /**
     * Tests the 'resolveInstance' method with ambiguous components
     *
     */
    public function testResolveInstanceAmbiguous()
    {
        $this->container->addComponent('Submarine')
            ->addComponent('SubFuel', 'myfuel1')
            ->addComponent('SubFuel', 'myfuel2');
        $adapter = $this->container->getComponentAdapterByType('Submarine');
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $this->setExpectedException('Xyster_Container_Parameter_Exception');
        $return = $this->object->resolveInstance($this->container, $adapter, new Xyster_Type('SubFuel'), $nameBinding, true);
    }
    
    /**
     * Tests the 'resolveInstance' method using parameter names
     *
     */
    public function testResolveInstanceParameterNames()
    {
    	$this->container->addComponent('Submarine')
            ->addComponent('SubFuel', 'fuel');
        $adapter = $this->container->getComponentAdapterByType('Submarine');
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $return = $this->object->resolveInstance($this->container, $adapter, new Xyster_Type('SubFuel'), $nameBinding, true);
        $this->assertType('SubFuel', $return);
    }
    
    /**
     * Tests the 'resolveInstance' method using no parameter names
     *
     */
    public function testResolveInstanceParameterNoNames()
    {
        $this->container->addComponent('Submarine')
            ->addComponent('SubFuel', 'fuel')
            ->addComponent('SubFuel', 'fuel2');
        $adapter = $this->container->getComponentAdapter('fuel');
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $return = $this->object->resolveInstance($this->container, $adapter, new Xyster_Type('SubFuel'), $nameBinding, false);
        $this->assertType('SubFuel', $return);
    }
    
    /**
     * Tests the 'resolveInstance' method with a key defined for the parameter
     *
     */
    public function testResolveInstanceKey()
    {
        $this->key = 'MyTestKey';
        $this->object = new Xyster_Container_Parameter_Basic($this->key);
        $this->container->addComponent('SubFuel', $this->key);
        $type = new Xyster_Type('Submarine');
        $constructor = $type->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $return = $this->object->resolveInstance($this->container, null, new Xyster_Type('SubFuel'), $nameBinding, true);
        $this->assertType('SubFuel', $return);
    }
    
    /**
     * Tests the 'resolveInstance' method with a null return
     *
     */
    public function testResolveInstanceNull()
    {
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 0);
        
        $return = $this->object->resolveInstance($this->container, null, new Xyster_Type('Sailor'), $nameBinding, true);
        $this->assertNull($return);
    }
    
    /**
     * Tests the 'resolveInstance' method for an array 
     */
    public function testResolveInstanceArray()
    {
        $this->container->addComponent(new Xyster_Type('array'));
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 2);
        
        $return = $this->object->resolveInstance($this->container, null, new Xyster_Type('array'), $nameBinding, true);
        $this->assertType('array', $return);
    }
    
    /**
     * Tests the 'resolveInstance' method for a scalar value
     */
    public function testResolveInstanceScalar()
    {
        $this->key = new Xyster_Type('SubmarineCaptain');
        $this->container = new Xyster_Container;
        $this->container->addConfig('name', 'Captain Crunch');
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 1);
        
        $return = $this->object->resolveInstance($this->container, null, new Xyster_Type('string'), $nameBinding, true);
        $this->assertSame('Captain Crunch', $return);
    }
    
    /**
     * Tests the 'standard' method
     */
    public function testStandard()
    {
        $standard = Xyster_Container_Parameter_Basic::standard();
        $this->assertType('Xyster_Container_Parameter_Basic', $standard);
        $this->assertSame(Xyster_Container_Parameter_Basic::standard(), $standard);
    }
    
    /**
     * Tests the 'verify' method
     */
    public function testVerify()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * Test a fail verify call
     */
    public function testVerifyFail()
    {
        $constructor = $this->key->getClass()->getConstructor(); /* @var $constructor ReflectionMethod */
        $nameBinding = new Xyster_Container_NameBinding_Parameter($constructor, 0);
        
        $this->setExpectedException('Xyster_Container_Parameter_Exception');
        $this->object->verify($this->container, null, new Xyster_Type('Sailor'), $nameBinding, true);
    }
}

// Call Xyster_Container_Parameter_BasicTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Container_Parameter_BasicTest::main') {
    Xyster_Container_Parameter_BasicTest::main();
}