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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_OrmTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_OrmTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * PHPUnit test case
 */
require_once 'Xyster/Orm/TestSetup.php';
/**
 * @see Xyster_Orm
 */
require_once 'Xyster/Orm.php';
/**
 * @see Xyster_Orm_CacheMock
 */
require_once 'Xyster/Orm/CacheMock.php';
/**
 * @see Xyster_Orm_Plugin_Abstract
 */
require_once 'Xyster/Orm/Plugin/Abstract.php';


/**
 * Test for Xyster_Orm
 *
 */
class Xyster_OrmTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm
     */
    protected $_orm;

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
     * Sets up the tests
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_orm = Xyster_Orm::getInstance();
        $this->_orm->setMapperFactory($this->_mockFactory());
        $this->_orm->setup('MockBug');
        require_once 'Zend/Cache.php';
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array('automatic_serialization'=>true));
        $cache->setBackend(new Xyster_Orm_CacheMock());
        $this->_orm->setSecondaryCache($cache);
    }
    
    /**
     * Tests setting up and tearing down an instance of the ORM frontend
     *
     */
    public function testGetInstanceAndClear()
    {
        $this->assertType('Xyster_Orm', $this->_orm);
        
        $this->_orm->clear();
        
        $this->assertNotSame($this->_orm, Xyster_Orm::getInstance());
    }
    
    /**
     * Tests the commit method
     *
     */
    public function testCommit()
    {
        $entity = $this->_orm->get('MockBug', 3);
        $entity2 = $this->_orm->get('MockBug', 2);
        
        // do a remove
        $this->_orm->remove($entity);
        
        // persist a new
        $newEntity = new MockBug();
        $this->_orm->persist($newEntity);

        // update existing
        $entity2->updatedOn = date('Y-m-d H:i:s');
        
        $this->_orm->commit();
        
        $map = $this->_mockFactory()->get('MockBug');
        $this->assertTrue($map->wasSaved($entity2));
        $this->assertTrue($map->wasSaved($newEntity));
        $this->assertFalse($newEntity->isDirty());
        $this->assertFalse($entity2->isDirty());
        
        $repoId = array('Xyster_Orm', $map->getDomain(), 'MockBug');
        foreach( $entity2->getPrimaryKey() as $key => $value ) {
            $repoId[] = $key . '=' . $value;
        }
        $repoId = md5(implode("/", $repoId));
        $entityFromSecondCache = $this->_orm->getSecondaryCache()->load($repoId);
        $this->assertType('array', $entityFromSecondCache);
        $this->assertEquals($entity2->toArray(), $entityFromSecondCache['values']);
        $this->assertTrue($map->wasDeleted($entity));
    }
    
    /**
     * Tests the find method
     *
     */
    public function testFind()
    {
        $criteria = array('bugId'=>1);
        $entity = $this->_orm->find('MockBug', $criteria);
        
        $this->assertType('MockBug', $entity);
        $this->assertEquals($criteria, $entity->getPrimaryKey());
    }
    
    /**
     * Tests the findAll method
     *
     */
    public function testFindAll()
    {
        $criteria = array('bugStatus'=>'OPEN');
        
        $set = $this->_orm->findAll('MockBug', $criteria);
        
        $this->assertType('MockBugSet', $set);
        foreach( $set as $entity ) {
            $this->assertEquals('OPEN', $entity->bugStatus);
        }
    }
    
    /**
     * Tests the get method
     *
     */
    public function testGet()
    {
        $criteria = array('bugId'=>1);
        $entity = $this->_orm->get('MockBug', $criteria['bugId']);
        
        $this->assertType('MockBug', $entity);
        $this->assertEquals($criteria, $entity->getPrimaryKey());
    }
    
    /**
     * Tests the 'getOrFail' method
     *
     */
    public function testGetOrFail()
    {
        $criteria = array('bugId'=>1);
        $entity = $this->_orm->getOrFail('MockBug', $criteria['bugId']);
        
        $this->assertType('MockBug', $entity);
        $this->assertEquals($criteria, $entity->getPrimaryKey());
        
        $criteria = array('bugId'=>100);
        
        $this->setExpectedException('Xyster_Orm_Exception');
        $this->_orm->getOrFail('MockBug', $criteria);
    }
    
    /**
     * Tests the getAll method
     *
     */
    public function testGetAll()
    {
        $criteria = range(1,5);
        $set = $this->_orm->getAll('MockBug', $criteria);
        
        $this->assertType('MockBugSet', $set);
    }
    
    /**
     * Tests getting and setting the mapper factory
     *
     */
    public function testGetAndSetMapperFactory()
    {
        $fact = $this->_orm->getMapperFactory();
        
        $this->assertType('Xyster_Orm_Mapper_Factory_Interface', $fact);
        
        $return = $this->_orm->setMapperFactory($fact);
        
        $this->assertSame($this->_orm, $return);
    }
    
    /**
     * Tests getting and setting the secondary cache
     *
     */
    public function testGetAndSetSecondaryCache()
    {
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache/Backend/Test.php';
        $cache = new Zend_Cache_Core(array());
        $cache->setBackend(new Zend_Cache_Backend_Test());
        
        $return = $this->_orm->setSecondaryCache($cache);
        
        $this->assertSame($this->_orm, $return);
        
        $this->assertSame($cache, $this->_orm->getSecondaryCache());
    }
    
    /**
     * Tests the 'getPlugin' method
     */
    public function testGetPlugin()
    {
        $plugin = $this->getMock('Xyster_Orm_Plugin_Abstract');
        $this->_orm->registerPlugin($plugin);
        $class = get_class($plugin);
        
        $return = $this->_orm->getPlugin(get_class($plugin));
        $this->assertSame($plugin, $return);
    }
    
    /**
     * Tests the 'getPlugins' method
     */
    public function testGetPlugins()
    {
        $plugin = $this->getMock('Xyster_Orm_Plugin_Abstract');
        $this->_orm->registerPlugin($plugin);
        $class = get_class($plugin);
        
        $return = $this->_orm->getPlugins();
        $this->assertType('array', $return);
        $this->assertContains($plugin, $return);
    }
    
    /**
     * Tests that persisting an existing entity errors
     *
     */
    public function testPersistExisting()
    {
        $entity = $this->_orm->get('MockBug', 1);
        $this->setExpectedException('Xyster_Orm_Exception');
        $this->_orm->persist($entity);
    }
    
    /**
     * Tests the 'refresh' method
     *
     */
    public function testRefresh()
    {
        $entity = $this->_orm->get('MockBug', 1);
        $this->assertType('MockBug', $entity);
        $baseValues = $entity->getBase();
        $entity->reportedBy = 'doublecompile';

        $this->_orm->refresh($entity);
        $this->assertEquals($baseValues, $entity->getBase());
    }
    
    /**
     * Tests the query method
     *
     */
    public function testQueryAndReportQuery()
    {
        $query = $this->_orm->query('MockBug', 'where bugDescription <> null');
        
        $this->assertType('Xyster_Orm_Query', $query);
        $this->assertEquals(1, count($query->getWhere()));
        
        $report = $this->_orm->reportQuery('MockBug', 'select reportedBy where bugDescription <> null');
        
        $this->assertType('Xyster_Orm_Query_Report', $report);
        $this->assertEquals(1, count($report->getFields()));
        $this->assertEquals(1, count($report->getWhere()));
    }
    
    /**
     * Tests the 'unregisterPlugin' method
     */
    public function testUnregisterPlugin()
    {
        $plugin = $this->getMock('Xyster_Orm_Plugin_Abstract');
        $this->_orm->registerPlugin($plugin);
        $class = get_class($plugin);
        
        $this->assertTrue($this->_orm->hasPlugin($class));
        $return = $this->_orm->unregisterPlugin($plugin);
        $this->assertSame($this->_orm, $return);
        $this->assertFalse($this->_orm->hasPlugin($class));
    }
}

// Call Xyster_OrmTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_OrmTest::main') {
    Xyster_OrmTest::main();
}
