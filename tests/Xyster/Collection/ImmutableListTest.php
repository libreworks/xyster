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
require_once 'Xyster/Collection/ImmutableCollectionTest.php';
/**
 * Xyster_Collection_List
 */
require_once 'Xyster/Collection/List.php';
/**
 * Test class for immutable collection
 */
class Xyster_Collection_ImmutableListTest extends Xyster_Collection_ImmutableCollectionTest
{
    /**
     * @var Xyster_List
     */
    protected $_c;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        $this->_c = new Xyster_Collection_List($this->_getNewCollectionWithRandomValues(), true);
    }
    
    /**
     * Tests the 'insert' method
     *
     */
    public function testInsert()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->insert(0, null);
    }
    
    /**
     * Tests the 'insertAll' method
     *
     */
    public function testinsertAll()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->insertAll(0, $this->_getNewCollectionWithRandomValues());
    }
    
    /**
     * Tests the 'removeAt' method
     *
     */
    public function testRemoveAt()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->removeAt(0);
    }
    
    /**
     * Tests the 'set' method
     *
     */
    public function testSet()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->set(0, null);
    }
    
    /**
     * Tests the 'slice' method
     *
     */
    public function testSlice()
    {
        $this->setExpectedException('Xyster_Collection_Exception');
        $this->_c->slice(0, 1);
    }
}