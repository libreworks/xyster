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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Container/Injector/Autowiring.php';
require_once 'Xyster/Container.php';
require_once 'Xyster/Container/Autowire.php';
require_once 'Xyster/Container/_files/Sdi.php';

/**
 * Test for the setter injection methods
 */
class Xyster_Container_Injector_AutowiringSdiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container_Injector_Autowiring
     */
    protected $object;
    
    public function testGetByType()
    {
        $container = new Xyster_Container();
        $container->add(Xyster_Container::definition('RocketPilot')
            ->dependsOn('suit', 'SpaceSuit'))
            ->add(new Xyster_Container_Definition('RocketFuel'))
            ->add(new Xyster_Container_Definition('SpaceSuit'));
        
        $this->object = new Xyster_Container_Injector_Autowiring(Xyster_Container::definition('RocketShip'), Xyster_Container_Autowire::ByType());
        $object = $this->object->get($container);
        self::assertType('RocketShip', $object);
        self::assertType('RocketFuel', $object->getFuel());
        self::assertType('RocketPilot', $object->getPilot());
    }

    public function testGetByTypeNone()
    {
        $container = new Xyster_Container();
        $this->object = new Xyster_Container_Injector_Autowiring(Xyster_Container::definition('RocketShip'), Xyster_Container_Autowire::ByType());        
        $this->setExpectedException('Xyster_Container_Injector_Exception', 'Cannot inject property pilot into RocketShip: type not found in the container: Class Astronaut');
        $object = $this->object->get($container);
    }
    
    public function testGetByTypeMulti()
    {
        $container = new Xyster_Container();
        $container->add(Xyster_Container::definition('RocketPilot', 'pilot1')
                ->dependsOn('suit', 'SpaceSuit'))
            ->add(new Xyster_Container_Definition('RocketFuel'))
            ->add(new Xyster_Container_Definition('SpaceSuit'))
            ->add(Xyster_Container::definition('RocketPilot', 'pilot2')
                ->dependsOn('suit', 'SpaceSuit'));
        $this->object = new Xyster_Container_Injector_Autowiring(Xyster_Container::definition('RocketShip'), Xyster_Container_Autowire::ByType());        
        $this->setExpectedException('Xyster_Container_Injector_Exception', 'Cannot inject property pilot into RocketShip: more than one value is available in the container');
        $object = $this->object->get($container);
    }
    
    public function testGetByTypeIgnore()
    {
        $container = new Xyster_Container();
        $container->add(Xyster_Container::definition('RocketPilot')
            ->dependsOn('suit', 'SpaceSuit'))
            ->add(new Xyster_Container_Definition('SpaceSuit'));
        
        $this->object = new Xyster_Container_Injector_Autowiring(Xyster_Container::definition('RocketShip'), Xyster_Container_Autowire::ByType(), array('fuel'));
        $object = $this->object->get($container);
        self::assertType('RocketShip', $object);
        self::assertNull($object->getFuel());
        self::assertType('RocketPilot', $object->getPilot());
    }
    
    public function testGetByName()
    {
        $container = new Xyster_Container();
        $container->add(Xyster_Container::definition('RocketPilot', 'pilot')
            ->dependsOn('suit', 'SpaceSuit'))
            ->add(new Xyster_Container_Definition('RocketFuel', 'fuel'))
            ->add(new Xyster_Container_Definition('SpaceSuit'));
        
        $this->object = new Xyster_Container_Injector_Autowiring(Xyster_Container::definition('RocketShip'), Xyster_Container_Autowire::ByName());
        $object = $this->object->get($container);
        self::assertType('RocketShip', $object);
        self::assertType('RocketFuel', $object->getFuel());
        self::assertType('RocketPilot', $object->getPilot());
    }
    
    public function testGetByNameIgnore()
    {
        $container = new Xyster_Container();
        $container->add(Xyster_Container::definition('RocketPilot', 'pilot')
            ->dependsOn('suit', 'SpaceSuit'))
            ->add(new Xyster_Container_Definition('RocketFuel', 'fuel'))
            ->add(new Xyster_Container_Definition('SpaceSuit'));
        
        $this->object = new Xyster_Container_Injector_Autowiring(Xyster_Container::definition('RocketShip'), Xyster_Container_Autowire::ByName(), array('fuel'));
        $object = $this->object->get($container);
        self::assertType('RocketShip', $object);
        self::assertNull($object->getFuel());
        self::assertType('RocketPilot', $object->getPilot());
    }
}
