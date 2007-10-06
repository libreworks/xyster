<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Application
 * @subpackage   UnitTests
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_ApplicationTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Xyster/Application.php';
require_once 'Zend/Config.php';

/**
 * Test class for Xyster_ApplicationTest.
 * Generated by PHPUnit on 2007-10-01 at 19:00:04.
 */
class Xyster_ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application
     * @var Xyster_Application
     */
    public $application;
    
    /**
     * Runs the test methods of this class.
     * 
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_ApplicationTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        $this->application = Xyster_Application::getInstance();
    }
    
    /**
     * Tests getting a service
     *
     */
    public function testGetService()
    {
        $this->application->getServiceBroker()->addPrefix('Xyster_Application_Service');
        
        $svc = $this->application->getService('SecondExampleService');
        $this->assertType('Xyster_Application_Service_SecondExampleService', $svc);
    }
    
    /**
     * Tests that the service broker is of the right type
     *
     */
    public function testGetServiceBroker()
    {
        $this->assertType('Xyster_Application_Service_Broker', $this->application->getServiceBroker());
    }
    
    /**
     * Tests setting configuration with a zend_config object
     *
     */
    public function testSetConfigWithObject()
    {
        $array = array('three'=>3, 'four'=>4);
        $param = new Zend_Config($array);
        $return = $this->application->setConfig($param);
        
        $this->assertSame($this->application, $return);
        $this->assertSame($param, $this->application->getConfig());
        $this->assertEquals($array, $this->application->getConfig()->toArray());
    }

    /**
     * Tests setting configuration with an array
     *
     */
    public function testSetConfigWithArray()
    {
        $array = array('one'=>1, 'two'=>2);
        $param = $array;
        $return = $this->application->setConfig($param);
        
        $this->assertSame($this->application, $return);
        $this->assertType('Zend_Config', $this->application->getConfig());
        $this->assertEquals($array, $this->application->getConfig()->toArray());
    }
    
    /**
     * Tests setting an invalid config
     *
     */
    public function testSetConfigInvalid()
    {
        try {
            $this->application->setConfig('invalid argument');
            $this->fail('Exception not thrown');
        } catch ( Xyster_Application_Exception $thrown ) {
            // do nothing
        }
    }
}

// Call Xyster_ApplicationTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_ApplicationTest::main') {
    Xyster_ApplicationTest::main();
}