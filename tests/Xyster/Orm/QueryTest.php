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

// Call Xyster_Orm_QueryTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_QueryTest::main');
}

require_once dirname(__FILE__) . '/TestSetup.php';
require_once 'Xyster/Orm/Query.php';
require_once 'Xyster/Data/Expression.php';
require_once 'Xyster/Data/Sort.php';

/**
 * Test for Xyster_Orm_Query
 *
 */
class Xyster_Orm_QueryTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Query
     */
    protected $_query;
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Orm_QueryTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
        
    /**
     * Sets up the query
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_query = new Xyster_Orm_Query('MockBug', $this->_mockFactory()->getManager());
    }
    
    /**
     * Tests the 'execute' method
     *
     */
    public function testExecute()
    {
        $this->_query->where(Xyster_Data_Expression::eq('getCapitalOfNebraska()', 'Lincoln'))
            ->where(Xyster_Data_Expression::gt('bugId', 0))
            ->order(Xyster_Data_Sort::asc('bugDescription'))
            ->order(Xyster_Data_Sort::desc('__get(\"createdOn\")'))
            ->limit(3, 1);
            
        $return = $this->_query->execute();
        
        $this->assertType('Xyster_Orm_Set', $return);
        $this->assertEquals(3, count($return));
    }
    
    /**
     * Tests the 'from' method
     *
     */
    public function testFrom()
    {
        $this->assertEquals('MockBug', $this->_query->getFrom());
    }
    
    /**
     * Tests the 'limit' method works
     *
     */
    public function testLimitOffset()
    {
        $limit = 10;
        $offset = 20;
        $return = $this->_query->limit($limit, $offset);
        
        $this->assertSame($this->_query, $return); // test fluent interface
        $this->assertEquals($limit, $this->_query->getLimit());
        $this->assertEquals($offset, $this->_query->getOffset());
    }
    
    /**
     * Tests the 'order' method
     *
     */
    public function testOrderBackend()
    {
        $orders = array(
                Xyster_Data_Sort::desc('createdOn'),
                Xyster_Data_Sort::asc('reportedBy'),
                Xyster_Data_Sort::asc('bugDescription')
            );
        
        foreach( $orders as $order ) {
            $return = $this->_query->order($order);
            $this->assertSame($this->_query, $return); // test fluent interface
        }
        
        $sorts = $this->_query->getOrder()->toArray();
        foreach( $orders as $order ) {
            $this->assertContains($order, $sorts);
        }
        
        $this->assertFalse($this->_query->hasRuntimeOrder());
        $this->assertFalse($this->_query->isRuntime());
    }
    
    /**
     * Tests the 'order' method with 'runtime' sorts
     *
     */
    public function testOrderRuntime()
    {
        $orders = array(
                Xyster_Data_Sort::desc('getCapitalOfNebraska()'),
                Xyster_Data_Sort::asc('reportedBy'),
                Xyster_Data_Sort::asc('bugDescription')
            );
        
        foreach( $orders as $order ) {
            $return = $this->_query->order($order);
            $this->assertSame($this->_query, $return); // test fluent interface
        }
        
        $sorts = $this->_query->getOrder()->toArray();
        foreach( $orders as $order ) {
            $this->assertContains($order, $sorts);
        }
        
        $this->assertTrue($this->_query->hasRuntimeOrder());
        $this->assertTrue($this->_query->isRuntime());
    }
    
    /**
     * Tests the 'where' method with 'backend' criterion
     *
     */
    public function testWhereBackend()
    {
        $criteria = array(Xyster_Data_Expression::eq('verifiedBy', 'rspeed'),
            Xyster_Data_Expression::eq('assignee->accountName', 'astratton'),
            Xyster_Data_Expression::eq('reportedBy', 'doublecompile'));
        
        foreach( $criteria as $criterion ) {
            $return = $this->_query->where($criterion);
            $this->assertSame($this->_query, $return); // test fluent interface
        }
        
        $where = $this->_query->getWhere();
        foreach( $criteria as $criterion ) {
            $this->assertContains($criterion, $where);
        }
        $where = $this->_query->getBackendWhere();
        foreach( $criteria as $criterion ) {
            $this->assertContains($criterion, $where);
        }
        
        $this->assertFalse($this->_query->hasRuntimeWhere());
        $this->assertFalse($this->_query->isRuntime());
    }
    
    /**
     * Tests the 'where' method with 'runtime' criterion
     *
     */
    public function testWhereRuntime()
    {
        $backend = array(Xyster_Data_Expression::eq('verifiedBy', 'rspeed'),
            Xyster_Data_Expression::eq('assignee->accountName', 'astratton'),
            Xyster_Data_Expression::eq('reportedBy', 'doublecompile'));
        $runtime = array(Xyster_Data_Expression::eq('getCapitalOfNebraska()', 'Lincoln'),
            Xyster_Data_Expression::gt('__get(\"bugId\")', 0),
            Xyster_Data_Expression::notLike('__get(\"bugDescription\")', 'Unknown title%')
            );
            
        foreach( $backend as $criterion ) {
            $return = $this->_query->where($criterion);
            $this->assertSame($this->_query, $return); // test fluent interface
        }
        foreach( $runtime as $criterion ) {
            $return = $this->_query->where($criterion);
            $this->assertSame($this->_query, $return); // test fluent interface
        }
        
        $where = $this->_query->getWhere();
        foreach( $backend as $criterion ) {
            $this->assertContains($criterion, $where);
        }
        foreach( $runtime as $criterion ) {
            $this->assertContains($criterion, $where);
        }
        
        $where = $this->_query->getBackendWhere();
        foreach( $backend as $criterion ) {
            $this->assertContains($criterion, $where);
        }
        foreach( $runtime as $criterion ) {
            $this->assertNotContains($criterion, $where);
        }
        
        $this->assertTrue($this->_query->hasRuntimeWhere());
        $this->assertTrue($this->_query->isRuntime());
    }

    /**
     * Tests the 'where' method errors for an aggregate criterion
     *
     */
    public function testWhereAggregate()
    {
        $this->setExpectedException('Xyster_Orm_Query_Exception');
        $this->_query->where(Xyster_Data_Expression::eq('MAX(assignedTo)','zzz'));
    }
}

// Call Xyster_Orm_QueryTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_QueryTest::main') {
    Xyster_Orm_QueryTest::main();
}
