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
 * @see Xyster_Orm_TestSetup
 */
require_once dirname(__FILE__) . '/TestSetup.php';
/**
 * @see Xyster_Orm_Relation
 */
require_once 'Xyster/Orm/Relation.php';
/**
 * Test for Xyster_Orm_Relation
 *
 */
class Xyster_Orm_RelationTest extends Xyster_Orm_TestSetup
{
    /**
     * Tests to make sure that an invalid type throws an exception 
     *
     */
    public function testInvalidType()
    {
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $rel = new Xyster_Orm_Relation($this->_mockFactory()->getEntityMeta('MockBug'), 'Foobar', 'relationship');
    }
    
    /**
     * Tests the 'get' methods on the relation
     *
     */
    public function testGetMethods()
    {
        $name = 'goodProducts';
        $type = 'joined';
        
        $options = array(
            'class' => 'MockProduct',
            'id' => null,
            'filters' => 'productName like "awesome%"',
    
            'table' => 'zf_bugproducts',
            'left' => 'bug_id',
            'right' => 'product_id'
        );

        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $relation = new Xyster_Orm_Relation($meta, $type, $name, $options);

        $this->assertEquals($name, $relation->getName());
        $this->assertEquals('MockBug', $relation->getFrom());
        $this->assertEquals($options['class'], $relation->getTo());
        $this->assertEquals($type, $relation->getType());
        $this->assertNull($relation->getId());
        $this->assertType('Xyster_Data_Expression', $relation->getFilters());
        $this->assertEquals(array($options['left']), $relation->getLeft());
        $this->assertEquals(array($options['right']), $relation->getRight());
        $this->assertEquals($options['table'], $relation->getTable());
    }
    
    /**
     * Tests the methods on a many relation work as expected
     *
     */
    public function testGetMethodsOnMany()
    {
        $name = 'goodBugs';
        $type = 'many';
        
        $options = array(
            'class' => 'MockBug',
            'id' => 'reportedBy',
            'filters' => 'assignedTo like "awesome%"',
            'onDelete' => Xyster_Orm_Relation::ACTION_CASCADE,
            'onUpdate' => Xyster_Orm_Relation::ACTION_NONE    
        );

        $meta = $this->_mockFactory()->getEntityMeta('MockAccount');
        $relation = new Xyster_Orm_Relation($meta, $type, $name, $options);

        $this->assertEquals($name, $relation->getName());
        $this->assertEquals('MockAccount', $relation->getFrom());
        $this->assertEquals($options['class'], $relation->getTo());
        $this->assertEquals($type, $relation->getType());
        $this->assertEquals(array($options['id']), $relation->getId());
        $this->assertType('Xyster_Data_Expression', $relation->getFilters());
        $this->assertEquals($options['onDelete'], $relation->getOnDelete());
        $this->assertEquals($options['onUpdate'], $relation->getOnUpdate());
    }

    /**
     * Tests the auto-generated class name when doing a one-to-one
     *
     */
    public function testAutoClassNameOne()
    {
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $relation = new Xyster_Orm_Relation($meta, 'one', 'mockAccount', array('id'=>'assignedTo'));
        
        $this->assertEquals('MockAccount', $relation->getTo());
    }
    
    /**
     * Tests the auto-generated class name when doing a *-to-many
     *
     */
    public function testAutoClassNameMany()
    {
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $relation = new Xyster_Orm_Relation($meta, 'many', 'mockProducts');
        
        $this->assertEquals('MockProduct', $relation->getTo());
    }
    
    /**
     * Tests an exception is thrown if wrong number of keys supplied for joined
     *
     */
    public function testWrongCountForLeftJoinedKeys()
    {
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $relation = new Xyster_Orm_Relation($meta, 'joined', 'listOfProducts',
                array('class'=>'MockProduct', 'left'=>array()));
    }
    
    /**
     * Tests an exception is thrown if wrong number of keys supplied for joined
     *
     */
    public function testWrongCountForRightJoinedKeys()
    {
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $relation = new Xyster_Orm_Relation($meta, 'joined', 'listOfProducts',
            array('class'=>'MockProduct', 'right'=>array()));
    }
    
    
    
    /**
     * Tests to make sure 'many' and 'join' relations come up as collections
     *
     */
    public function testIsCollection()
    {
        $bugMeta = $this->_mockFactory()->getEntityMeta('MockBug');
        $accountMeta = $this->_mockFactory()->getEntityMeta('MockAccount');
        
        $one = new Xyster_Orm_Relation($bugMeta, 'one', 'reporter', array('class'=>'MockAccount'));
        
        $many = new Xyster_Orm_Relation($accountMeta, 'many', 'reported', array('class'=>'MockBug'));
        $join = new Xyster_Orm_Relation($bugMeta, 'joined', 'products', array('class'=>'MockProduct'));
        
        $this->assertFalse($one->isCollection());
        $this->assertTrue($many->isCollection());
        $this->assertTrue($join->isCollection());
    }

