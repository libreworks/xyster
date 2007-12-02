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
require_once dirname(__FILE__) . '/QueryTest.php';
/**
 * @see Xyster_Orm_Query_Report
 */
require_once 'Xyster/Orm/Query/Report.php';
/**
 * @see Xyster_Data_Expression
 */
require_once 'Xyster/Data/Expression.php';
/**
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * Test for Xyster_Orm_Query
 *
 */
class Xyster_Orm_Query_ReportTest extends Xyster_Orm_QueryTest
{
    /**
     * @var Xyster_Orm_Query_Report
     */
    protected $_query;
    
    /**
     * Sets up the query
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_query = new Xyster_Orm_Query_Report('MockBug', $this->_mockFactory()->getManager());
    }

    /**
     * Tests the distinct method
     *
     */
    public function testDistinct()
    {
        $return = $this->_query->distinct();
        
        $this->assertSame($this->_query, $return); // tests fluent
        $this->assertTrue($this->_query->isDistinct());
        
        $this->_query->distinct(false);
        
        $this->assertFalse($this->_query->isDistinct());
    }
    
    /**
     * Tests the 'execute' method
     *
     */
    public function testExecute()
    {
        $this->_query->field(Xyster_Data_Field::named('__get("createdOn")'))
            ->where(Xyster_Data_Expression::eq('getCapitalOfNebraska()', 'Lincoln'))
            ->where(Xyster_Data_Expression::gt('bugId', 0))
            ->order(Xyster_Data_Sort::asc('bugDescription'))
            ->order(Xyster_Data_Sort::desc('__get("createdOn")'))
            ->limit(3, 1);
            
        $return = $this->_query->execute();
        
        $this->assertType('Xyster_Data_Set', $return);
        $this->assertLessThanOrEqual(3, count($return));
    }
    
    /**
     * Tests the 'execute' method with just an aggregate field
     *
     */
    public function testExecuteAggregateBackend()
    {
        $this->_query->field(Xyster_Data_Field::max('createdOn'))
            ->where(Xyster_Data_Expression::gt('bugId', 0));
            
        $return = $this->_query->execute();
        
        $this->assertType('Xyster_Data_Set', $return);
    }
    
    /**
     * Tests the 'execute' method with just an aggregate field
     *
     */
    public function testExecuteAggregateRuntime()
    {
        $this->_query->field(Xyster_Data_Field::max('createdOn'))
            ->where(Xyster_Data_Expression::eq('getCapitalOfNebraska()', 'Lincoln'));
            
        $return = $this->_query->execute();
        
        $this->assertType('Xyster_Data_Set', $return);
    }
    
    /**
     * Tests executing with fields not grouped or aggregated errors
     *
     */
    public function testExecuteFieldNotGrouped()
    {
        $this->_query->field(Xyster_Data_Field::named('bugDescription'))
            ->group(Xyster_Data_Field::group('getCapitalOfNebraska()'))
            ->having(Xyster_Data_Expression::eq('max(getCapitalOfNebraska())', 'Lincoln'));
            
        $this->setExpectedException('Xyster_Orm_Query_Exception');
        $return = $this->_query->execute();
    }
    
    /**
     * Tests the 'execute' method with a group by specified
     *
     */
    public function testExecuteGrouped()
    {
        $this->_query->field(Xyster_Data_Field::group('getCapitalOfNebraska()'))
            ->having(Xyster_Data_Expression::eq('max(getCapitalOfNebraska())', 'Lincoln'))
            ->where(Xyster_Data_Expression::gt('bugId', 0))
            ->order(Xyster_Data_Sort::asc('bugDescription'))
            ->order(Xyster_Data_Sort::desc('__get("createdOn")'))
            ->limit(3, 1);
            
        $return = $this->_query->execute();
        
        $this->assertType('Xyster_Data_Set', $return);
        $this->assertLessThanOrEqual(3, count($return));
    }
    
    /**
     * Tests the 'field' method
     *
     */
    public function testFieldBackend()
    {
        $fields = array(Xyster_Data_Field::named('bugId'),
                Xyster_Data_Field::named('bugDescription'),
                Xyster_Data_Field::named('createdOn')
            );
            
        foreach( $fields as $field ) {
            $return = $this->_query->field($field);
            $this->assertSame($this->_query, $return); // tests fluent
        }
        
        $select = $this->_query->getFields();
        foreach( $fields as $field ) {
            $this->assertContains($field, $select);
        }
        
        $this->assertFalse($this->_query->hasRuntimeFields());
        $this->assertFalse($this->_query->isRuntime());
    }
    
