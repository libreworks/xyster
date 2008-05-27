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
 * @package   Xyster_Application
 * @subpackage   UnitTests
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_ContainerTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Xyster/Container.php';
require_once 'Xyster/Container/Features.php';

/**
 * Test class for Xyster_Container.
 * Generated by PHPUnit on 2007-12-12 at 19:40:53.
 */
class Xyster_ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container
     */
    protected $object;
    
    /**
     * @var Xyster_Container
     */
    protected $child;
    
    /**
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     *
     */
    protected function setUp()
    {
        $this->object = new Xyster_Container;
        $this->child = new Xyster_Container(null, $this->object);
        $this->object->addChildContainer($this->child);
    }

    /**
     * Tests the 'accept' method
     */
    public function testAccept()
    {
        $this->object->addComponentInstance(new ArrayObject);
        require_once 'Xyster/Container/Visitor/Mock.php';
        $visitor = new Xyster_Container_Visitor_Mock;
        $this->object->accept($visitor);
        $this->assertEquals(2, $visitor->getCalled('visitContainer'));
        $this->assertEquals(1, $visitor->getCalled('visitComponentAdapter'));
    }

    /**
     * Tests the 'addAdapter' method
     */
    public function testAddAdapter()
    {
        require_once 'Xyster/Container/Adapter/Instance.php';
        $key = new Xyster_Type('ArrayObject');
        $adapter = new Xyster_Container_Adapter_Instance($key, new ArrayObject);
        $return = $this->object->addAdapter($adapter);
        $this->assertSame($this->object, $return);
        $adapter2 = $this->object->getComponentAdapter($key);
        $this->assertSame($adapter, $adapter2);
    }
    
    /**
     * Tests the 'addAdapter' method using the with() method beforehand
     *
     */
    public function testAddAdapterWith()
    {
        require_once 'Xyster/Container/Adapter/Instance.php';
        $key = new Xyster_Type('ArrayObject');
        $adapter = new Xyster_Container_Adapter_Instance($key, new ArrayObject);
        $return = $this->object->with(Xyster_Container_Features::CACHE())
            ->addAdapter($adapter);
        $this->assertSame($this->object, $return);
        $adapter2 = $this->object->getComponentAdapter($key);
        $this->assertType('Xyster_Container_Behavior_Cached', $adapter2);
    }

    /**
     * Tests the 'addChildContainer' method
     *
     */
    public function testAddChildContainer()
    {
        require_once 'Xyster/Container/Empty.php';
        $child = new Xyster_Container_Empty;
        $return = $this->object->addChildContainer($child);
        $this->assertSame($this->object, $return);
        $this->object->addChildContainer(new Xyster_Container_Immutable(new Xyster_Container));
    }
    
    /**
     * Tests the 'addChildContainer' method with a bad type
     */
    public function testAddChildContainerBad()
    {
        $this->setExpectedException('Xyster_Container_Exception');
        $this->object->addChildContainer($this->object);
    }
    
    /**
     * Tests the 'addChildContainer' method with a bad type
     */
    public function testAddChildContainerBad2()
    {
        $this->setExpectedException('Xyster_Container_Exception');
        $this->object->addChildContainer(new Xyster_Container_Immutable(new Xyster_Container_Immutable($this->object)));
    }
        
    /**
     * Tests the 'addComponent' method
     */
    public function testAddComponent()
    {
        $class = new Xyster_Type('Xyster_Collection_Map');
        $return = $this->object->addComponent($class);
        $this->assertSame($this->object, $return);
        $adapter = $this->object->getComponentAdapterByType($class);
        $this->assertType('Xyster_Container_Adapter', $adapter);
        $this->assertSame($class, $adapter->getKey());
    }
   
    /**
     * Tests the 'addComponent' method
     */
    public function testAddComponent2()
    {
        $class = new ReflectionClass('Xyster_Collection_Map');
        $return = $this->object->addComponent($class, 'myKey123');
        $this->assertSame($this->object, $return);
        $adapter = $this->object->getComponentAdapterByType('Xyster_Collection_Map');
        $this->assertType('Xyster_Container_Adapter', $adapter);
        $this->assertEquals('myKey123', $adapter->getKey());
    }
    
    /**
     * Tests the 'addComponent' method for a key that already exists
     *
     */
    public function testAddComponentExists()
    {
        $key = new Xyster_Type('Xyster_Collection_Map');
        $return = $this->object->addComponent($key, $key);
        $this->assertSame($this->object, $return);
        $adapter = $this->object->getComponentAdapterByType($key);
        $this->assertType('Xyster_Container_Adapter', $adapter);
        $this->assertSame($key, $adapter->getKey());
        $this->setExpectedException('Xyster_Container_Exception');
        $this->object->addComponent($key, $key);
    }
    
    /**
     * Tests the 'addComponent' method with an instance of an object
     *
     */
    public function testAddComponentInstance()
    {
        $instance = new ArrayObject;
        $return = $this->object->addComponentInstance($instance);
        $this->assertSame($this->object, $return);
        $key = new Xyster_Type('ArrayObject');
        $adapter = $this->object->getComponentAdapterByType($key);
        $this->assertType('Xyster_Container_Adapter_Instance', $adapter);
        $this->assertSame($instance, $adapter->getInstance($this->object));
        $this->assertEquals($key, $adapter->getImplementation());
    }

    /**
     * Tests the 'addConfig' method
     *
     */
    public function testAddConfig()
    {
        $key = 'uri';
        $value = 'http://localhost';
        $this->object->addConfig($key, $value);
        $this->assertSame($value, $this->object->getComponent('uri'));
    }
    
    /**
     * Tests the 'change' method
     */
    public function testChange()
    {
        $feature = Xyster_Container_Features::SDI();
        $expected = new Xyster_Collection_Map_String($feature);
        $this->object->change($feature);
        $this->assertAttributeEquals($expected, '_properties', $this->object);
    }

    /**
     * Tests the 'changeMonitor' method
     */
    public function testChangeMonitor()
    {
        $this->object->addComponent('ArrayObject'); // to put adapters in the container
        
        require_once 'Xyster/Container/Monitor/Null.php';
        $monitor = new Xyster_Container_Monitor_Null;
        $this->object->changeMonitor($monitor);
        $this->assertAttributeSame($monitor, '_monitor', $this->object);
    }

    /**
     * Tests the 'currentMonitor' method
     */
    public function testCurrentMonitor()
    {
        $this->assertAttributeSame($this->object->currentMonitor(), '_monitor', $this->object);
    }

    /**
     * Tests the 'getComponent' method
     */
    public function testGetComponent()
    {
        $type = new Xyster_Type('SplObjectStorage');
        $this->object->addComponent($type, 'objstrg');
        
        $this->assertType('SplObjectStorage', $this->object->getComponent($type));
        $this->assertType('SplObjectStorage', $this->object->getComponent('objstrg'));
        $this->assertNull($this->object->getComponent('nonexist'));
        $this->assertType('SplObjectStorage', $this->child->getComponent($type));
    }

    /**
     * Tests the 'getComponents' method
     */
    public function testGetComponents()
    {
        $this->object->addComponent('SplObjectStorage');
        
        $components = $this->object->getComponents();
        $this->assertSame(1, count($components));
        $this->assertType('Xyster_Collection_List_Interface', $components);
        $this->assertType('SplObjectStorage', $components[0]);
    }

    /**
     * Tests the 'getComponentAdapter' method
     */
    public function testGetComponentAdapter()
    {
        $this->object->addComponentInstance(new ArrayObject(array()), 'abc123');
        $adapter = $this->child->getComponentAdapter('abc123');
        $this->assertType('Xyster_Container_Adapter', $adapter);
    }
    
    /**
     * Tests the 'getComponentAdapterByType' method
     */
    public function testGetComponentAdapterByType()
    {
        $this->object->addComponentInstance(new ArrayObject(array()), 'arrayObj');
        
        $adapter = $this->object->getComponentAdapterByType('ArrayObject');
        $this->assertType('Xyster_Container_Adapter', $adapter);
        
        $adapter = $this->object->getComponentAdapterByType('SplObjectStorage');
        $this->assertNull($adapter);
        
        $this->object->addComponentInstance(new ArrayObject(array(123)), 'myArrayObj');
        
        $class = new ReflectionClass('TestObject');
        $member = $class->getMethod('methodInject');
        require_once 'Xyster/Container/NameBinding/Parameter.php';
        $nameBinding = new Xyster_Container_NameBinding_Parameter($member, 0);
        $adapter = $this->object->getComponentAdapterByType('ArrayObject', $nameBinding);
        $this->assertType('Xyster_Container_Adapter', $adapter);
    }
    
    /**
     * Tests getting a component by type with more than one type
     *
     */
    public function testGetComponentAdapterByTypeAmbiguous()
    {
        $key = new Xyster_Type('SplObjectStorage');
        $this->object->addComponent($key, 'myObjStorage')
            ->addComponent($key, 'myObjStorage2');
        
        $this->setExpectedException('Xyster_Container_Exception');
        $this->object->getComponentAdapterByType($key);
    }
    
    /**
     * Tests the 'getParent' method
     */
    public function testGetParent()
    {
        $this->assertSame($this->object, $this->child->getParent()->getDelegate());
    }

    /**
     * Tests the 'makeChildContainer' method
     */
    public function testMakeChildContainer()
    {
        $child = $this->object->makeChildContainer();
        $this->assertType('Xyster_Container', $child);
        $this->assertSame($this->object, $child->getParent()->getDelegate());
    }
    
    /**
     * Tests the 'removeChildContainer' method
     */
    public function testRemoveChildContainer()
    {
        $container = new Xyster_Container;
        $this->object->addChildContainer($container);
        require_once 'Xyster/Container/Empty.php';
        $return = $this->object->removeChildContainer(new Xyster_Container_Empty);
        $this->assertFalse($return);
        $return2 = $this->object->removeChildContainer($container);
        $this->assertTrue($return2);
    }
    
    /**
     * Test the 'removeComponent' method
     */
    public function testRemoveComponent()
    {
        $this->object->addComponent('SplObjectStorage');
        
        $this->assertEquals(1, count($this->object->getComponentAdapters()));
        $this->object->removeComponent(new Xyster_Type('SplObjectStorage'));
        $this->assertEquals(0, count($this->object->getComponentAdapters()));
    }

    /**
     * Tests the 'removeComponentByInstance' method
     */
    public function testRemoveComponentByInstance()
    {
        $this->object->addComponentInstance(new ArrayObject(array()));
        $this->object->addComponent('SplObjectStorage');
        
        $this->assertEquals(2, count($this->object->getComponentAdapters()));
        $this->object->removeComponentByInstance(new SplObjectStorage);
        $this->assertEquals(1, count($this->object->getComponentAdapters()));
        
        $this->object->removeComponentByInstance(new ReflectionClass('SplObjectStorage'));
        $this->assertEquals(1, count($this->object->getComponentAdapters()));
    }

    /**
     * Tests the 'with' method
     */
    public function testWith()
    {
        $feature = Xyster_Container_Features::CACHE();
        $this->object->with($feature);
        $this->assertAttributeSame($feature, '_with', $this->object);
    }
    
    /**
     * Tests checking for leftover properties
     *
     */
    public function testLeftoverProperties()
    {
        require_once 'Xyster/Container/Behavior/Factory/PropertyApplicator.php';
        $factory = new Xyster_Container_Behavior_Factory_PropertyApplicator;
        $this->object = new Xyster_Container($factory);
        
        $this->setExpectedException('Xyster_Container_Exception');
        $this->object->with(Xyster_Container_Features::CACHE())
            ->addComponent('SplObjectStorage');
    }
}

class TestObject
{
	public function methodInject( ArrayObject $myArrayObj )
	{
		// do nothing
	}
}

// Call Xyster_ContainerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_ContainerTest::main') {
    Xyster_ContainerTest::main();
}
