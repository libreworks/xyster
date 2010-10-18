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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Container\Injector;
use Xyster\Container\Injector\Autowiring;
use Xyster\Container\Definition;
use Xyster\Container\Container;
use Xyster\Container\Autowire;
require_once dirname(dirname(__FILE__)) . '/_files/Sdi.php';
/**
 * Test for the setter injection methods
 */
class Xyster_Container_Injector_AutowiringSdiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container_Injector_Autowiring
     */
    protected $object;
    
    public function testGetByType()
    {
        $container = new Container();
        $container->add(Container::definition('\XysterTest\Container\RocketPilot')
            ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'))
            ->add(new Definition('\XysterTest\Container\RocketFuel'))
            ->add(new Definition('\XysterTest\Container\SpaceSuit'));
        
        $this->object = new Autowiring(Container::definition('\XysterTest\Container\RocketShip'), Autowire::ByType());
        $object = $this->object->get($container);
        self::assertType('\XysterTest\Container\RocketShip', $object);
        self::assertType('\XysterTest\Container\RocketFuel', $object->getFuel());
        self::assertType('\XysterTest\Container\RocketPilot', $object->getPilot());
    }

    /**
     * @expectedException \Xyster\Container\Injector\Exception
     */
    public function testGetByTypeNone()
    {
        $container = new Container();
        $this->object = new Autowiring(Container::definition('\XysterTest\Container\RocketShip'), Autowire::ByType());
        //$this->setExpectedException('Xyster_Container_Injector_Exception', 'Cannot inject property pilot into RocketShip: type not found in the container: Class Astronaut');
        $object = $this->object->get($container);
    }

    /**
     * @expectedException \Xyster\Container\Injector\Exception
     */
    public function testGetByTypeMulti()
    {
        $container = new Container();
        $container->add(Container::definition('\XysterTest\Container\RocketPilot', 'pilot1')
                ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'))
            ->add(new Definition('\XysterTest\Container\RocketFuel'))
            ->add(new Definition('\XysterTest\Container\SpaceSuit'))
            ->add(Container::definition('\XysterTest\Container\RocketPilot', 'pilot2')
                ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'));
        $this->object = new Autowiring(Container::definition('\XysterTest\Container\RocketShip'), Autowire::ByType());
        //$this->setExpectedException('Xyster_Container_Injector_Exception', 'Cannot inject property pilot into RocketShip: more than one value is available in the container');
        $object = $this->object->get($container);
    }
    
    public function testGetByTypeIgnore()
    {
        $container = new Container();
        $container->add(Container::definition('\XysterTest\Container\RocketPilot')
            ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'))
            ->add(new Definition('\XysterTest\Container\SpaceSuit'));
        
        $this->object = new Autowiring(Container::definition('\XysterTest\Container\RocketShip'), Autowire::ByType(), array('fuel'));
        $object = $this->object->get($container);
        self::assertType('\XysterTest\Container\RocketShip', $object);
        self::assertNull($object->getFuel());
        self::assertType('\XysterTest\Container\RocketPilot', $object->getPilot());
    }
    
    public function testGetByName()
    {
        $container = new Container();
        $container->add(Container::definition('\XysterTest\Container\RocketPilot', 'pilot')
            ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'))
            ->add(new Definition('\XysterTest\Container\RocketFuel', 'fuel'))
            ->add(new Definition('\XysterTest\Container\SpaceSuit'));
        
        $this->object = new Autowiring(Container::definition('\XysterTest\Container\RocketShip'), Autowire::ByName());
        $object = $this->object->get($container);
        self::assertType('\XysterTest\Container\RocketShip', $object);
        self::assertType('\XysterTest\Container\RocketFuel', $object->getFuel());
        self::assertType('\XysterTest\Container\RocketPilot', $object->getPilot());
    }
    
    public function testGetByNameIgnore()
    {
        $container = new Container();
        $container->add(Container::definition('\XysterTest\Container\RocketPilot', 'pilot')
            ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'))
            ->add(new Definition('\XysterTest\Container\RocketFuel', 'fuel'))
            ->add(new Definition('\XysterTest\Container\SpaceSuit'));
        
        $this->object = new Autowiring(Container::definition('\XysterTest\Container\RocketShip'), Autowire::ByName(), array('fuel'));
        $object = $this->object->get($container);
        self::assertType('\XysterTest\Container\RocketShip', $object);
        self::assertNull($object->getFuel());
        self::assertType('\XysterTest\Container\RocketPilot', $object->getPilot());
    }
}
