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
namespace XysterTest\Container\Provider;
use Xyster\Container\Provider\Caching;
use Xyster\Container\Container;
/**
 * Test class for Xyster_Container_Provider_Caching.
 * Generated by PHPUnit on 2009-06-14 at 19:14:07.
 */
class CachingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    Caching
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Caching(
            new \Xyster\Container\Injector\Standard(
                Container::definition('\Xyster\Collection\Collection', 'colly')));
    }

    public function testGet()
    {
        $object = $this->object->get(new Container());
        $object2 = $this->object->get(new Container());
        self::assertSame($object2, $object);
    }

    public function testGetLabel()
    {
        self::assertEquals('Caching', $this->object->getLabel());
    }
}
