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
namespace XysterTest\Container;
use Xyster\Container\Container;
/**
 * Test class for Xyster_Container.
 * Generated by PHPUnit on 2009-06-14 at 18:35:23.
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    \Xyster\Container\Container
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Container;
    }
    
    public function testAutowire()
    {
        self::assertSame($this->object, $this->object->autowire('\Xyster\Collection\Collection', 'colly'));
        self::assertAttributeEquals(
            array('colly'=>
                new \Xyster\Container\Injector\Autowiring(
                    new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'),
                    \Xyster\Container\Autowire::Constructor())),
            '_providers', $this->object);
    }

    public function testAutowireByName()
    {
        self::assertSame($this->object, $this->object->autowireByName('\Xyster\Collection\Collection', 'colly'));
        self::assertAttributeEquals(
            array('colly'=>
                new \Xyster\Container\Injector\Autowiring(
                    new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'),
                    \Xyster\Container\Autowire::ByName())),
            '_providers', $this->object);
    }

    public function testAutowireByType()
    {
        self::assertSame($this->object, $this->object->autowireByType('\Xyster\Collection\Collection', 'colly'));
        self::assertAttributeEquals(
            array('colly'=>
                new \Xyster\Container\Injector\Autowiring(
                    new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'),
                    \Xyster\Container\Autowire::ByType())),
            '_providers', $this->object);
    }

    public function testAddProvider()
    {
        $prov = new \Xyster\Container\Injector\Standard(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'));
        self::assertAttributeEquals(array(), '_providers', $this->object);
        self::assertSame($this->object, $this->object->addProvider($prov));
        self::assertAttributeEquals(array('colly'=>$prov), '_providers', $this->object);
    }
    
    public function testAddProviderAgain()
    {
        $def = new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly');
        $this->object->add($def);
        $this->setExpectedException('\Xyster\Container\Exception', 'A component with the name "colly" is already registered');
        $this->object->add($def);
    }

    public function testContains()
    {
        self::assertSame($this->object, $this->object->add(new \Xyster\Container\Definition('ArrayObject')));
        self::assertTrue($this->object->contains('ArrayObject'));
        self::assertFalse($this->object->contains('Foobar'));
    }

    public function testContainsAll()
    {
        $this->object->add(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'))
            ->add(new \Xyster\Container\Definition('\Xyster\Collection\StringMap', 'props'));
        self::assertTrue($this->object->containsAll(array('colly', 'props')));
        self::assertFalse($this->object->containsAll(array('colly', 'props', 'foobar')));
        self::assertFalse($this->object->containsAll(array('foo', 'bar', 'baz')));
    }

    public function testContainsAny()
    {
        $this->object->add(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'))
            ->add(new \Xyster\Container\Definition('\Xyster\Collection\StringMap', 'props'));
        self::assertTrue($this->object->containsAny(array('colly', 'props')));
        self::assertTrue($this->object->containsAny(array('colly', 'props', 'foobar')));
        self::assertFalse($this->object->containsAny(array('foo', 'bar', 'baz')));
    }

    public function testContainsType()
    {
        self::assertSame($this->object, $this->object->add(new \Xyster\Container\Definition('ArrayObject')));
        self::assertTrue($this->object->containsType('ArrayObject'));
    }

    public function testDefinition()
    {
        self::assertEquals(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'foobar'),
            \Xyster\Container\Container::definition('\Xyster\Collection\Collection', 'foobar'));
    }
    
    public function testGet()
    {
        self::assertNull($this->object->get('foobar'));
        $this->object->add(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'))
            ->add(new \Xyster\Container\Definition('\Xyster\Collection\StringMap', 'props'));
        self::assertType('\Xyster\Collection\StringMap', $this->object->get('props'));
    }

    public function testGetForType()
    {
        $type = new \Xyster\Type\Type('\Xyster\Collection\Collection');
        $this->object->add(new \Xyster\Container\Definition($type, 'colly'))
            ->add(new \Xyster\Container\Definition('\Xyster\Collection\StringMap', 'props'));
        self::assertEquals(array('colly'=>new \Xyster\Collection\Collection()), $this->object->getForType($type));
    }

    public function testGetNames()
    {
        $this->object->add(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'))
            ->add(new \Xyster\Container\Definition('\Xyster\Collection\StringMap', 'props'));
        self::assertEquals(array('colly', 'props'), $this->object->getNames());
        self::assertEquals(array('colly'), $this->object->getNames('\Xyster\Collection\ICollection'));
    }

    public function testGetParent()
    {
        $container = new \Xyster\Container\Container($this->object);
        self::assertSame($this->object, $container->getParent());
    }

    public function testGetType()
    {
        self::assertNull($this->object->getType('foobar'));
        $this->object->add(new \Xyster\Container\Definition('\Xyster\Collection\Collection', 'colly'))
            ->add(new \Xyster\Container\Definition('\Xyster\Collection\StringMap', 'props'));
        self::assertEquals(new \Xyster\Type\Type('\Xyster\Collection\Collection'), $this->object->getType('colly'));
    }
}
