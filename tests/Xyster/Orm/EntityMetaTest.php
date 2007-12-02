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
require_once 'Xyster/Orm/TestSetup.php';
/**
 * @see Xyster_Orm_Entity_Field
 */
require_once 'Xyster/Orm/Entity/Meta.php';
/**
 * Test for Xyster_Orm_Entity_Field
 *
 */
class Xyster_Orm_Entity_MetaTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Entity_Meta
     */
    protected $_meta;
    
    /**
     * Sets up the tests
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $map = $this->_mockFactory()->get('MockBug');
        $this->_meta = $map->getEntityMeta();
    }
    
    /**
     * Tests the 'getEntityName' method
     *
     */
    public function testGetEntityName()
    {
        $this->_meta->getEntityName();
    }
    
    /**
     * Tests the 'getFields' method
     *
     */
    public function testGetFields()
    {
        $fields = $this->_meta->getFields();
        $this->assertType('array', $fields);
        foreach( $fields as $field ) {
            $this->assertType('Xyster_Orm_Entity_Field', $field);
        }
    }
    
    /**
     * Tests the 'getFieldNames' method
     *
     */
    public function testGetFieldNames()
    {
        $names = $this->_meta->getFieldNames();
        $this->assertEquals(array_keys($this->_meta->getFields()), $names);
    }
    
    /**
     * Tests the 'getMapperFactory' method
     *
     */
    public function testGetMapperFactory()
    {
        $this->assertSame($this->_mockFactory(), $this->_meta->getMapperFactory());
    }
    
    /**
     * Tests the 'getMembers' method
     *
     */
    public function testGetMembers()
    {
        $membs = array();
        $membs = array_merge($membs, $this->_meta->getFieldNames());
        $membs = array_merge($membs, $this->_meta->getRelationNames());
        $membs = array_merge($membs, get_class_methods($this->_meta->getEntityName()));
        
        $this->assertEquals($membs, $this->_meta->getMembers());
    }
    
    /**
     * Tests the 'getPrimary' method
     *
     */
    public function testGetPrimary()
    {
        $primary = $this->_meta->getPrimary();
        $this->assertEquals(array('bugId'), $primary);
    }
    
    /**
     * Tests the 'getRelation' method
     *
     */
    public function testGetRelation()
    {
        $rel = $this->_meta->getRelation('reporter');
        $this->assertType('Xyster_Orm_Relation', $rel);
        $this->assertEquals('reporter', $rel->getName());
    }
    
    /**
     * Tests that 'getRelation' throws an exception for a bad name
     *
     */
    public function testGetRelationWithBadName()
    {
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $this->_meta->getRelation('foobar');
    }
    
    /**
     * Tests the 'getRelations' method
     *
     */
    public function testGetRelations()
    {
        $relations = $this->_meta->getRelations();
        $this->assertType('array', $relations);
        foreach( $relations as $rel ) {
            $this->assertType('Xyster_Orm_Relation', $rel);
        }
    }
    
    /**
     * Tests the 'getRelationNames' method
     *
     */
    public function testGetRelationNames()
    {
        $names = $this->_meta->getRelationNames();
        $this->assertType('array', $names);
        $this->assertEquals($names, array_keys($this->_meta->getRelations()));
    }
    
    /**
     * Tests the 'isRelation' method
     *
     */
    public function testIsRelation()
    {
        $this->assertTrue($this->_meta->isRelation('assignee'));
        $this->assertFalse($this->_meta->isRelation('foobar'));
    }
    
    public function testBelongsTo()
    {
        
    }

    public function testHasJoined()
    {
        
    }
    
    public function testHasMany()
    {
        
    }
    
    public function testHasOne()
    {
        $this->_meta->hasOne('reportingAccount', array('class'=>'MockAccount','id'=>'reportedBy'));
    }

    /**
     * Tests the creating an existing relation throws an exception
     *
     */
    public function testCreateExistingRelation()
    {
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $this->_meta->belongsTo('reporter', array('class'=>'MockAccount','id'=>'reportedBy'));
    }
}