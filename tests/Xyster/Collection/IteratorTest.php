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
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Xyster_Collection
 */
require_once 'Xyster/Collection.php';
/**
 * Test for Xyster_Collection_Iterator
 *
 */
class Xyster_Collection_IteratorTest extends PHPUnit_Framework_TestCase
{
    public function testForeach()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        foreach( $c as $key=>$value ) {
            // nothing to do
        }
    }
    public function testCount()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        $this->assertEquals($c->count(),$c->getIterator()->count());
    }
    public function testSeek()
    {
        $c = $this->_getNewCollectionWithRandomValues();
        $it = $c->getIterator();
        $it->seek(2);
        try {
            $it->seek(20);
        } catch ( Exception $thrown ) {
            return;
        }
        $this->fail("Unthrown exception when seeking past max index");
    }
    /**
     * @return Xyster_Collection
     */
    protected function _getNewCollectionWithRandomValues()
    {
        $c = new Xyster_Collection();
        for( $i=0; $i<rand(3,10); $i++ ) {
            $c->add( new Xyster_Collection_Test_Value(md5(rand(0,100))) );
        }
        return $c;
    }
}