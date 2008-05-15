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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_Orm_BinderTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_BinderTest::main');
}

/**
 * PHPUnit test case
 */
require_once dirname(__FILE__).'/TestSetup.php';
require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Orm/Binder.php';

/**
 * Test class for Xyster_Orm_Binder.
 * Generated by PHPUnit on 2008-05-12 at 10:35:13.
 */
class Xyster_Orm_BinderTest extends Xyster_Orm_TestSetup
{
    /**
     * @var    Xyster_Orm_Binder
     */
    protected $object;
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Orm_BinderTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    public function setUp()
    {
        $this->object = new Xyster_Orm_Binder($this->_mockFactory()->getManager(),
            $this->_getMockEntity());
    }

    /**
     * Tests the 'bind' method
     */
    public function testBind()
    {
        $this->object->setAllowedFields(array('bugDescription', 'bugStatus', 'updatedOn', 'assignedTo'));
        $values = array('bugDescription' => 'Water is wet 2',
            'bugStatus'      => 'CLOSED',
            'updatedOn'      => '2008-05-12',
            'assignedTo'     => 'doublecompile');
        $this->object->bind($values);
        foreach( $values as $name => $value ) {
            $this->assertEquals($value, $this->object->getTarget()->$name);
        }
    }

    /**
     * Tests the 'isAllowed' method
     */
    public function testIsAllowed()
    {
        $this->object->setAllowedFields(array('bugDescription', 'bugId', 'reportedBy'));
        $this->assertFalse($this->object->isAllowed('bugId'));
        $this->assertTrue($this->object->isAllowed('bugDescription'));
        $this->assertFalse($this->object->isAllowed('verifiedBy'));
        
        $this->object = new Xyster_Orm_Binder($this->_mockFactory()->getManager(),
            $this->_getMockEntity(), true);
        $this->object->setAllowedFields(array('bugDescription', 'bugId', 'reportedBy'));
        $this->assertTrue($this->object->isAllowed('bugId'));
    }
}

// Call Xyster_Orm_BinderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_BinderTest::main') {
    Xyster_Orm_BinderTest::main();
}