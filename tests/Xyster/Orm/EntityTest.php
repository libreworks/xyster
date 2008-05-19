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
// Call Xyster_Orm_EntityTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_EntityTest::main');
}

/**
 * PHPUnit test case
 */
require_once dirname(__FILE__) . '/TestSetup.php';
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
        $this->setExpectedException('Xyster_Orm_Entity_Exception');
        Xyster_Orm_Loader::loadEntityClass('MockBugProduct');
        $product = new MockBugProduct();
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
     * Tests the 'equals' method
     *
     */
    public function testEquals()
    {
        $entity = $this->_getMockEntity();
        $this->assertTrue($entity->equals($entity));
    }
    
    /**
     * Tests the 'equals' method on entities with a primary key
     *
     */
    public function testEqualsWithPk()
    {
    	$entity = $this->_getMockEntity();
    	$clone = clone $entity;
        $notEqualTo = $this->_getMockEntityWithNoPk();
        
        $this->assertFalse($entity->equals('a string'));
    	$this->assertFalse($entity->equals($notEqualTo));
        $this->assertFalse($notEqualTo->equals($entity));
    	$this->assertTrue($entity->equals($clone));
    	$this->assertTrue($clone->equals($entity));
    	$clone->bugId = 99999;
    	$this->assertFalse($entity->equals($clone));
    	$this->assertFalse($clone->equals($entity));
    }
    
    /**
     * Tests the 'equals' method on entities with no primary key
     */
    public function testEqualsWithNoPk()
    {
    	$entity = $this->_getMockEntityWithNoPk();
    	$clone = clone $entity;
    	$this->assertTrue($entity->equals($clone));
    	
    	$entity->bugDescription = 'This is normal';
    	$clone->bugDescription = 'This is weird';
    	$this->assertFalse($entity->equals($clone));
    	
    	$clone->bugDescription = 'This is normal';
    	$this->assertTrue($entity->equals($clone));
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
     * Tests the primary key as string method
     *
     */
    public function testGetPrimaryKeyAsString()
    {
        $bugValues = current($this->_bugValues);

        $entity = new MockBug($bugValues);
        
        $preSet = $entity->getPrimaryKeyAsString();
        $this->assertEquals('bugId=' . $bugValues['bugId'], $preSet);
        
        $entity->bugId = 99;
        
        $this->assertEquals('bugId=' . 99, $entity->getPrimaryKeyAsString());
        $this->assertEquals($preSet, $entity->getPrimaryKeyAsString(true));
    }

    /**
     * Tests the hashCode method
     *
     */
    public function testHashCode()
    {
    	$entity = $this->_getMockEntity();

    	$clone = clone $entity;
        $this->assertEquals($entity->hashCode(), $clone->hashCode());
    	
        $another = $this->_getMockEntityWithNoPk();
        $this->assertNotEquals($entity->hashCode(), $another->hashCode());
        
        $anotherBlank = $this->_getMockEntityWithNoPk();
        $this->assertEquals($another->hashCode(), $anotherBlank->hashCode());
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
        $this->setExpectedException('Xyster_Orm_Entity_Exception');
        $var = $entity->foobar;
    }
    
    /**
     * Tests setting a bad field throws an exception
     *
     */
    public function testSetBadField()
    {
        $entity = $this->_getMockEntity();
        $this->setExpectedException('Xyster_Orm_Entity_Exception');
        $entity->foobar = 12345;
    }
    
    /**
     * Tests setting a field works correctly
     *
     */
    public function testSetField()
    {
        $entity = $this->_getMockEntityWithNoPk();
        $this->assertFalse($entity->isDirty());
        
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
     * Tests that loading a relation will not set an entity dirty
     *
     */
    public function testGetRelatedNotDirty()
    {
        $reporter = new MockAccount(array('accountName'=>'doublecompile'));
        $this->assertFalse($reporter->isDirty(), 'Entity should not be dirty right after load');
        $reporter->assigned;
        $reporter->verified;
        $bugs = $reporter->reported;
        $this->assertType('MockBugSet', $bugs);
        $this->assertFalse($reporter->isDirty(), 'Entity should not be dirty after loading a relation');
    }
    
    /**
     * Tests setting an entity or set works as expected
     *
     */
    public function testSetRelatedAndIsLoaded()
    {
        // a new entity doesn't have relations loaded
        $entity = $this->_getMockEntityWithNoPk();
        $this->assertFalse($entity->isLoaded('reporter'));
        $this->assertFalse($entity->isLoaded('products'));

        // use of property syntax
        $reporter = new MockAccount();
        $entity->reporter = $reporter;
        $this->assertSame($reporter, $entity->reporter);
        $this->assertTrue($entity->isDirty());
        $this->assertTrue($entity->isLoaded('reporter'));
        $entity->setDirty(false);
        
        // use of method syntax
        $reporter2 = new MockAccount();
        $entity->setReporter($reporter2);
        $this->assertSame($reporter2, $entity->reporter);
        $this->assertTrue($entity->isDirty());
        $entity->setDirty(false);
        
        // use of property syntax
        Xyster_Orm_Loader::loadSetClass('MockProduct');
        $products = new MockProductSet();
        $entity->products = $products;
        $this->assertSame($products, $entity->products);
        $this->assertTrue($entity->isLoaded('products'));
        $this->assertTrue($entity->isDirty());
        $entity->setDirty(false);
        
        // use of property syntax
        $products2 = new MockProductSet();
        $entity->setProducts($products2);
        $this->assertSame($products2, $entity->products);
        $this->assertTrue($entity->isDirty());
    }
    
    /**
     * Tests setting a related one-to-one entity with a null value
     *
     */
    public function testSetRelatedWithNull()
    {
        $entity = $this->_getMockEntityWithNoPk();
        $entity->reportedBy = 'doublecompile';
        $entity->setDirty(false);
        $this->assertFalse($entity->isLoaded('reporter'));
        $this->assertFalse($entity->isLoaded('products'));
        $reporter = $entity->reporter;
        $this->assertType('MockAccount', $reporter);
        $this->assertTrue($entity->isLoaded('reporter'));
        $this->assertFalse($entity->isDirty());
        
        $entity->reporter = null;
        $this->assertTrue($entity->isDirty());
        $this->assertNull($entity->reporter);
        $this->assertNull($entity->reportedBy);
        $this->assertTrue($entity->isLoaded('reporter'));
    }
    
    /**
     * Tests setting a related entity of the wrong type throws an exception
     *
     */
    public function testSetRelatedOneWithWrongType()
    {
        $entity = $this->_getMockEntityWithNoPk();
        $this->setExpectedException('Xyster_Orm_Exception');
        $entity->reporter = $this->_getMockEntityWithNoPk();
    }

    /**
     * Tests setting a related set of the wrong type throws an exception
     *
     */
    public function testSetRelatedManyWithWrongType()
    {
        $entity = $this->_getMockEntityWithNoPk();
        $this->setExpectedException('Xyster_Orm_Exception');
        $entity->products = new MockAccountSet();
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
    
    /**
     * Tests the 'addListener' method
     *
     */
    public function testAddListener()
    {
        $entity = $this->_getMockEntity();
        $listener = new Xyster_Orm_EntityTest_Listener;
        $listener2 = new Xyster_Orm_EntityTest_Listener_Relation;
        $this->assertTrue($entity->addListener($listener));
        $this->assertFalse($entity->addListener($listener)); // second time
        $this->assertTrue($entity->addListener($listener2));
        
        $this->assertFalse($listener->setField);
        $entity->bugDescription .= ' Thingy!';
        $this->assertTrue($listener->setField);
        
        $this->assertFalse($listener2->setRelation);
        $entity->reporter = new MockAccount();
        $this->assertTrue($listener2->setRelation);
    }
    
    
    /**
     * Tests the 'removeListener' method
     *
     */
    public function testRemoveListener()
    {
        $entity = $this->_getMockEntity();
        $listener = new Xyster_Orm_EntityTest_Listener;
        $listener2 = new Xyster_Orm_EntityTest_Listener;
        $this->assertTrue($entity->addListener($listener));
        $this->assertTrue($entity->removeListener($listener));
        $this->assertFalse($entity->removeListener($listener2)); // not added
    }
    
    /**
     * Tests the 'clearListeners' method 
     *
     */
    public function testClearListeners()
    {
        $entity = $this->_getMockEntity();
        $listener = new Xyster_Orm_EntityTest_Listener;
        $entity->addListener($listener);
        $entity->clearListeners();
        $this->assertAttributeEquals(null, '_listeners', $entity);
    }
    
    /**
     * Tests the '_getLookup' method 
     *
     */
    public function testGetLookup()
    {
        $entity = $this->_getMockEntity();
        $lookup = $entity->createdOnDate;
        $expected = new Zend_Date($entity->createdOn);
        $this->assertEquals($expected, $lookup);
        $this->assertEquals($expected, $entity->getCreatedOnDate());
    }
    
    /**
     * Tests the '_setLookup' method
     *
     */
    public function testSetLookup()
    {
        $entity = $this->_getMockEntity();
        $newDate = new Zend_Date();
        $expected = $newDate->get(Zend_Date::ISO_8601);
        $entity->createdOnDate = $newDate;
        $this->assertSame($newDate, $entity->createdOnDate);
        $this->assertEquals($expected, $entity->createdOn);
    }
}

require_once 'Xyster/Orm/Entity/Listener.php';

class Xyster_Orm_EntityTest_Listener extends Xyster_Orm_Entity_Listener
{
    public $setField = false;
    
    public function onSetField( Xyster_Orm_Entity $entity, $name, $old, $new )
    {
        $this->setField = true;
    }
}

class Xyster_Orm_EntityTest_Listener_Relation extends Xyster_Orm_Entity_Listener
{
    public $setRelation = false;
    
    public function onSetRelation( Xyster_Orm_Entity $entity, $name, $old, $new )
    {
        $this->setRelation = true;
    }
}

// Call Xyster_Orm_EntityTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_EntityTest::main') {
    Xyster_Orm_EntityTest::main();
}