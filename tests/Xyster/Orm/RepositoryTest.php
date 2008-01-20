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
 * PHPUnit test case
 */
require_once 'Xyster/Orm/TestSetup.php';
/**
 * @see Xyster_Orm_Repository
 */
require_once 'Xyster/Orm/Repository.php';
/**
 * Test for Xyster_Orm_Repository
 *
 */
class Xyster_Orm_RepositoryTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Repository
     */
    protected $_repo;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->_repo = new Xyster_Orm_Repository($this->_mockFactory());
    }
    
    /**
     * Tests the 'addAll' and 'getAll' methods
     *
     */
    public function testAddAll()
    {
        $bugs = new MockBugSet();
        
        $entities = $this->_getMockEntities();
        
        $bugs->merge($entities);
        $this->_repo->addAll($entities);
        
        foreach( $bugs as $bug ) {
            $this->assertTrue($this->_repo->contains($bug));
        }
        $this->assertTrue($this->_repo->getAll('MockBug')->containsAll($bugs));
    }
    
    /**
     * Tests the 'contains' method
     *
     */
    public function testContains()
    {
        $entity = $this->_getMockEntity();
        $this->_repo->add($entity);
        
        $account = new MockAccount();
        $account->accountName = 'gandalf';

        $this->assertTrue($this->_repo->contains($entity));
        $this->assertFalse($this->_repo->contains($account));
    }
    
    /**
     * Tests the 'find' method
     *
     */
    public function testFind()
    {
        $prod = new MockProduct();
        $prod->productId = 199;
        $prod->productName = 'Widget';
        
        $this->_repo->add($prod);
        
        $this->assertTrue($this->_repo->contains($prod));
        $this->assertSame($prod, $this->_repo->find('MockProduct', array('productName'=>'Widget')));
    }
    
    /**
     * Tests the 'get' method
     *
     */
    public function testGet()
    {
        $entity = $this->_getMockEntity();
        $this->_repo->add($entity);
        
        $this->assertSame($entity, $this->_repo->get('MockBug', $entity->getPrimaryKey()));
        $this->assertNull($this->_repo->get('MockBug', array('bugId'=>99)));
    }
    
    /**
     * Tests getting the classes in the repository
     *
     */
    public function testGetClasses()
    {
        $this->assertEquals(0, count($this->_repo->getClasses()));
        
        $entity = $this->_getMockEntity();
        $this->_repo->add($entity);
        
        $account = new MockAccount();
        $account->accountName = 'gandalf';
        $this->_repo->add($account);
        
        $this->_repo->setHasAll('MockProduct', false);
        
        $classes = $this->_repo->getClasses();
        $this->assertContains('MockBug', $classes);
        $this->assertContains('MockAccount', $classes);
        $this->assertContains('MockProduct', $classes);
    }
    /**
     * Tests getting and setting the 'hasAll' value
     *
     */
    public function testHasAll()
    {
        $this->assertFalse($this->_repo->hasAll('MockBug'));
        $this->_repo->setHasAll('MockBug');
        $this->assertTrue($this->_repo->hasAll('MockBug'));
        $this->_repo->setHasAll('MockBug', false);
        $this->assertFalse($this->_repo->hasAll('MockBug'));
    }
    
    /**
     * Tests the 'remove' method
     *
     */
    public function testRemove()
    {
        $entity = $this->_getMockEntity();
        $this->_repo->add($entity);
        
        $this->assertSame($entity, $this->_repo->get('MockBug', $entity->getPrimaryKey()));
        
        $this->_repo->setHasAll('MockBug');
        $this->_repo->remove($entity);

        $this->assertFalse($this->_repo->contains($entity));
        $this->assertFalse($this->_repo->hasAll('MockBug'));
        $this->assertNull($this->_repo->get('MockBug', $entity->getPrimaryKey()));
    }
    
    /**
     * Tests the 'remove' method with an entity that can be retrieved by index
     *
     */
    public function testRemoveWithIndex()
    {
        $entity = new MockProduct();
        $entity->productId = 12345;
        $entity->productName = 'Widget';
        $this->_repo->add($entity);
        
        $this->assertSame($entity, $this->_repo->find('MockProduct', array('productName'=>'Widget')));
        $this->_repo->remove($entity);
        
        $this->assertFalse($this->_repo->contains($entity));
        $this->assertNull($this->_repo->find('MockProduct', array('productName'=>'Widget')));
    }
    
    /**
     * Tests the 'removeAll' method
     *
     */
    public function testRemoveAll()
    {
        $bugs = new MockBugSet();
        
        $entities = $this->_getMockEntities();
        $bugs->merge($entities);
        $this->_repo->addAll($entities);
        
        $this->_repo->setHasAll('MockBug');
        $this->_repo->removeAll($bugs);
        
        $this->assertFalse($this->_repo->getAll('MockBug')->containsAny($bugs));
        $this->assertFalse($this->_repo->hasAll('MockBug'));
    }
    
    /**
     * Tests the 'removeByKey' method
     *
     */
    public function testRemoveByKey()
    {
        $entity = $this->_getMockEntity();
        $this->_repo->add($entity);
        
        $this->assertSame($entity, $this->_repo->get('MockBug', $entity->getPrimaryKey()));
        
        $this->_repo->setHasAll('MockBug');
        $this->_repo->removeByKey('MockBug', $entity->getPrimaryKey());
        
        $this->assertFalse($this->_repo->contains($entity));
        $this->assertFalse($this->_repo->hasAll('MockBug'));
        $this->assertNull($this->_repo->get('MockBug', $entity->getPrimaryKey()));
    }
    
    /**
     * Tests the 'removeAllByKey' method
     *
     */
    public function testRemoveAllByKey()
    {
        $bugs = new MockBugSet();
        
        $entities = $this->_getMockEntities();
        $bugs->merge($entities);
        $this->_repo->addAll($entities);
        
        $this->_repo->setHasAll('MockBug');
        $this->_repo->removeAllByKey('MockBug', $bugs->getPrimaryKeys());
        
        $this->assertFalse($this->_repo->getAll('MockBug')->containsAny($bugs));
        $this->assertFalse($this->_repo->hasAll('MockBug'));
    }
}