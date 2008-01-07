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

// Call Xyster_Container_Behavior_AutomatedTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Container_Behavior_AutomatedTest::main');
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'CommonTest.php';

require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Container/Behavior/Automated.php';

/**
 * Test class for Xyster_Container_Behavior_Automated.
 * Generated by PHPUnit on 2008-01-06 at 13:09:21.
 */
class Xyster_Container_Behavior_AutomatedTest extends Xyster_Container_Behavior_CommonTest
{
    /**
     * @var Xyster_Container_Behavior_Automated
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Container_Behavior_AutomatedTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Xyster_Container_Behavior_Automated($this->delegate);
    }

    /**
     * Tests the 'toString' method
     */
    public function test__toString()
    {
        $this->assertSame('Automated:' . $this->delegate->__toString(), $this->object->__toString());
    }
}

// Call Xyster_Container_Behavior_AutomatedTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Container_Behavior_AutomatedTest::main') {
    Xyster_Container_Behavior_AutomatedTest::main();
}
