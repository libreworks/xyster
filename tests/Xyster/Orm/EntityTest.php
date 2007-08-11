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
 * @see Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';
/**
 * @see Xyster_Data_Expression
 */
require_once 'Xyster/Data/Expression.php';
/**
 * Test for Xyster_Orm_Entity
 *
 */
class Xyster_Orm_EntityTest extends Xyster_Orm_TestSetup
{
    /**
     * Tests that constructing an entity will import parameter values 
     *
     */
    public function testConstructWithValues()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        
        foreach( $bugValues as $key => $value ) {
            $this->assertSame($value, $entity->$key);
        }
        
        $this->assertEquals($bugValues, $entity->getBase());
    }
    
    /**
     * Tests that constructing an entity before its metadata is defined errors
     *
     */
    public function testConstructBeforeMetadata()
    {
        try {
            Xyster_Orm_Loader::loadEntityClass('MockBugProduct');
            $product = new MockBugProduct();
        } catch ( Xyster_Orm_Entity_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests that 'setMeta' will throw an exception if meta helper already set
     *
     */
    public function testSetMetaWhenExists()
    {
        $meta = $this->_mockFactory()->getEntityMeta('MockBug');
        try {
            Xyster_Orm_Entity::setMeta($meta);
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the dirty methods
     *
     */
    public function testDirty()
    {
        $entity = $this->_getMockEntityWithNoPk();
        
        $this->assertFalse($entity->isDirty(), 'New entities should be clean');
        
        $entity->setDirty();
        $this->assertTrue($entity->isDirty());
        
        $entity->setDirty(false);
        $this->assertFalse($entity->isDirty());
    }
    /**
     * Tests the 'get dirty fields' method
     *
     */
    public function testGetDirtyFields()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        $entity->reportedBy = 'smith';
        $entity->bugStatus = 'FIXED';
        
        $diff = array_diff_assoc($entity->toArray(), $bugValues);
        
        $this->assertEquals($diff, $entity->getDirtyFields());
    }

    /**
     * Tests that 'getDirtyFields' returns current values on new entity
     *
     */
    public function testGetDirtyFieldsOnNew()
    {
        $entity = $this->_getMockEntityWithNoPk();
        $this->assertEquals($entity->toArray(), $entity->getDirtyFields());
    }
    
    /**
     * Tests the primary key method
     *
     */
    public function testGetPrimaryKey()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        
        $preSet = $entity->getPrimaryKey();
        $this->assertEquals(array('bugId'=>$bugValues['bugId']), $preSet);
        
        $entity->bugId = 99;
        
        $this->assertEquals(array('bugId'=>99), $entity->getPrimaryKey());
        $this->assertEquals($preSet, $entity->getPrimaryKey(true));
    }
    /**
     * Tests the primary key as criterion method
     *
     */
    public function testGetPrimaryKeyAsCriterion()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        $expected = Xyster_Data_Expression::eq('bugId', $bugValues['bugId']);
        
        $preSet = $entity->getPrimaryKeyAsCriterion();
        $this->assertEquals($expected, $preSet);
        
        $entity->bugId = 99;
        $this->assertEquals($expected, $entity->getPrimaryKeyAsCriterion(true));
    }
    
    /**
     * Tests the primary key as criterion method on a composite key
     *
     */
    public function testGetPrimaryKeyAsCriterionCompositeKey()
    {
        $this->_setupClass('MockBugProduct');
        $entity = new MockBugProduct();
        $this->assertType('Xyster_Data_Junction', $entity->getPrimaryKeyAsCriterion());
    }
    
    /**
     * Tests getting a field works correctly
     *
     */
    public function testGetField()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        
        foreach( $bugValues as $key => $value ) {
            $this->assertSame($value, $entity->$key);
        }
        foreach( $bugValues as $key => $value ) {
            $method = 'get' . ucfirst($key);
            $this->assertSame($value, $entity->$method());
        }
    }
    
    /**
     * Tests getting a bad field throws an exception
     *
     */
    public function testGetBadField()
    {
        $entity = $this->_getMockEntity();
        try {
            $var = $entity->foobar;
        } catch ( Xyster_Orm_Entity_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests setting a field works correctly
     *
     */
    public function testSetField()
    {
        $entity = $this->_getMockEntityWithNoPk();
        
        $entity->reportedBy = 'doublecompile';
        $this->assertEquals('doublecompile', $entity->reportedBy);
        $this->assertTrue($entity->isDirty());
        $entity->setDirty(false);
        
        $entity->setReportedBy('astratton');
        $this->assertEquals('astratton', $entity->reportedBy);
        $this->assertTrue($entity->isDirty());
    }
    
    /**
     * Tests getting an entity or set works as expected
     *
     */
    public function testGetRelated()
    {
        $entity = $this->_getMockEntityWithNoPk();
        
        // relationships should be null on a new entity
        $this->assertNull($entity->reporter);
        $this->assertNull($entity->getReporter());
        
        // an empty set for a new entity
        $set = $entity->products;
        $this->assertType('MockProductSet', $set);        
        $this->assertSame($set, $entity->getProducts());
    }
    
    /**
     * Tests setting an entity or set works as expected
     *
     */
    public function testSetRelatedAndIsLoaded()
    {
        $reporter = new MockAccount();
        $entity = $this->_getMockEntityWithNoPk();
        $this->assertFalse($entity->isLoaded('reporter'));
        $this->assertFalse($entity->isLoaded('products'));
        
        $entity->reporter = $reporter;
        $this->assertSame($reporter, $entity->reporter);
        $this->assertTrue($entity->isDirty());
        $this->assertTrue($entity->isLoaded('reporter'));
        $entity->setDirty(false);
        
        $reporter2 = new MockAccount();
        $entity->setReporter($reporter2);
        $this->assertSame($reporter2, $entity->reporter);
        $this->assertTrue($entity->isDirty());
        $entity->setDirty(false);
        
        Xyster_Orm_Loader::loadSetClass('MockProduct');
        $products = new MockProductSet();
        $entity->products = $products;
        $this->assertSame($products, $entity->products);
        $this->assertTrue($entity->isLoaded('products'));
        $this->assertTrue($entity->isDirty());
        $entity->setDirty(false);
        
        $products2 = new MockProductSet();
        $entity->setProducts($products2);
        $this->assertSame($products2, $entity->products);
        $this->assertTrue($entity->isDirty());
    }
    
    /**
     * Tests setting a related entity of the wrong type throws an exception
     *
     */
    public function testSetRelatedOneWithWrongType()
    {
        $entity = $this->_getMockEntityWithNoPk();
        try { 
            $entity->reporter = $this->_getMockEntityWithNoPk();
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }

    /**
     * Tests setting a related set of the wrong type throws an exception
     *
     */
    public function testSetRelatedManyWithWrongType()
    {
        $entity = $this->_getMockEntityWithNoPk();
        try { 
            $entity->products = new MockAccountSet();
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the toArray method
     *
     */
    public function testToArray()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        
        $this->assertSame($bugValues, $entity->toArray());
    }
    
    /**
     * Tests the toString method
     *
     */
    public function testToString()
    {
        $bugValues = current($this->_bugValues);
        $entity = new MockBug($bugValues);
        
        $string = 'MockBug [';
        $first = true;
        foreach( $bugValues as $name => $value ) {
            if ( !$first ) {
                $string .= ',';
            }
            $string .= $name . '=' . $value;
            $first = false;
        }
        $string .= ']';
        
        $this->assertEquals($string, $entity->__toString());
    }
}