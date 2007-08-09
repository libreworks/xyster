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
     * Tests the 'addAll' and 'getAll' methods
     *
     */
    public function testAddAll()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        $bugs = new MockBugSet();
        
        $entities = $this->_getMockEntities();
        
        $bugs->merge($entities);
        $repo->addAll($entities);
        
        foreach( $bugs as $bug ) {
            $this->assertTrue($repo->contains($bug));
        }
        $this->assertTrue($repo->getAll('MockBug')->containsAll($bugs));
    }
    
    /**
     * Tests the 'contains' method
     *
     */
    public function testContains()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $entity = $this->_getMockEntity();
        $repo->add($entity);
        
        $account = new MockAccount();
        $account->accountName = 'gandalf';

        $this->assertTrue($repo->contains($entity));
        $this->assertFalse($repo->contains($account));
    }
    
    /**
     * Tests the 'find' method
     *
     */
    public function testFind()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $prod = new MockProduct();
        $prod->productId = 199;
        $prod->productName = 'Widget';
        
        $repo->add($prod);
        
        $this->assertTrue($repo->contains($prod));
        $this->assertSame($prod, $repo->find('MockProduct', array('productName'=>'Widget')));
    }
    
    /**
     * Tests the 'get' method
     *
     */
    public function testGet()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $entity = $this->_getMockEntity();
        $repo->add($entity);
        
        $this->assertSame($entity, $repo->get('MockBug', $entity->getPrimaryKey()));
        $this->assertNull($repo->get('MockBug', array('bugId'=>99)));
    }
    
    /**
     * Tests getting the classes in the repository
     *
     */
    public function testGetClasses()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $this->assertEquals(0, count($repo->getClasses()));
        
        $entity = $this->_getMockEntity();
        $repo->add($entity);
        
        $account = new MockAccount();
        $account->accountName = 'gandalf';
        $repo->add($account);
        
        $repo->setHasAll('MockProduct', false);
        
        $classes = $repo->getClasses();
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
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $this->assertFalse($repo->hasAll('MockBug'));
        $repo->setHasAll('MockBug');
        $this->assertTrue($repo->hasAll('MockBug'));
        $repo->setHasAll('MockBug', false);
        $this->assertFalse($repo->hasAll('MockBug'));
    }
    
    /**
     * Tests the 'remove' method
     *
     */
    public function testRemove()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $entity = $this->_getMockEntity();
        $repo->add($entity);
        
        $this->assertSame($entity, $repo->get('MockBug', $entity->getPrimaryKey()));
        
        $repo->setHasAll('MockBug');
        $repo->remove($entity);

        $this->assertFalse($repo->contains($entity));
        $this->assertFalse($repo->hasAll('MockBug'));
        $this->assertNull($repo->get('MockBug', $entity->getPrimaryKey()));
    }
    
    /**
     * Tests the 'remove' method with an entity that can be retrieved by index
     *
     */
    public function testRemoveWithIndex()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $entity = new MockProduct();
        $entity->productId = 12345;
        $entity->productName = 'Widget';
        $repo->add($entity);
        
        $this->assertSame($entity, $repo->find('MockProduct', array('productName'=>'Widget')));
        $repo->remove($entity);
        
        $this->assertFalse($repo->contains($entity));
        $this->assertNull($repo->find('MockProduct', array('productName'=>'Widget')));
    }
    
    /**
     * Tests the 'removeAll' method
     *
     */
    public function testRemoveAll()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        $bugs = new MockBugSet();
        
        $entities = $this->_getMockEntities();
        $bugs->merge($entities);
        $repo->addAll($entities);
        
        $repo->setHasAll('MockBug');
        $repo->removeAll($bugs);
        
        $this->assertFalse($repo->getAll('MockBug')->containsAny($bugs));
        $this->assertFalse($repo->hasAll('MockBug'));
    }
    
    /**
     * Tests the 'removeByKey' method
     *
     */
    public function testRemoveByKey()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        
        $entity = $this->_getMockEntity();
        $repo->add($entity);
        
        $this->assertSame($entity, $repo->get('MockBug', $entity->getPrimaryKey()));
        
        $repo->setHasAll('MockBug');
        $repo->removeByKey('MockBug', $entity->getPrimaryKey());
        
        $this->assertFalse($repo->contains($entity));
        $this->assertFalse($repo->hasAll('MockBug'));
        $this->assertNull($repo->get('MockBug', $entity->getPrimaryKey()));
    }
    
    /**
     * Tests the 'removeAllByKey' method
     *
     */
    public function testRemoveAllByKey()
    {
        $repo = new Xyster_Orm_Repository(self::$_mockFactory);
        $bugs = new MockBugSet();
        
        $entities = $this->_getMockEntities();
        $bugs->merge($entities);
        $repo->addAll($entities);
        
        $repo->setHasAll('MockBug');
        $repo->removeAllByKey('MockBug', $bugs->getPrimaryKeys());
        
        $this->assertFalse($repo->getAll('MockBug')->containsAny($bugs));
        $this->assertFalse($repo->hasAll('MockBug'));
    }
}