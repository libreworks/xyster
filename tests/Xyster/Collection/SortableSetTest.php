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

/**
 * PHPUnit test case
 */
require_once 'Xyster/Collection/SetTest.php';
/**
 * Xyster_Collection
 */
require_once 'Xyster/Collection/Set/Sortable.php';
/**
 * Xyster_Collection_Comparator_Interface
 */
require_once 'Xyster/Collection/Comparator/Interface.php';
/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_SortableSetTest extends Xyster_Collection_SetTest
{
    protected $_className = 'Xyster_Collection_Set_Sortable';
    protected $_comparatorName = 'Xyster_Collection_SortableSetTest_Comparator';
    
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