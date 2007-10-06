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
 * Xyster_Orm_Mapper_TestSetup
 */
require_once dirname(__FILE__) . '/TestSetup.php';

/**
 * @see Xyster_Orm_Mapper_Translator
 */
require_once 'Xyster/Orm/Mapper/Translator.php';

require_once 'Xyster/Data/Sort.php';
require_once 'Xyster/Data/Field.php';


/**
 * Test for Xyster_Orm_Mapper
 *
 */
abstract class Xyster_Orm_Mapper_TestCommon extends Xyster_Orm_Mapper_TestSetup
{

    /**
     * Tests trying to set up a metadataCache with a bad registry key
     *
     */
    public function testConstructForCacheCache()
    {
        Zend_Registry::set('nonExistantRegistryKey', new stdClass);
        
        $this->_factory()->setDefaultMetadataCache(null);
    
        // try with a registry key that is fine
        try {
            $this->_setupClass('Product'); 
        } catch ( Xyster_Orm_Mapper_Exception $thrown ) {
            $this->fail('Exception should not be thrown');
        }
        
        // try with a registry key that isn't a Zend_Cache_Core
        try {
            $this->_setupClass('BugProduct');
            $this->fail('Exception not thrown');
        } catch ( Xyster_Orm_Mapper_Exception $thrown ) {
        }

        // try with a null key
        try { 
            $this->_setupClass('Account');
            $map = $this->_factory()->get('Account');
        } catch ( Xyster_Orm_Mapper_Exception $thrown ) {
            $this->fail('Exception should not be thrown');
        }
        $this->assertNull($map->getMetadataCache());
    }
    
    /**
     * Tests the 'delete' method
     *
     */
    public function testDelete()
    {
        $mapper = $this->_factory()->get('Bug');
        $bug = $mapper->get(1);
        
        $mapper->delete($bug);
        
        $all = $mapper->getAll();
        
        foreach( $all as $entity ) {
            $this->assertNotEquals($bug->bugId, $entity->bugId);
        }
        
        $bug = $mapper->get(1);
        $this->assertNull($bug);
    }
    
    /**
     * Tests the 'delete' method with a SET_NULL cascade
     *
     */
    public function testDeleteSetNull()
    {
        $mapper = $this->_factory()->get('Account');
        $account = $mapper->get('mmouse');
        $reported = $account->reported;
        $this->assertGreaterThan(0, count($reported));
        
        foreach( $reported as $bug ) {
            $this->assertEquals('mmouse', $bug->reportedBy);
            $this->assertSame($account, $bug->reporter);
        }
        
        $mapper->delete($account);
        
        foreach( $reported as $bug ) {
            $this->assertNull($bug->reportedBy);
            $this->assertNull($bug->reporter);
        }
    }
    
    /**
     * Tests the 'find' method
     *
     */
    public function testFind()
    {
        $criteria = array('reportedBy'=>'mmouse');
        
        $mapper = $this->_factory()->get('Bug');
        $entity = $mapper->find($criteria);
        $this->assertType('Bug', $entity);
        $this->assertEquals('mmouse', $entity->reportedBy);
        
        $this->_factory()->getManager()->getRepository()->add($entity);
        $entity2 = $mapper->find($criteria);
        $this->assertSame($entity, $entity2);
    }
    
    /**
     * Tests the 'findAll' method
     *
     */
    public function testFindAll()
    {
        $criteria = array('reportedBy'=>'mmouse');
        $mapper = $this->_factory()->get('Bug');
        
        $all = $mapper->findAll($criteria);
        $this->assertType('BugSet', $all);
        
        $this->_factory()->getManager()->getRepository()->addAll($all);
        
        $all2 = $mapper->findAll($criteria, Xyster_Data_Sort::asc('bugId'));
        $this->assertType('BugSet', $all2);
        
        // make sure the entities are identical
        $this->assertTrue($all2->containsAll($all));
        
        try { 
            $all = $mapper->findAll($criteria, Xyster_Data_Field::named('bugId'));
            $this->fail('Exception not thrown');
        } catch ( Xyster_Orm_Exception $thrown ) {
            return;
        }
    }
    
    /**
     * Tests the 'get' method
     *
     */
    public function testGet()
    {
        $pk = array('bugId'=>1);
        $mapper = $this->_factory()->get('Bug');
        $entity = $mapper->get($pk);
        $this->assertType('Bug', $entity);
        $this->assertEquals($pk, $entity->getPrimaryKey());
    }
    
    /**
     * Tests the 'getAll' method
     *
     */
    public function testGetAll()
    {
        $mapper = $this->_factory()->get('Bug');
        
        $all = $mapper->getAll();
        $this->assertType('BugSet', $all);
        
        $this->_factory()->getManager()->getRepository()->addAll($all);
        
        $some = $mapper->getAll(array(1,2,3));
        $this->assertType('BugSet', $some);
        $this->assertTrue($all->containsAll($some));
    }
    
