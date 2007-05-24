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
 * @package   UnitTests
 * @subpackage Xyster_Collection
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * PHPUnit test case
 */
require_once 'Xyster/Collection/CollectionTest.php';
/**
 * Xyster_Collection
 */
require_once 'Xyster/Collection/Set.php';
/**
 * Test for Xyster_Collection
 *
 */
class Xyster_Collection_SetTest extends Xyster_Collection_CollectionTest
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