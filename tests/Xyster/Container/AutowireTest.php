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
use Xyster\Container\Autowire;
class AutowireTest extends \PHPUnit_Framework_TestCase
{
    public function testNone()
    {
        $enum = Autowire::None();
        $this->_runTests($enum, 'None', 0);
    }
    public function testByName()
    {
        $enum = Autowire::ByName();
        $this->_runTests($enum, 'ByName', 1);
    }
    public function testByType()
    {
        $enum = Autowire::ByType();
        $this->_runTests($enum, 'ByType', 2);
    }
    public function testConstructor()
    {
        $enum = Autowire::Constructor();
        $this->_runTests($enum, 'Constructor', 3);
    }

    protected function _runTests( $actual, $name, $value )
    {
        self::assertEquals($name,$actual->getName());
        self::assertEquals($value,$actual->getValue());
        self::assertEquals('Xyster\Container\Autowire ['.$value.','.$name.']',(string)$actual);
        self::assertEquals($actual,\Xyster\Enum\Enum::parse('\Xyster\Container\Autowire',$name));
        self::assertEquals($actual,\Xyster\Enum\Enum::valueOf('\Xyster\Container\Autowire',$value));
    }
}