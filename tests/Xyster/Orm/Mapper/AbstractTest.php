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

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
 
 
/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Xyster_Orm_Mapper_FactoryMock
 */
require_once 'Xyster/Orm/Mapper/FactoryMock.php';

/**
 * @see Xyster_Orm_Manager
 */
require_once 'Xyster/Orm/Manager.php';

/**
 * Test for Xyster_Orm_Mapper_Abstract
 *
 */
class Xyster_Orm_Mapper_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MockBugProductMapper
     */
    protected $_mapper;

    /**
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mockFactory;

    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        $this->_mapper = $this->_mockFactory()->get('MockBugProduct');
    }
    
    /**
     * Gets the mock mapper factory
     *
     * @return Xyster_Orm_Mapper_FactoryMock
     */
    protected function _mockFactory()
    {
        if ( !$this->_mockFactory ) {
            $this->_mockFactory = new Xyster_Orm_Mapper_FactoryMock();
            Xyster_Orm_Loader::addPath(dirname(dirname(__FILE__)).'/_files');
            $manager = new Xyster_Orm_Manager();
            $this->_mockFactory->setManager($manager);
            $manager->setMapperFactory($this->_mockFactory);
            Xyster_Orm_Loader::loadMapperClass('MockBugProduct');
            Xyster_Orm_Loader::loadSetClass('MockBugProduct');
            $this->_mockFactory()->getEntityType('MockBugProduct');
        }
        return $this->_mockFactory;
    }
    
    /**
     * Tests the delete method
     *
     */
    public function testDelete()
    {
        $entity = $this->_mapper->get(array('bugId'=>1,'productId'=>1,'versionId'=>2));
        
        $broker = $this->_mockFactory()->getManager()->getPluginBroker();
        require_once 'Xyster/Orm/Plugin/Abstract.php';
        $plugin = $this->getMock('Xyster_Orm_Plugin_Abstract', array('preDelete', 'postDelete'));
        $plugin->expects($this->once())
            ->method('preDelete')
            ->with($this->equalTo($entity));
        $plugin->expects($this->once())
            ->method('postDelete')
            ->with($this->equalTo($entity));
        $broker->registerPlugin($plugin);
        
        $this->_mapper->delete($entity);
        
    }
    
    /**
     * Tests the delete method will cascade its effects
     *
     */
    public function testDeleteCascade()
    {
        $map = $this->_mockFactory()->get('MockAccount');
        $bmap = $this->_mockFactory()->get('MockBug');
        
        $account = $map->get('doublecompile');
        $bug = $bmap->get(10);
        
        $map->delete($account);
        $this->assertTrue($map->wasDeleted($account));
        $this->assertTrue($bmap->wasDeleted($bug));
    }
    
    /**
     * Tests the 'find' method
     *
     */
    public function testFind()
    {
        $pk = array('bugId'=>1,'productId'=>1,'versionId'=>2);
        $entity = $this->_mapper->find($pk);
        $this->assertType('MockBugProduct', $entity);
        $this->assertEquals($pk, $entity->toArray());
    }
    
    /**
     * Tests the 'find' method with bad names
     *
     */
    public function testFindBadNames()
    {
        $this->setExpectedException('Xyster_Orm_Mapper_Exception');
        $entity = $this->_mapper->find(array('badId'=>1));
    }
    
    /**
     * Tests the 'findAll' method
     *
     */
    public function testFindAll()
    {
        $set = $this->_mapper->findAll(array('bugId'=>1));
        $this->assertType('MockBugProductSet', $set);
        
        require_once 'Xyster/Data/Expression.php';
        $set = $this->_mapper->findAll(Xyster_Data_Expression::eq('bugId',1));
        $this->assertType('MockBugProductSet', $set);
    }
    
    /**
     * Tests the 'findAll' method with a bad parameter
     *
     */
    public function testFindAllBadCriteria()
    {
        $this->setExpectedException('Xyster_Orm_Mapper_Exception');
        $this->_mapper->findAll(new MockBugProduct());
    }
    
    /**
     * Tests the 'get' method
     *
     */
    public function testGet()
    {
        $pk = array('bugId'=>1,'productId'=>1,'versionId'=>2);
        $entity = $this->_mapper->get($pk);
        $this->assertType('MockBugProduct', $entity);
        $this->assertEquals($pk, $entity->getPrimaryKey());
    }
    
    /**
     * Tests the 'get' method with the wrong number of keys
     *
     */
    public function testGetWrongKeyCount()
    {
        $pk = array('bugId'=>1,'productId'=>1);
        
        $this->setExpectedException('Xyster_Orm_Mapper_Exception');
        $entity = $this->_mapper->get($pk);
    }
    
    /**
     * Tests the 'get' method with the wrong key names
     *
     */
    public function testGetWrongKeyNames()
    {
        $pk = array('bugId'=>1,'productId'=>1, 'wrongId'=>2);

        $this->setExpectedException('Xyster_Orm_Mapper_Exception');
        $entity = $this->_mapper->get($pk);
    }
    
    /**
     * Tests the 'getDomain' method
     *
     */
    public function testGetDomain()
    {
        $domain = $this->_mapper->getDomain();
        
        $this->assertEquals('mock', $domain);
    }
    
    /**
     * Tests the 'getIndex' method
     *
     */
    public function testGetIndex()
    {
        $index = $this->_mapper->getIndex();
        
        $this->assertType('array', $index);
    }
    
    /**
     * Tests the 'getLifetime' method
     *
     */
    public function testGetLifetime()
    {
        $lifetime = $this->_mapper->getLifetime();
        
        $this->assertTrue(is_int($lifetime));
        $this->assertEquals(99, $lifetime);
    }
    
    /**
     * Tests the 'getOption' method
     *
     */
    public function testGetOption()
    {
        $this->assertEquals(1234, $this->_mapper->getOption('testing'));
        $this->assertNull($this->_mapper->getOption('nonExistant'));
    }
    
    /**
     * Tests the 'getOptions' method
     *
     */
    public function testGetOptions()
    {
        $options = $this->_mapper->getOptions();
        
        $this->assertType('array', $options);
    }
    
    /**
     * Tests the 'getTable' method
     *
     */
    public function testGetTable()
    {
        $table = $this->_mapper->getTable();
        
        $this->assertEquals('mock_bug_product', $table);
    }
    
    /**
     * Tests the 'save' method
     *
     */
    public function testSave()
    {
        $pk = array('bugId'=>1,'productId'=>1,'versionId'=>2);
        $entity = $this->_mapper->get($pk);
        $entity->versionId = 3;
        
        $broker = $this->_mockFactory()->getManager()->getPluginBroker();
        require_once 'Xyster/Orm/Plugin/Abstract.php';
        $plugin = $this->getMock('Xyster_Orm_Plugin_Abstract', array('preUpdate', 'postUpdate'));
        $plugin->expects($this->once())
            ->method('preUpdate');
        $plugin->expects($this->once())
            ->method('postUpdate');
        $broker->registerPlugin($plugin);
        
        $this->_mapper->save($entity);
        $this->assertTrue($this->_mapper->wasSaved($entity));
    }
    
    /**
     * Tests the 'save' method with a new entity
     *
     */
    public function testSaveInsert()
    {
        $entity = new MockBugProduct();
        $entity->bugId = 1;
        $entity->productId = 1;
        $entity->versionId = 4;
        
        $broker = $this->_mockFactory()->getManager()->getPluginBroker();
        require_once 'Xyster/Orm/Plugin/Abstract.php';
        $plugin = $this->getMock('Xyster_Orm_Plugin_Abstract', array('preInsert', 'postInsert'));
        $plugin->expects($this->once())
            ->method('preInsert');
        $plugin->expects($this->once())
            ->method('postInsert');
        $broker->registerPlugin($plugin);
        
        $this->_mapper->save($entity);
        $this->assertTrue($this->_mapper->wasSaved($entity));
    }
    
    /**
     * Tests the 'save' method for relations
     *
     */
    public function testSaveOneToMany()
    {
        $map = $this->_mockFactory()->get('MockAccount');
        $bmap = $this->_mockFactory()->get('MockBug');
        $entity = $map->get('mmouse');
        
        $newAccount = new MockAccount();
        $newAccount->accountName = 'doublecompile';
        
        Xyster_Orm_Loader::loadSetClass('MockBug');
        $set = new MockBugSet();
        $bug = new MockBug();
        $bug->bugDescription = 'Testing123';
        $bug->verifier = $newAccount;
        $set->add($bug);
        
        $reported = $entity->reported;
        $reported->merge($set);
        $reported->retainAll($set);

        $removed = $reported->getDiffRemoved();
        $map->save($entity);
        
        $this->assertTrue($bmap->wasSaved($bug));
        $this->assertTrue($map->wasSaved($newAccount));
        foreach( $removed as $removedEntity ) {
            $this->assertTrue($bmap->wasDeleted($removedEntity));
        }
    }
    
    /**
     * Tests the 'save' method when a primary key is updated
     *
     */
    public function testSaveUpdateKey()
    {
        $map = $this->_mockFactory()->get('MockAccount');
        $bmap = $this->_mockFactory()->get('MockBug');
        $entity = $map->get('mmouse');
        
        foreach( $entity->assigned as $bug ) {
            $this->assertEquals('mmouse', $bug->assignedTo);
        }
        
        $entity->accountName = 'mickey';
        $map->save($entity);
        
        foreach( $entity->assigned as $bug ) {
            $this->assertTrue($bmap->wasSaved($bug));
            $this->assertEquals('mickey', $bug->assignedTo);
        }
    }
    
    /**
     * Tests the 'translateField' method
     *
     */
    public function testTranslateField()
    {
        $this->assertEquals('bugId', $this->_mapper->translateField('bug_id'));
    }
    
    /**
     * Tests the 'untranslateField' method
     *
     */
    public function testUnranslateField()
    {
        $this->assertEquals('bug_id', $this->_mapper->untranslateField('bugId'));
    }
    
    /**
     * Tries using the wrong type with the mapper
     *
     */
    public function testAssertThisType()
    {
        $entity = new MockBug();
        $this->setExpectedException('Xyster_Orm_Mapper_Exception');
        $this->_mapper->save($entity);
    }
}