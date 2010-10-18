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
use Xyster\Container\Injector\Standard;
use Xyster\Container\Definition;
use Xyster\Container\Container;
require_once dirname(dirname(__FILE__)) . '/_files/Sdi.php';

/**
 * Only for testing the setter injection
 */
class StandardSdiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    Standard
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $def = new Definition('\XysterTest\Container\RocketShip');
        $def->dependsOn('pilot', 'XysterTest\Container\RocketPilot')
            ->dependsOn('fuel', 'XysterTest\Container\RocketFuel');
        $this->object = new Standard($def);
    }

    public function testGet()
    {
        $container = new Container();
        $container->add(Container::definition('\XysterTest\Container\RocketPilot')
            ->dependsOn('suit', 'XysterTest\Container\SpaceSuit'))
            ->add(new Definition('XysterTest\Container\RocketFuel'))
            ->add(new Definition('XysterTest\Container\SpaceSuit'));
        $object = $this->object->get($container);
        self::assertType('\XysterTest\Container\RocketShip', $object);
        self::assertAttributeEquals(new \XysterTest\Container\RocketFuel(), '_fuel', $object);
        self::assertType('\XysterTest\Container\RocketPilot', $object->getPilot());
    }

    /**
     * @expectedException \Xyster\Container\Injector\Exception
     */
    public function testGetBad()
    {
        //$this->setExpectedException('Xyster_Container_Injector_Exception', 'Cannot inject property pilot into RocketShip: key not found in the container: RocketPilot');
        $this->object->get(new Container());
    }
    
    public function testGetName()
    {
        self::assertEquals('XysterTest\Container\RocketShip', $this->object->getName());
    }
    
    public function testGetType()
    {
        self::assertEquals(new \Xyster\Type\Type('\XysterTest\Container\RocketShip'), $this->object->getType());
    }

    public function testGetLabel()
    {
        self::assertEquals('Injector', $this->object->getLabel());
    }
    
    public function testToString()
    {
        self::assertEquals('Injector:XysterTest\Container\RocketShip', $this->object->__toString());
    }

    /**
     * @expectedException \Xyster\Container\Injector\Exception
     */
    public function testValidateError1()
    {
        $this->object->validate(new Container());
    }
    
    public function testValidate()
    {
        $def = new Definition('\XysterTest\Container\RocketPilot');
        $def->dependsOn('suit', 'XysterTest\Container\SpaceSuit');
        $container = new Container();
        $container->add($def)
            ->add(new Definition('\XysterTest\Container\RocketFuel'))
            ->add(new Definition('\XysterTest\Container\SpaceSuit'));
        $this->object->validate($container);
    }
}
