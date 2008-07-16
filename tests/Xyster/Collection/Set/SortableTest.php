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
// Call Xyster_Collection_Set_SortableTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Collection_Set_SortableTest::main');
}
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'SetTest.php';
require_once 'Xyster/Collection/Set/Sortable.php';
require_once 'Xyster/Collection/Comparator/Interface.php';

/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_Set_SortableTest extends Xyster_Collection_SetTest
{
    protected $_className = 'Xyster_Collection_Set_Sortable';
    protected $_comparatorName = 'Xyster_Collection_SortableSetTest_Comparator';
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Collection_Set_SortableTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Tests using a comparator works as expected
     *
     */
    public function testSort()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        /* @var $c Xyster_Collection_Set_Sortable */
        
        $expected = $c->toArray();
        
        $comparatorClass = $this->_comparatorName;
        $comparator = new $comparatorClass();
        
        usort($expected, array($comparator, 'compare'));
        
        $c->sort($comparator);
        
        $this->assertSame($expected, $c->toArray());
    }
}

/**
 * Implementation of the comparator interface
 * 
 */
class Xyster_Collection_SortableSetTest_Comparator implements Xyster_Collection_Comparator_Interface
{
    public function compare( $a, $b )
    {
        return strcmp($a, $b);
    }
}

// Call Xyster_Collection_Set_SortableTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Collection_Set_SortableTest::main') {
    Xyster_Collection_Set_SortableTest::main();
}
