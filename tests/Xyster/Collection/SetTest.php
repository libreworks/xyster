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
require_once 'Xyster/Collection/BaseCollectionTest.php';
/**
 * Xyster_Collection
 */
require_once 'Xyster/Collection/Set.php';
/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_SetTest extends Xyster_Collection_BaseCollectionTest
{
    protected $_className = 'Xyster_Collection_Set';

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