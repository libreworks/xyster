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
 * @see Xyster_Orm_Mapper_Factory
 */
require_once 'Xyster/Orm/Mapper/Factory.php';

/**
 * Test for Xyster_Orm_Entity
 *
 */
class Xyster_Orm_Mapper_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Orm_Mapper_Factory
     */
    protected $_factory;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        Xyster_Orm_Loader::addPath(dirname(dirname(__FILE__)) . '/_files');
        $this->_factory = new Xyster_Orm_Mapper_Factory();
    }
    
    /**
     * Tests getting a mapper
     *
     */
    public function testGet()
    {
        $mapper = $this->_factory->get('MockBugProduct');
        $this->assertType('Xyster_Orm_Mapper_Interface', $mapper);
    }
    
    /**
     * Tests getting an entity meta
     *
     */
    public function testGetEntityMeta()
    {
        $meta = $this->_factory->getEntityMeta('MockBugProduct');
        $this->assertType('Xyster_Orm_Entity_Meta', $meta);
    }

    /**
     * Tests setting a secondary cache
     *
     */
    public function testSetDefaultMetadataCache()
    {
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array());
        $cache->setBackend(new Zend_Cache_Backend_Test());
        
        $this->_factory->setDefaultMetadataCache($cache);
        
        $this->assertSame($cache, $this->_factory->getDefaultMetadataCache());
    }

    /**
     * Tests setting a secondary cache with a registry key
     *
     */
    public function testSetDefaultMetadataCacheRegistry()
    {
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array());
        $cache->setBackend(new Zend_Cache_Backend_Test());
        
        require_once 'Zend/Registry.php';
        Zend_Registry::set('OrmDefaultMetadataCache', $cache);
        
        $this->_factory->setDefaultMetadataCache('OrmDefaultMetadataCache');
        
        $this->assertSame($cache, $this->_factory->getDefaultMetadataCache());
    }
    
    /**
     * Tests 'setDefaultMetadataCache' with a null value
     *
     */
    public function testSetDefaultMetadataCacheNull()
    {
        $this->_factory->setDefaultMetadataCache(null);
        $this->assertNull($this->_factory->getDefaultMetadataCache());
    }

    /**
     * Tests 'setDefaultMetadataCache' fails for wrong type
     *
     */
    public function testSetDefaultMetadataCacheBadRegistryKey()
    {
        try {
            $this->_factory->setDefaultMetadataCache('shouldntExist');
        } catch ( Zend_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests 'setDefaultMetadataCache' fails for wrong type
     *
     */
    public function testSetDefaultMetadataCacheBadType()
    {
        try {
            $this->_factory->setDefaultMetadataCache(array());
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }    
}