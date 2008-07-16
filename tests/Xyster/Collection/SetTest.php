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
 * @subpackage Xyster_Collection
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_Collection_SetTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Collection_SetTest::main');
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BaseCollectionTest.php';
require_once 'Xyster/Collection/Set.php';

/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_SetTest extends Xyster_Collection_BaseCollectionTest
{
    protected $_className = 'Xyster_Collection_Set';

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Collection_SetTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests the 'add' method
     */
    public function testAdd()
    {
        $c = $this->_getNewCollection();
        $value = $this->_getNewValue();
        $pre = $c->count();
        $this->assertTrue( $c->add($value) );
        $post = $c->count();
        $this->assertTrue( $pre < $post );
        $this->assertFalse( $c->add($value) );
    }
}

// Call Xyster_Collection_SetTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Collection_SetTest::main') {
    Xyster_Collection_SetTest::main();
}