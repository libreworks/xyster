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
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Container/Autowire.php';

class Xyster_Container_AutowireTest extends PHPUnit_Framework_TestCase
{
    public function testNone()
    {
        $enum = Xyster_Container_Autowire::None();
        $this->_runTests($enum, 'None', 0);
    }
    public function testByName()
    {
        $enum = Xyster_Container_Autowire::ByName();
        $this->_runTests($enum, 'ByName', 1);
    }
    public function testByType()
    {
        $enum = Xyster_Container_Autowire::ByType();
        $this->_runTests($enum, 'ByType', 2);
    }
    public function testConstructor()
    {
        $enum = Xyster_Container_Autowire::Constructor();
        $this->_runTests($enum, 'Constructor', 3);
    }

    protected function _runTests( $actual, $name, $value )
    {
        self::assertEquals($name,$actual->getName());
        self::assertEquals($value,$actual->getValue());
        self::assertEquals('Xyster_Container_Autowire ['.$value.','.$name.']',(string)$actual);
        self::assertEquals($actual,Xyster_Enum::parse('Xyster_Container_Autowire',$name));
        self::assertEquals($actual,Xyster_Enum::valueOf('Xyster_Container_Autowire',$value));
    }
}