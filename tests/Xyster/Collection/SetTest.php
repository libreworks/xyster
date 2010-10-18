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
use Xyster\Collection\Set;
/**
 * Test for Xyster_Collection
 *
 */
class SetTest extends BaseCollectionTest
{
    protected $_className = '\Xyster\Collection\Set';
    
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
