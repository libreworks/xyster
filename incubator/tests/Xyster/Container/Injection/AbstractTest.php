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

// Call Xyster_Container_Injection_AbstractTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Container_Injection_AbstractTest::main');
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Container/Injection/Abstract.php';
require_once 'Xyster/Type.php';

/**
 * Test class for Xyster_Container_Injection_Abstract.
 */
class Xyster_Container_Injection_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Xyster_Container_Injection_Abstract
     */
    protected $object;
    
    protected $container;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Container_Injection_AbstractTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
    }
    
    /**
     * Tests an exception for a non-concrete class
     *
     */
    public function testConcrete()
    {
        $key = new Xyster_Type('TestClass');
        $this->setExpectedException('Xyster_Container_Exception');
        $inj = new Xyster_Container_Injection_AbstractImpl($key, $key);
    }
    
    /**
     * Tests an exception for an invalid parameter type
     *
     */
    public function testBadParam()
    {
        $key = new Xyster_Type('SplObjectStorage');
        $this->setExpectedException('Xyster_Container_Exception');
        require_once 'Xyster/Container/Parameter/Basic.php';
        $inj = new Xyster_Container_Injection_AbstractImpl($key, $key,
            array(Xyster_Container_Parameter_Basic::standard(), 123));
    }
}

abstract class TestClass
{
}

class Xyster_Container_Injection_AbstractImpl extends Xyster_Container_Injection_Abstract 
{
    public function getInstance( Xyster_Container_Interface $container )
    {
    }
    public function verify( Xyster_Container_Interface $container )
    {
    }
}

// Call Xyster_Container_Injection_AbstractTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Container_Injection_AbstractTest::main') {
    Xyster_Container_Injection_AbstractTest::main();
}
