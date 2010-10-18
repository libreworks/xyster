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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Collection;
use Xyster\Collection\SortableSet;
/**
 * Test for Xyster_Collection
 *
 */
class SortableSetTest extends SetTest
{
    protected $_className = '\Xyster\Collection\SortableSet';
    protected $_comparatorName = '\XysterTest\Collection\SortableSetTest_Comparator';
    
    /**
     * Tests using a comparator works as expected
     *
     */
    public function testSort()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        /* @var $c SortableSet */
        
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
class SortableSetTest_Comparator implements \Xyster\Collection\IComparator
{
    public function compare( $a, $b )
    {
        return strcmp($a, $b);
    }
}
