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
 * @subpackage Xyster_Data
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
        
        try {
            Xyster_Orm_Loader::addPath(dirname(__FILE__) . '/_doesntExist');
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'loadClass' method
     *
     */
    public function testLoadClass()
    {
        try {
            Xyster_Orm_Loader::loadClass('MockProduct');
        } catch ( Exception $thrown ) {
            $this->fail('Loading a valid class should not error');
        }
        
        try {
            Xyster_Orm_Loader::loadClass('NoClassInside');
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests loading a 
     *
     */
    public function testLoadEntityClass()
    {
        try {
            Xyster_Orm_Loader::loadEntityClass('MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Loading a valid entity class should not error');
        }
    }
    
    /**
     * Tests that an entity class must be a subclass of Xyster_Orm_Entity
     *
     */
    public function testLoadEntityClassNotSub()
    {
        try {
            Xyster_Orm_Loader::loadEntityClass('MockBugSet');
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests loading a set class
     *
     */
    public function testLoadSetClass()
    {
        try {
            Xyster_Orm_Loader::loadSetClass('MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Loading a valid set class should not error');
        }
    }
    
    /**
     * Tests that a set class must be a subclass of Xyster_Orm_Set
     *
     */
    public function testLoadSetClassNotSub()
    {
        try {
            Xyster_Orm_Loader::loadSetClass('BadBug');
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests loading a mapper class
     *
     */
    public function testLoadMapperClass()
    {
        try {
            Xyster_Orm_Loader::loadMapperClass('MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Loading a valid mapper class should not error');
        }
    }
    
    /**
     * Tests that a mapper class must be an instance of Xyster_Orm_Mapper_Interface
     *
     */
    public function testLoadMapperClassNotSub()
    {
        try {
            Xyster_Orm_Loader::loadMapperClass('BadBug');
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
}