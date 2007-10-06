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
// Call Xyster_Orm_ManagerTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_ManagerTest::main');
}

/**
 * PHPUnit test case
 */
require_once dirname(__FILE__).'/TestSetup.php';
/**
 * @see Xyster_Orm_Manager
 */
require_once 'Xyster/Orm/Manager.php';
/**
 * @see Xyster_Orm_CacheMock
 */
require_once 'Xyster/Orm/CacheMock.php';

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
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
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
     * Tests the 'getFromCache' method
     *
     */
    public function testGetFromCache()
    {
        $entity = $this->_manager->get('MockProduct', array('productId'=>1));
        
        $entity2 = $this->_manager->getFromCache('MockProduct', array('productId'=>1));
        $this->assertSame($entity, $entity2);
        
        $entity3 = $this->_manager->getFromCache('MockProduct', 99);
        $this->assertNull($entity3);
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

// Call Xyster_Orm_ManagerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_ManagerTest::main') {
    Xyster_Orm_ManagerTest::main();
}