    /**
     * Tests the 'getReverse' method
     *
     */
    public function testGetReverse()
    {
        $accountMeta = $this->_mockFactory()->getEntityMeta('MockAccount');
        
        $relation = $accountMeta->getRelation('reported');
        
        $reverse = $relation->getReverse();
        $this->assertType('Xyster_Orm_Relation', $reverse);
        $this->assertEquals('belongs', $reverse->getType());
    }
    
    /**
     * Tests calling 'getReverse' on a not-many relation will throw exception
     *
     */
    public function testGetReverseNotMany()
    {
        $bugMeta = $this->_mockFactory()->getEntityMeta('MockBug');
        $one = new Xyster_Orm_Relation($bugMeta, 'one', 'reporter', array('class'=>'MockAccount'));
        
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $one->getReverse();
    }
    
    /**
     * Tests calling 'getReverse' when there is none
     *
     */
    public function testGetReverseNotThere()
    {
        $meta = $this->_mockFactory()->getEntityMeta('MockProduct');
        $meta->hasMany('mockBugProducts');
        
        $relation = $meta->getRelation('mockBugProducts');
        
        $reverse = $relation->getReverse();
        $this->assertFalse($reverse);
    }
    
    /**
     * Tests that 'relate' works correctly
     *
     */
    public function testRelate()
    {
        $manager = $this->_mockFactory()->getManager();
        $account = $manager->get('MockAccount', 'doublecompile');
        $meta = $this->_mockFactory()->getEntityMeta('MockAccount');
        $reported = $meta->getRelation('verified');
        $bug = $this->_getMockEntity();
        
        $this->assertFalse($bug->isDirty());
        $this->assertNotEquals($account->accountName, $bug->verifiedBy);
        
        $reported->relate($account, $bug);
        $this->assertSame($bug->verifier, $account);
        $this->assertEquals($account->accountName, $bug->verifiedBy);
        $this->assertTrue($bug->isDirty());
    }
    
    /**
     * Tests using relate on a not-many relation throws exception
     *
     */
    public function testRelateNotMany()
    {
        $manager = $this->_mockFactory()->getManager();
        $account = $manager->get('MockAccount', 'doublecompile');

        $bug = $this->_getMockEntity();
        
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $reported = $meta->getRelation('reporter');
        
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $reported->relate($account, $bug);
    }
    
    /**
     * Tests using the wrong from class on a relation throws exception
     *
     */
    public function testRelateWrongFromClass()
    {
        $bug = $this->_getMockEntity();
        
        $meta = $this->_mockFactory()->getEntityMeta('MockAccount');
        $reported = $meta->getRelation('reported');
        
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $reported->relate($bug, $bug);
    }
    
    /**
     * Tests using the wrong to class on a relation throws exception
     *
     */
    public function testRelateWrongToClass()
    {
        $manager = $this->_mockFactory()->getManager();
        $account = $manager->get('MockAccount', 'doublecompile');
                
        $meta = $this->_mockFactory()->getEntityMeta('MockAccount');
        $reported = $meta->getRelation('reported');
        
        $this->setExpectedException('Xyster_Orm_Relation_Exception');
        $reported->relate($account, $account);
        
    }

    /**
     * Tests loading one entity
     *
     */
    public function testLoadOne()
    {
        $bug = $this->_getMockEntity();
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $relation = $meta->getRelation('reporter');
        
        $account = $relation->load($bug);
        $this->assertType('MockAccount', $account);
        $this->assertEquals($bug->reportedBy, $account->accountName);
    }
    
    /**
     * Tests loading many
     *
     */
    public function testLoadMany()
    {
        $manager = $this->_mockFactory()->getManager();
        
        $account = $manager->get('MockAccount', 'mmouse');
        
        $meta = $this->_mockFactory()->getEntityMeta('MockAccount');
        $relation = $meta->getRelation('assigned');
        
        $bugs = $relation->load($account);
        $this->assertType('MockBugSet', $bugs);
        foreach( $bugs as $bug ) {
            $this->assertEquals($account->accountName, $bug->assignedTo);
            $this->assertSame($account, $bug->assignee);
        }
    }
    
    /**
     * Tests loading a 'many' relation with no primary key gives empty set
     *
     */
    public function testLoadManyWithNoPk()
    {
        $account = new MockAccount();
                
        $meta = $this->_mockFactory()->getEntityMeta('MockAccount');
        $relation = $meta->getRelation('reported');
        
        $set = $relation->load($account);

        $this->assertType('MockBugSet', $set);
        $this->assertTrue($set->isEmpty());
    }
    
    /**
     * Tests loading a 'joined' relation
     *
     */
    public function testLoadJoined()
    {
        $bug = $this->_getMockEntity();
        
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        $relation = $meta->getRelation('products');
        
        $products = $relation->load($bug);
        
        $this->assertType('MockProductSet', $products);
    }
}