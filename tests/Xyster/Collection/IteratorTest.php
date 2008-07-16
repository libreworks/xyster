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
// Call Xyster_Collection_IteratorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Collection_IteratorTest::main');
}
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Collection.php';

/**
 * Test for Xyster_Collection_Iterator
 *
 */
class Xyster_Collection_IteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Collection_IteratorTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests using foreach
     *
     */
    public function testForeach()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        foreach( $c as $key=>$value ) {
            // nothing to do
        }
    }
    
    /**
     * Tests the 'count' method
     *
     */
    public function testCount()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        $this->assertEquals($c->count(), $c->getIterator()->count());
    }
    
    /**
     * Tests the 'seek' method
     *
     */
    public function testSeek()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $c = $this->_getNewCollectionWithRandomValues();
        $it = $c->getIterator();
        $it->seek(2); // should be valid
        $it->seek(20);
    }
    
    /**
     * @return Xyster_Collection
     */
    protected function _getNewCollectionWithRandomValues()
    {
        $c = new Xyster_Collection();
        for( $i=0; $i<rand(3, 10); $i++ ) {
            $val = new stdClass;
            $val->value = md5(rand(0, 100));
            $c->add($val);
        }
        return $c;
    }
}

// Call Xyster_Collection_IteratorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Collection_IteratorTest::main') {
    Xyster_Collection_IteratorTest::main();
}