    /**
     * Tests the instantiating a mapper with no Db setup will error
     *
     */
    public function testGetAdapterWithNoDb()
    {
        require_once dirname(__FILE__).'/../_files/CustomMapper.php';
        
        try {
            $mapper = new CustomMapper($this->_factory());
            $this->fail('Exception not thrown');
        } catch ( Xyster_Orm_Mapper_Exception $thrown ) {
        }
    }
    
    /**
     * Tests the 'getJoined' method
     *
     */
    public function testGetAndSaveJoined()
    {
        $mapper = $this->_factory()->get('Bug');
        $bug = $mapper->get(2);
        
        $this->_setupClass('Product');
        
        $products = $bug->products;
        
        $this->assertType('ProductSet', $products);
        
        $prodToRemove = null;
        foreach( $products as $product ) {
            $prodToRemove = $product;
        }
        
        $products->remove($prodToRemove);
        
        $pmapper = $this->_factory()->get('Product');
        
        $prods1And2 = $pmapper->getAll(array(1,2));
        $products->merge($prods1And2);
        
        $mapper->save($bug);
        
        $bug = $mapper->get(2);
        $pks = $bug->products->getPrimaryKeys();
        $this->assertNotContains($prodToRemove->getPrimaryKey(), $pks);
        foreach( $prods1And2 as $product ) {
            $this->assertContains($product->getPrimaryKey(), $pks);
        }
    }

    /**
     * Tests successful save of joined entities if unsaved entity added
     *
     */
    public function testSaveJoinedNew()
    {
        $mapper = $this->_factory()->get('Bug');
        $bug = $mapper->get(2);
        
        $this->_setupClass('Product');
        
        $products = $bug->products;
        
        $this->assertType('ProductSet', $products);

        $product = new Product();
        $product->productName = 'New product!';
        $products->add($product);
        
        $mapper->save($bug);
        
        $bug = $mapper->get(2);
        $pks = $bug->products->getPrimaryKeys();
        $this->assertNotNull($product->productId); // make sure entity saved
        $this->assertContains($product->getPrimaryKey(), $pks);
    }
    
    /**
     * Tests successful save of joined entities if none removed
     *
     */
    public function testSaveJoinedNoRemoved()
    {
        $mapper = $this->_factory()->get('Bug');
        $bug = $mapper->get(2);
        
        $this->_setupClass('Product');
        
        $products = $bug->products;
        
        $this->assertType('ProductSet', $products);
        
        $pmapper = $this->_factory()->get('Product');
        $prods1And2 = $pmapper->getAll(array(1,2));
        $products->merge($prods1And2);
        $mapper->save($bug);
        
        $bug = $mapper->get(2);
        $pks = $bug->products->getPrimaryKeys();
        foreach( $prods1And2 as $product ) {
            $this->assertContains($product->getPrimaryKey(), $pks);
        }
    }
    
    /**
     * Tests trying to save metadata in the cache and it throws an exception
     *
     */
    public function testGetFieldsWithBadSave()
    {
        $cache = new Zend_Cache_Core(array('automatic_serialization'=>true));
        $cache->setBackend(new Xyster_Orm_CacheMock(false));
        $this->_factory()->setDefaultMetadataCache($cache);
        
        try {
            $this->_setupClass('BugProduct');
            $this->fail('Exception not thrown');
        } catch ( Xyster_Orm_Mapper_Exception $thrown ) {
        }
    }
    
    /**
     * Tests a 'save' and insert
     *
     */
    public function testSaveInsert()
    {
        $mapper = $this->_factory()->get('Bug');
        $amapper = $this->_factory()->get('Account');
        
        $newAccount = new Account();
        $newAccount->accountName = 'doublecompile';
        
        $bug = new Bug();
        $bug->bugDescription = 'Not sure why this is still around...';
        $bug->bugStatus = 'OPEN';
        $bug->createdOn = date("Y-m-d H:i:s");
        $bug->updatedOn = date("Y-m-d H:i:s");
        $bug->reporter = $newAccount;
        $bug->assignee = $amapper->get('dduck');
        $bug->verifier = $amapper->get('goofy');
        
        $mapper->save($bug);
        
        $this->assertNotNull($bug->bugId);
        $this->assertNotNull($amapper->get('doublecompile'));
    }
    
    /**
     * Tests a 'save' and insert for a composite primary key
     *
     */
    public function testSaveInsertWithPrimaryKey()
    {
        $this->_setupClass('BugProduct');
        $mapper = $this->_factory()->get('BugProduct');
        
        $bp = new BugProduct();
        $bp->bugId = 4;
        $bp->productId = 4;
        
        $oldPk = $bp->getPrimaryKey(true);
        $mapper->save($bp);
        
        $this->assertNotEquals($oldPk, $bp->getPrimaryKey());
    }
    
