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
require_once 'Xyster/Container/Injector/Standard.php';
require_once 'Xyster/Container/Definition.php';
require_once 'Xyster/Container.php';
require_once 'Xyster/Container/_files/Sdi.php';

/**
 * Only for testing the setter injection
 */
class Xyster_Container_Injector_StandardSdiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container_Injector_Standard
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $def = new Xyster_Container_Definition('RocketShip');
        $def->dependsOn('pilot', 'RocketPilot')
            ->dependsOn('fuel', 'RocketFuel');
        $this->object = new Xyster_Container_Injector_Standard($def);
    }

    public function testGet()
    {
        $container = new Xyster_Container();
        $container->add(Xyster_Container::definition('RocketPilot')
            ->dependsOn('suit', 'SpaceSuit'))
            ->add(new Xyster_Container_Definition('RocketFuel'))
            ->add(new Xyster_Container_Definition('SpaceSuit'));
        $object = $this->object->get($container);
        self::assertType('RocketShip', $object);
        self::assertAttributeEquals(new RocketFuel(), '_fuel', $object);
        self::assertType('RocketPilot', $object->getPilot());
    }
    
    public function testGetBad()
    {
        $this->setExpectedException('Xyster_Container_Injector_Exception', 'Cannot inject property pilot into RocketShip: key not found in the container: RocketPilot');
        $this->object->get(new Xyster_Container());
    }
    
    public function testGetName()
    {
        self::assertEquals('RocketShip', $this->object->getName());
    }
    
    public function testGetType()
    {
        self::assertEquals(new Xyster_Type('RocketShip'), $this->object->getType());
    }

    public function testGetLabel()
    {
        self::assertEquals('Injector', $this->object->getLabel());
    }
    
    public function testToString()
    {
        self::assertEquals('Injector:RocketShip', $this->object->__toString());
    }
    
    public function testValidateError1()
    {
        $this->setExpectedException('Xyster_Container_Injector_Exception');
        $this->object->validate(new Xyster_Container());
    }
    
    public function testValidate()
    {
        $def = new Xyster_Container_Definition('RocketPilot');
        $def->dependsOn('suit', 'SpaceSuit');
        $container = new Xyster_Container();
        $container->add($def)
            ->add(new Xyster_Container_Definition('RocketFuel'))
            ->add(new Xyster_Container_Definition('SpaceSuit'));
        $this->object->validate($container);
    }
}
