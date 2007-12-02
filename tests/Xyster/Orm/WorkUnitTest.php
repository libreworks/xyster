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
 * @see Xyster_Orm_WorkUnit
 */
require_once 'Xyster/Orm/WorkUnit.php';
/**
 * Test for Xyster_Orm_Entity
 *
 */
class Xyster_Orm_WorkUnitTest extends Xyster_Orm_TestSetup
{
    /**
     * Tests registering a removed entity
     *
     */
    public function testRemove()
    {
        $entity = $this->_getMockEntity();
        
        $wu = new Xyster_Orm_WorkUnitMock;
        $wu->registerDirty($entity);
        $wu->registerRemoved($entity);
        
        $this->assertFalse($wu->getDirty()->contains($entity));
        $this->assertFalse($wu->getNew()->contains($entity));
        $this->assertTrue($wu->getRemoved()->contains($entity));
        
        $wu->rollback();
        $wu->registerNew($entity);
        $wu->registerRemoved($entity);
        $this->assertFalse($wu->getDirty()->contains($entity));
        $this->assertFalse($wu->getNew()->contains($entity));
        $this->assertFalse($wu->getRemoved()->contains($entity));
    }
    
    /**
     * Tests registering a new entity
     *
     */
    public function testNew()
    {
        $entity = $this->_getMockEntity();
        
        $wu = new Xyster_Orm_WorkUnitMock;
        $wu->registerNew($entity);
        $this->assertTrue($wu->getNew()->contains($entity));
        $wu->rollback();
        $wu->registerDirty($entity);
        
        $this->setExpectedException('Xyster_Orm_Exception');
        $wu->registerNew($entity);
    }
    
    /**
     * Tests registering a dirty entity
     *
     */
    public function testDirty()
    {
        $entity = $this->_getMockEntity();
        
        $wu = new Xyster_Orm_WorkUnitMock;
        $wu->registerDirty($entity);
        
        $this->assertTrue($wu->getDirty()->contains($entity));
    }
    
    /**
     * Tests registering an entity with no PK as dirty
     *
     */
    public function testDirtyWithNoPk()
    {
        $wu = new Xyster_Orm_WorkUnitMock;
        
        $this->setExpectedException('Xyster_Orm_Exception');
        $wu->registerDirty($this->_getMockEntityWithNoPk());
    }
    
    /**
     * Tests registering a removed entity as dirty
     *
     */
    public function testDirtyWithRemoved()
    {
        $entity = $this->_getMockEntity();
        $wu = new Xyster_Orm_WorkUnitMock;
        $wu->registerRemoved($entity);
        
        $this->setExpectedException('Xyster_Orm_Exception');
        $wu->registerDirty($entity);
    }
    
    /**
     * Tests the rollback method
     *
     */
    public function testRollback()
    {
        $wu = new Xyster_Orm_WorkUnitMock();
        foreach( $this->_bugValues as $values ) {
            $entity = new MockBug($values);
            $wu->registerDirty($entity);
        }
        $wu->registerRemoved($entity);
        
        $wu->rollback();
        
        $this->assertTrue($wu->getDirty()->isEmpty());
        $this->assertTrue($wu->getRemoved()->isEmpty());
        $this->assertTrue($wu->getNew()->isEmpty());
    }
    
    /**
     * Tests committing the work
     *
     */
    public function testCommit()
    {
        $wu = new Xyster_Orm_WorkUnitMock();
        $entities = array();
        foreach( $this->_bugValues as $values ) {
            $entity = new MockBug($values);
            $entities[] = $entity;
            $wu->registerDirty($entity);
        }
        $removed = new MockBug();
        $removed->bugId = 99;
        $wu->registerRemoved($removed);
        $new = new MockBug();
        $wu->registerNew($new);

        $manager = new Xyster_Orm_Manager();
        $fact = $this->_mockFactory();
        $manager->setMapperFactory($fact);
        
        $wu->commit($manager);
        
        // verify what was saved, etc.
        $map = $fact->get('MockBug');
        /* @var $map Xyster_Orm_MapperMock */
        $this->assertTrue($map->wasSaved($new));
        foreach( $entities as $entity ) {
            $this->assertTrue($map->wasSaved($entity));
        }
        $this->assertTrue($map->wasDeleted($removed));
        $this->assertTrue($wu->getDirty()->isEmpty());
        $this->assertTrue($wu->getRemoved()->isEmpty());
        $this->assertTrue($wu->getNew()->isEmpty());
    }
}

/**
 * A mock work unit
 *
 */
class Xyster_Orm_WorkUnitMock extends Xyster_Orm_WorkUnit
{
    /**
     * Gets the dirty entities
     *
     * @return Xyster_Collection_Set
     */
    public function getDirty()
    {
        return $this->_dirty;
    }
    /**
     * Gets the removed entities
     *
     * @return Xyster_Collection_Set
     */
    public function getRemoved()
    {
        return $this->_removed;
    }
    /**
     * Gets the new entities
     *
     * @return Xyster_Collection_Set
     */
    public function getNew()
    {
        return $this->_new;
    }
}