    /**
     * Tests a 'save' and update
     *
     */
    public function testSaveUpdate()
    {
        $mapper = $this->_factory()->get('Bug');
        $amapper = $this->_factory()->get('Account');
        
        $bug = $mapper->get(1);
        
        $bug->updatedOn = date('Y-m-d H:i:s');
        $bug->bugDescription .= ' (test)';

        $values = $bug->toArray();
        
        $mapper->save($bug);
        
        $updated = $mapper->get(1);
        $this->assertEquals($updated->toArray(), $bug->toArray());
    }
        
    /**
     * Tests the 'refresh' method
     *
     */
    public function testRefresh()
    {
        $mapper = $this->_factory()->get('Bug');
        $entity = $mapper->get(1);
        $baseValues = $entity->getBase();
        $entity->reportedBy = 'doublecompile';

        $mapper->refresh($entity);
        $this->assertEquals($baseValues, $entity->getBase());
    }
    
    /**
     * Tests a regular query
     *
     */
    public function testQuery()
    {
        $mapper = $this->_factory()->get('Bug');
        
        require_once 'Xyster/Orm/Query.php';
        $query = new Xyster_Orm_Query('Bug', $this->_factory()->getManager());
        
        $query->where(Xyster_Data_Expression::neq('bugId', null));
        $query->where(Xyster_Data_Expression::neq('reporter->accountName', null));
        $query->order(Xyster_Data_Sort::asc('createdOn'));
        $query->limit(10);
        
        $result = $query->execute();
        
        $this->assertType('BugSet', $result);
        
        $this->_factory()->getManager()->getRepository()->addAll($result);
        
        $all = $mapper->getAll();
        $this->assertTrue($all->containsAll($result));
    }
    
    /**
     * Tests a report query
     *
     */
    public function testQueryReport()
    {
        $mapper = $this->_factory()->get('Bug');
        
        require_once 'Xyster/Orm/Query/Report.php';
        $query = new Xyster_Orm_Query_Report('Bug', $this->_factory()->getManager());
        
        $query->distinct();
        $query->field(Xyster_Data_Field::named('bugDescription'));
        $query->field(Xyster_Data_Field::named('createdOn'));
        $query->field(Xyster_Data_Field::named('reportedBy'));
        $query->field(Xyster_Data_Field::named('reporter->accountName', 'reporterName'));
        $query->where(Xyster_Data_Expression::neq('bugId', null));
        $query->where(Xyster_Data_Expression::neq('reporter->accountName', null));
        $query->order(Xyster_Data_Sort::asc('createdOn'));
        $query->limit(10);
        
        $result = $query->execute();
        
        $this->assertType('Xyster_Data_Set', $result);
    }
    
    /**
     * Tests a report query that's runtime
     *
     */
    public function testQueryReportGroup()
    {
        $mapper = $this->_factory()->get('Bug');
        
        require_once 'Xyster/Orm/Query/Report.php';
        $query = new Xyster_Orm_Query_Report('Bug', $this->_factory()->getManager());
        
        $query->field(Xyster_Data_Field::count('bugId','numOfBugs'));
        $query->where(Xyster_Data_Expression::neq('bugId', null));
        $query->group(Xyster_Data_Field::group('reportedBy'));
        $query->group(Xyster_Data_Field::group('assignedTo','assignedName'));
        $query->having(Xyster_Data_Expression::gt('count(bugId)',0));
        
        $result = $query->execute();
        
        $this->assertType('Xyster_Data_Set', $result);
        
    }

    /**
     * Tests a report query that's runtime
     *
     */
    public function testQueryReportGroupRuntime()
    {
        $mapper = $this->_factory()->get('Bug');
        
        require_once 'Xyster/Orm/Query/Report.php';
        $query = new Xyster_Orm_Query_Report('Bug', $this->_factory()->getManager());
        
        $query->field(Xyster_Data_Field::count('bugId','numOfBugs'));
        $query->where(Xyster_Data_Expression::neq('bugId', null));
        $query->where(Xyster_Data_Expression::eq('getCapitalOfNebraska()', 'Lincoln'));
        $query->group(Xyster_Data_Field::group('reportedBy'));
        $query->having(Xyster_Data_Expression::gt('count(bugId)',0));
        
        $result = $query->execute();
        
        $this->assertType('Xyster_Data_Set', $result);
    }

    
    /**
     * Tests that 'aliasField' will throw an exception for a runtime field
     *
     */
    public function testTranslatorRuntimeAlias()
    {
        $translator = new Xyster_Orm_Mapper_Translator($this->_db,
            'Bug', $this->_factory());
            
        $field = Xyster_Data_Field::named('getCapitalOfNebraska()');
        try {
            $translator->aliasField($field);
            $this->fail('Exception not thrown');
        } catch ( Xyster_Orm_Mapper_Exception $thrown ) {
        }
    }
}