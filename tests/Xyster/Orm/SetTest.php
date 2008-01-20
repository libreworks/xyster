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
 * @see Xyster_Orm_Set
 */
require_once 'Xyster/Orm/Set.php';
/**
 * Test for Xyster_Orm_Set
 *
 */
class Xyster_Orm_SetTest extends Xyster_Orm_TestSetup
{
    /**
     * Tests that passing entities to the constructor will merge them
     *
     */
    public function testConstructWithValues()
    {
        $entities = $this->_getMockEntities();
        $set = $this->_getMockSet($entities);
   
        $this->assertTrue($set->containsAll($entities));
        $this->assertTrue($entities->containsAll($set));
    }
    
    /**
     * Tests adding an entity to a set works as expected
     *
     */
    public function testAdd()
    {
        $set = $this->_getMockSet();
        $entity = $this->_getMockEntity();
        $set->add($entity);
        
        $this->assertEquals(1,$set->count());
        $this->assertTrue($set->contains($entity));
    }
    
    /**
     * Tests that adding a non-entity will throw an exception
     *
     */
    public function testAddBadItem()
    {
        $set = $this->_getMockSet();

        $this->setExpectedException('Xyster_Data_Set_Exception');
        $set->add(1234); 
    }
    
    /**
     * Tests adding an entity will have its relation set
     *
     */
    public function testAddWithRelation()
    {
        $account = new MockAccount();
        
        $set = $account->reported;
        $entity = $this->_getMockEntity();
        $set->add($entity);
        
        $this->assertSame($account, $entity->reporter);
        $this->assertTrue($account->isDirty());
    }
    
    /**
     * Tests baselining a set
     *
     */
    public function testBaseline()
    {
        $set = $this->_getMockSet();
        
        $bugs = array();
        $entities = $this->_getMockEntities();
        foreach( $entities as $entity ) {
            $set->add($entity);
            $bugs[] = $entity;
        }
        $set->baseline();
        
        $this->assertEquals($bugs, $set->getBase());
    }
    
    /**
     * Tests that a related set will set the entity dirty when cleared
     *
     */
    public function testClearSetsEntityDirty()
    {
        $account = new MockAccount();
        
        $set = $account->reported;
        /* @var $set MockBugSet */
        $entity = $this->_getMockEntity();
        $set->add($entity);
        $set->baseline();
        $account->setDirty(false);
        $set->clear();
        
        $this->assertTrue($account->isDirty());
    }
    
    /**
     * Tests that items added to a set will be returned in the 'DiffAdded'
     *
     */
    public function testDiffAdded()
    {
        $set = $this->_getMockSet();
        
        $bugs = array();
        $entities = $this->_getMockEntities();
        foreach( $entities as $entity ) {
            $set->add($entity);
            $bugs[] = $entity;
        }
        
        $this->assertEquals($bugs, $set->getDiffAdded());
    }
    
    /**
     * Tests that items removed from a set will be returned in the 'DiffRemoved'
     *
     */
    public function testDiffRemoved()
    {
        $set = $this->_getMockSet();
        
        $bugs = array();
        $entity = null;
        $entities = $this->_getMockEntities();
        foreach( $entities as $entity ) {
            $set->add($entity);
            $bugs[] = $entity;
        }
        $set->baseline();
        $set->remove($entity);

        $this->assertEquals(array($entity), $set->getDiffRemoved());
    }
    
    /**
     * Tests the default get entity name method
     *
     */
    public function testGetEntityName()
    {
        $set = $this->_getMockSet();
        $this->assertEquals(substr(get_class($set), 0, -3), $set->getEntityName());
    }
    
    /**
     * Tests the relateTo method will assign the entity and relation
     *
     */
    public function testRelateTo()
    {
        $set = $this->_getMockSet();
        
        $mf = $this->_mockFactory();
        $map = $mf->get('MockAccount');
        $meta = $map->getEntityMeta();
        $relation = $meta->getRelation('reported');
        $entity = $this->_getMockEntity();

        $set->relateTo($relation, $entity);
        
        $this->assertSame($relation, $set->getRelation());
        $this->assertSame($entity, $set->getRelatedEntity());
        
        // try and relate already related
        $this->setExpectedException('Xyster_Orm_Exception');
        $set->relateTo($relation, new MockBug());
    }
    
    /**
     * Tests that removing an entity will set the related entity dirty
     *
     */
    public function testRemoveSetsEntityDirty()
    {
        $account = new MockAccount();
        
        $set = $account->reported;
        /* @var $set MockBugSet */
        $entity = $this->_getMockEntity();
        $set->add($entity);
        $set->baseline();
        $account->setDirty(false);
        $set->remove($entity);
        
        $this->assertTrue($account->isDirty());
        $this->assertFalse($set->contains($entity));
    }
    
    /**
     * Tests that removing multiple entities will set the related entity dirty
     *
     */
    public function testRemoveAllSetsEntityDirty()
    {
        $account = new MockAccount();
        
        $set = $account->reported;
        /* @var $set MockBugSet */
        $remove = $this->_getMockSet();

        foreach( $this->_getMockEntities() as $entity ) {
            $set->add($entity);
            $remove->add($entity);
        }
        $set->baseline();
        $account->setDirty(false);
        $set->removeAll($set);

        $this->assertTrue($account->isDirty());
        $this->assertFalse($set->containsAny($remove));
    }
    
    /**
     * Tests that retaining entities will set the related entity dirty
     *
     */
    public function testRetainAllSetsEntityDirty()
    {
        $account = new MockAccount();
        //$account->accountName = 'doublecompile';
        
        $set = $account->reported;
        /* @var $set MockBugSet */
        $remove = $this->_getMockSet();

        $entity = null;
        foreach( $this->_getMockEntities() as $entity ) {
            $set->add($entity);
        }
        
        $remove->add($entity);
        $set->baseline();
        $account->setDirty(false);
        $set->retainAll($remove);

        $this->assertTrue($account->isDirty());
        $this->assertTrue($set->containsAll($remove));
    }
    
    /**
     * Gets a new Mock Set
     *
     * @param Xyster_Orm_Set $set 
     * @return Xyster_Orm_Set
     */
    protected function _getMockSet( Xyster_Orm_Set $set = null )
    {
        return new MockBugSet($set);
    }
}