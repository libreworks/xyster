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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Xyster_Orm_Loader
 */
require_once 'Xyster/Orm/Loader.php';

/**
 * Test for Xyster_Orm_Entity
 *
 */
class Xyster_Orm_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        Xyster_Orm_Loader::addPath(dirname(__FILE__) . '/_files');
    }
    
    /**
     * Tests the 'addPath' method
     *
     */
    public function testAddPath()
    {
        Xyster_Orm_Loader::addPath(dirname(__FILE__) . '/_files');
        
        $this->setExpectedException('Xyster_Orm_Exception');
        Xyster_Orm_Loader::addPath(dirname(__FILE__) . '/_doesntExist');
    }
    
    /**
     * Tests the 'loadClass' method
     *
     */
    public function testLoadClass()
    {
        // loading a valid class shouldn't error
        Xyster_Orm_Loader::loadClass('MockProduct');
        
        $this->setExpectedException('Xyster_Orm_Exception');
        Xyster_Orm_Loader::loadClass('NoClassInside');
    }
    
    /**
     * Tests loading a 
     *
     */
    public function testLoadEntityClass()
    {
        // loading a valid entity class shouldn't error
        Xyster_Orm_Loader::loadEntityClass('MockBug');
    }
    
    /**
     * Tests that an entity class must be a subclass of Xyster_Orm_Entity
     *
     */
    public function testLoadEntityClassNotSub()
    {
        $this->setExpectedException('Xyster_Orm_Exception');
        Xyster_Orm_Loader::loadEntityClass('MockBugSet');
    }
    
    /**
     * Tests loading a set class
     *
     */
    public function testLoadSetClass()
    {
        // loading a valid set class shouldn't error
        Xyster_Orm_Loader::loadSetClass('MockBug');
    }
    
    /**
     * Tests that a set class must be a subclass of Xyster_Orm_Set
     *
     */
    public function testLoadSetClassNotSub()
    {
        $this->setExpectedException('Xyster_Orm_Exception');
        Xyster_Orm_Loader::loadSetClass('BadBug');
    }
    
    /**
     * Tests loading a mapper class
     *
     */
    public function testLoadMapperClass()
    {
        Xyster_Orm_Loader::loadMapperClass('MockBug');
        // Loading a valid mapper class should not error
    }
    
    /**
     * Tests that a mapper class must be an instance of Xyster_Orm_Mapper_Interface
     *
     */
    public function testLoadMapperClassNotSub()
    {
        $this->setExpectedException('Xyster_Orm_Exception');
        Xyster_Orm_Loader::loadMapperClass('BadBug');
    }
}