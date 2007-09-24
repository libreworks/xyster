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
require_once dirname(__FILE__).'/TestSetup.php';
/**
 * @see Xyster_Orm_Manager
 */
require_once 'Xyster/Orm/Manager.php';
/**
 * @see Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';
/**
 * Test for Xyster_Orm_Manager
 *
 */
class Xyster_Orm_ManagerTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Manager
     */
    protected $_manager;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_manager = $this->_mockFactory()->getManager();
        require_once 'Zend/Cache.php';
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array('automatic_serialization'=>true));
        $cache->setBackend(new Xyster_Orm_CacheMock());
        $this->_manager->setSecondaryCache($cache);
    }
    
    /**
     * Test clearing the manager works as expected
     *
     */
    public function testClear()
    {
        $this->testGetRepository();
                
        $this->_manager->clear();
        
        $this->assertAttributeEquals(null,'_repository',$this->_manager);
    }
    
    /**
     * Tests the 'executeQuery' method
     *
     */
    public function testExecuteQuery()
    {
        require_once 'Xyster/Orm/Query.php';
        $query = new Xyster_Orm_Query('MockBug', $this->_manager);
        
        $result = $this->_manager->executeQuery($query);
        $this->assertType('MockBugSet', $result);
    }
    
    /**
     * Tests the 'executeQuery' method
     *
     */
    public function testExecuteQueryReport()
    {
        require_once 'Xyster/Orm/Query/Report.php';
        $query = new Xyster_Orm_Query_Report('MockBug', $this->_manager);
        
        $result = $this->_manager->executeQuery($query);
        $this->assertType('Xyster_Data_Set', $result);
    }
    
    /**
     * Tests the 'find' method
     *
     */
    public function testFind()
    {
        $entity = $this->_manager->find('MockBug', array('bugId'=>2));
        
        $this->assertType('Xyster_Orm_Entity', $entity);
        // should be in the repository now
        $this->assertSame($entity, $this->_manager->get('MockBug', 2));
    }
    
    /**
     * Tests the 'find' method with caching
     *
     */
    public function testFindCached()
    {
        $entity = $this->_manager->get('MockProduct', array('productId'=>1));
        
        $entity2 = $this->_manager->find('MockProduct',array('productName'=>$entity->productName));
        $this->assertSame($entity, $entity2);
    }
    
    /**
     * Tests the 'findAll' method
     *
     */
    public function testFindAll()
    {
        $set = $this->_manager->findAll('MockBug', array('reportedBy'=>'doublecompile'));
        
        $this->assertType('Xyster_Orm_Set', $set);
        foreach( $set as $entity ) {
            $this->assertTrue($this->_manager->getRepository()->contains($entity));
        }
    }

    /**
     * Tests the 'findAll' method with a non-criteria type
     *
     */
    public function testFindAllBadCriteria()
    {
        try {
            $set = $this->_manager->findAll('MockBug', new MockBug());
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'get' method
     *
     */
    public function testGet()
    {
        $entity = $this->_manager->get('MockBug', 1);
        
        $this->assertType('Xyster_Orm_Entity', $entity);
        // should be in the repository now
        $this->assertSame($entity, $this->_manager->get('MockBug', 1));
    }
    
    /**
     * Tests the 'get' method for a primary key that doesn't exist
     *
     */
    public function testGetNotThere()
    {
        $entity = $this->_manager->get('MockBug', 99);
        
        $this->assertNull($entity);
    }
    
	/**
     * Tests the 'get' method with a null primary key
     *
     */
    public function testGetWithNull()
    {
        $entity = $this->_manager->get('MockBug', null);
        
        $this->assertNull($entity);
    }
    
    /**
     * Tests the 'get' method for an entity already in the secondary repo
     *
     */
    public function testGetAlreadyInSecondary()
    {
        $entity = $this->_getMockEntity();
        $this->_manager->getSecondaryCache()->save($entity, md5('Xyster_Orm/mock/MockBug/bugId=' . $entity->bugId));
        
        $entity2 = $this->_manager->get('MockBug', $entity->bugId);
        
        $this->assertType('MockBug', $entity2);
        $this->assertEquals($entity->bugId, $entity2->bugId);
    }
    
    /**
     * Tests the 'getAll' method
     *
     */
    public function testGetAll()
    {
        $all = $this->_manager->getAll('MockBug');
        
        $this->assertType('Xyster_Orm_Set', $all);
        foreach( $all as $entity ) {
            $this->assertTrue($this->_manager->getRepository()->contains($entity));
        }
        
        $some = $this->_manager->getAll('MockBug', array(1,2,3,4));
        $this->assertType('Xyster_Orm_Set', $some);
        foreach( $some as $entity ) {
            $this->assertSame($entity, $this->_manager->get('MockBug', $entity->getPrimaryKey()));
        }
        
        $allAgain = $this->_manager->getAll('MockBug');
        $this->assertTrue($all->containsAll($allAgain));
    }
    
    /**
     * Tests passing several keys to 'getAll' returns correctly
     *
     */
    public function testGetAllKeys()
    {
        $some = $this->_manager->getAll('MockBug', array(1,2,3,4));
        $this->assertType('Xyster_Orm_Set', $some);
        foreach( $some as $entity ) {
            $this->assertSame($entity, $this->_manager->get('MockBug', $entity->getPrimaryKey()));
        }
    }
    
    /**
     * Tests the 'getJoined' method
     *
     */
    public function testGetJoined()
    {
        $entity = $this->_manager->get('MockBug', 1);
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $relation = $meta->getRelation('products');
        $joined = $this->_manager->getJoined($entity, $relation);
        $set = $this->_mockFactory()->get($relation->getTo())->getSet();
        $this->assertType(get_class($set), $joined);
    }
    
    /**
     * Tests 'getMapperFactory'
     *
     */
    public function testGetMapperFactory()
    {
        $this->assertType('Xyster_Orm_Mapper_Factory_Interface', $this->_manager->getMapperFactory());
        
        $manager = new Xyster_Orm_Manager();
        $this->assertType('Xyster_Orm_Mapper_Factory', $manager->getMapperFactory());
    }
    
    /**
     * Tests the 'getRepository' method
     *
     */
    public function testGetRepository()
    {
        $this->assertType('Xyster_Orm_Repository', $this->_manager->getRepository());
    }
    
    /**
     * Tests the 'refresh' method
     *
     */
    public function testRefresh()
    {
        $entity = $this->_manager->get('MockBug', 1);
        $baseValues = $entity->getBase();
        $entity->reportedBy = 'doublecompile';

        $this->_manager->refresh($entity);
        $this->assertEquals($baseValues, $entity->getBase());
    }
    
    /**
     * Tests 'setMapperFactory'
     *
     */
    public function testSetMapperFactory()
    {
        $mf = new Xyster_Orm_Mapper_FactoryMock();
        $this->_manager->setMapperFactory($mf);
        $this->assertSame($mf, $this->_manager->getMapperFactory());
    }
    
    /**
     * Tests setting a secondary cache
     *
     */
    public function testSetSecondaryCache()
    {
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array());
        $cache->setBackend(new Zend_Cache_Backend_Test());
        
        $this->_manager->setSecondaryCache($cache);
        
        $this->assertSame($cache, $this->_manager->getSecondaryCache());
    }

    /**
     * Tests setting a secondary cache with a registry key
     *
     */
    public function testSetSecondaryCacheRegistry()
    {
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array());
        $cache->setBackend(new Zend_Cache_Backend_Test());
        
        require_once 'Zend/Registry.php';
        Zend_Registry::set('OrmSecondaryCache', $cache);
        
        $this->_manager->setSecondaryCache('OrmSecondaryCache');
        
        $this->assertSame($cache, $this->_manager->getSecondaryCache());
    }
    
    /**
     * Tests 'setSecondaryCache' with a null value
     *
     */
    public function testSetSecondaryCacheNull()
    {
        $this->_manager->setSecondaryCache(null);
        $this->assertNull($this->_manager->getSecondaryCache());
    }

    /**
     * Tests 'setSecondaryCache' fails for wrong type
     *
     */
    public function testSetSecondaryCacheBadRegistryKey()
    {
        try {
            $this->_manager->setSecondaryCache('shouldntExist');
        } catch ( Zend_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests 'setSecondaryCache' fails for wrong type
     *
     */
    public function testSetSecondaryCacheBadType()
    {
        try {
            $this->_manager->setSecondaryCache(new MockBug());
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
}

/**
 * A mock cache
 *
 */
class Xyster_Orm_CacheMock implements Zend_Cache_Backend_Interface
{
    /**
     * @var Xyster_Collection_Map_String
     */
    protected $_cache;
    
    protected $_returnOnSave = true;
    
    /**
     * Constructor
     *
     * @param boolean $returnOnSave
     */
    public function __construct( $returnOnSave = true )
    {
        $this->_returnOnSave = $returnOnSave;
        
        require_once 'Xyster/Collection/Map/String.php';
        $this->_cache = new Xyster_Collection_Map_String();
    }
    
    /**
     * Set the frontend directives
     * 
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives)
    {
    }
       
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     * 
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $value = $this->_cache->get($id);
        return ( is_object($value) ) ? clone $value : $value;
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        return $this->_cache->containsKey($id);
    }
    
    /**
     * Save some string datas into a cache record
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @param int $specificLifetime if != false, set a specific lifetime for this cache record (null => infinite lifetime)    
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $this->_cache->set($id, is_object($data) ? clone $data : $data);
        return $this->_returnOnSave;
    }
    
    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        $this->_cache->remove($id);
        return true;
    }
    
    /**
     * Clean some cache records
     * 
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    { 
    }
    
    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return false;
    }
}