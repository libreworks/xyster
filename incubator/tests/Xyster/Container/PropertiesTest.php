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

// Call Xyster_Container_PropertiesTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Container_PropertiesTest::main');
}

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Container/Properties.php';

/**
 * Test class for Xyster_Container_Properties.
 * Generated by PHPUnit on 2008-01-31 at 17:56:56.
 */
class Xyster_Container_PropertiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container_Properties
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Container_PropertiesTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests the basic operation of the class
     *
     */
    public function testMain()
    {
    	$map = new Xyster_Collection_Map_String;
    	$map['SplObjectStorage'] = 'myObjStrg';
        $map['ArrayObject'] = 'myArrayObj';
        
        $this->object = new Xyster_Container_Properties($map);
        
        $adapter1 = $this->object->getComponentAdapter('myObjStrg');
        $this->assertType('Xyster_Container_Adapter', $adapter1);
        $this->assertEquals('SplObjectStorage', $adapter1->getImplementation()->getName());
        
        $adapter2 = $this->object->getComponentAdapter('myArrayObj');
        $this->assertType('Xyster_Container_Adapter', $adapter2);
        $this->assertEquals('ArrayObject', $adapter2->getImplementation()->getName());
    }
}

// Call Xyster_Container_PropertiesTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Container_PropertiesTest::main') {
    Xyster_Container_PropertiesTest::main();
}