    /**
     * Tests adding a grouped field with the 'field' method works correctly
     *
     */
    public function testFieldGroup()
    {
        $field = Xyster_Data_Field::group('reportedBy');
        
        $this->_query->field($field);
        
        $this->assertContains($field, $this->_query->getGroup());
        $this->assertNotContains($field, $this->_query->getFields());
    }
    
    /**
     * Tests the 'field' method with runtime fields
     *
     */
    public function testFieldRuntime()
    {
        $fields = array(Xyster_Data_Field::named('getCapitalOfNebraska()','capital'),
                Xyster_Data_Field::named('__get("assignedTo")','assignedTo')
            );
            
        foreach( $fields as $field ) {
            $return = $this->_query->field($field);
            $this->assertSame($this->_query, $return); // tests fluent
        }
        
        $select = $this->_query->getFields();
        foreach( $fields as $field ) {
            $this->assertContains($field, $select);
        }
        
        $this->assertTrue($this->_query->hasRuntimeFields());
        $this->assertTrue($this->_query->isRuntime());
    }
    
    /**
     * Tests the 'group' method
     *
     */
    public function testGroupBackend()
    {
        $groups = array(Xyster_Data_Field::group('reportedBy'),
                Xyster_Data_Field::group('assignedTo')
            );
            
        foreach( $groups as $group ) {
            $return = $this->_query->group($group);
            $this->assertSame($this->_query, $return); // tests fluent
        }
        
        $groupBy = $this->_query->getGroup();
        foreach( $groups as $group ) {
            $this->assertContains($group, $groupBy);
        }
        
        $this->assertFalse($this->_query->hasRuntimeGroup());
        $this->assertFalse($this->_query->isRuntime());
    }
    
    /**
     * Tests the 'group' method with a runtime field
     *
     */
    public function testGroupRuntime()
    {
        $groups = array(Xyster_Data_Field::group('getCapitalOfNebraska()','capital'),
                Xyster_Data_Field::group('__get("assignedTo")','assignedTo')
            );
            
        foreach( $groups as $group ) {
            $return = $this->_query->group($group);
            $this->assertSame($this->_query, $return); // tests fluent
        }
        
        $groupBy = $this->_query->getGroup();
        foreach( $groups as $group ) {
            $this->assertContains($group, $groupBy);
        }
        
        $this->assertTrue($this->_query->hasRuntimeGroup());
        $this->assertTrue($this->_query->isRuntime());
    }
    
    /**
     * Tests the 'having' method
     *
     */
    public function testHavingBackend()
    {
        $having = array(Xyster_Data_Expression::gt('MAX(bugId)',0),
            Xyster_Data_Expression::lt('min(createdOn)','2007-01-01')
            );
            
        foreach( $having as $criterion ) {
            $return = $this->_query->having($criterion);
            $this->assertSame($this->_query, $return);
        }
        
        $havings = $this->_query->getHaving();
        foreach( $having as $criterion ) {
            $this->assertContains($criterion, $havings);
        }
        
        $this->assertFalse($this->_query->hasRuntimeGroup());
        $this->assertFalse($this->_query->isRuntime());
    }
    
    /**
     * Tests adding a having criteria with no aggregate errors
     *
     */
    public function testHavingNotAggregate()
    {
        $this->setExpectedException('Xyster_Orm_Query_Exception');
        $this->_query->having(Xyster_Data_Expression::eq('bugDescription','12345'));
    }
    
    /**
     * Tests the 'having' method with runtime criteria
     *
     */
    public function testHavingRuntime()
    {
        $having = array(Xyster_Data_Expression::gt('MAX(__get("bugId"))',0),
            Xyster_Data_Expression::lt('min(__get("createdOn"))','2007-01-01')
            );
            
        foreach( $having as $criterion ) {
            $return = $this->_query->having($criterion);
            $this->assertSame($this->_query, $return);
        }
        
        $havings = $this->_query->getHaving();
        foreach( $having as $criterion ) {
            $this->assertContains($criterion, $havings);
        }
        
        $this->assertTrue($this->_query->hasRuntimeGroup());
        $this->assertTrue($this->_query->isRuntime());
    }
}