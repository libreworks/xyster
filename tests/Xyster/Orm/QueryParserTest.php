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
require_once dirname(__FILE__) . '/TestSetup.php';
/**
 * @see Xyster_Orm_Query
 */
require_once 'Xyster/Orm/Query/Parser.php';
/**
 * Test for Xyster_Orm_Query
 *
 */
class Xyster_Orm_Query_ParserTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Query_Parser
     */
    protected $_parser;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_parser = new Xyster_Orm_Query_Parser($this->_mockFactory());
    }
    
    /**
     * Tests the 'assertValidFieldForClass' method
     *
     */
    public function testValidField()
    {
        try { 
            $this->_parser->assertValidFieldForClass('bugDescription', 'MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Should not throw exception' . $thrown->getMessage());
        }
        
        try {
            $this->assertFalse($this->_parser->assertValidFieldForClass('doesntExist', 'MockBug'));
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'assertValidFieldForClass' method with a relation
     *
     */
    public function testValidFieldRelation()
    {
        try { 
            $this->_parser->assertValidFieldForClass('max(reporter->accountName)', 'MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Should not throw exception: ' . $thrown->getMessage());
        }

        try { 
            $this->_parser->assertValidFieldForClass('products->count()', 'MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Should not throw exception: ' . $thrown->getMessage());
        }
    }
    
    /**
     * Tests the 'assertValidFieldForClass' method with a method call
     *
     */
    public function testValidFieldMethod()
    {
        try {
            $this->_parser->assertValidFieldForClass('getCapitalOfNebraska(1,"test",createdOn)', 'MockBug');
        } catch ( Exception $thrown ) {
            $this->fail('Should not throw exception: ' . $thrown->getMessage());
        }
        
        try {
            $this->_parser->assertValidFieldForClass('nonExistantMethod()', 'MockBug');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'isMethodCall' method
     *
     */
    public function testIsMethodCall()
    {
        $this->assertTrue($this->_parser->isMethodCall('thisBeAMethodCall()'));
        $this->assertTrue($this->_parser->isMethodCall('thisBeAMethodCall(123,"something")'));
        $this->assertFalse($this->_parser->isMethodCall('justSomeField'));
    }
    
    /**
     * Tests the 'isRuntime' method
     *
     */
    public function testIsRuntime()
    {
        $this->assertFalse($this->_parser->isRuntime(Xyster_Data_Field::named('bugDescription'), 'MockBug'));
        $this->assertFalse($this->_parser->isRuntime(Xyster_Data_Field::named('reporter->accountName'), 'MockBug'));
        
        $this->assertTrue($this->_parser->isRuntime(Xyster_Data_Field::named('reporter->accountName->somePublicField'), 'MockBug'));
        $this->assertTrue($this->_parser->isRuntime(Xyster_Data_Field::named('assignee->isDirty()'), 'MockBug'));
        $this->assertTrue($this->_parser->isRuntime(Xyster_Data_Field::named('getCapitalOfNebraska()'), 'MockBug'));
    }
    
    /**
     * Tests the 'isRuntime' method with criteria
     *
     */
    public function testIsRuntimeCriteria()
    {
        $field = Xyster_Data_Field::named('bugDescription');
        $criteria = Xyster_Data_Junction::all($field->notLike('foo%'),
            $field->notLike('bar%'));
            
        $this->assertFalse($this->_parser->isRuntime($criteria, 'MockBug'));
        
        $field = Xyster_Data_Field::named('getCapitalOfNebraska()');
        $criteria = Xyster_Data_Junction::all($field->notLike('foo%'),
            $field->notLike('bar%'));
            
        $this->assertTrue($this->_parser->isRuntime($criteria, 'MockBug'));
    }
    
    /**
     * Tests the 'isRuntime' method with sorts
     *
     */
    public function testIsRuntimeSort()
    {
        require_once 'Xyster/Data/Sort.php';
        
        $this->assertFalse($this->_parser->isRuntime(Xyster_Data_Sort::asc('bugDescription'), 'MockBug'));
        
        $this->assertTrue($this->_parser->isRuntime(Xyster_Data_Sort::asc('getCapitalOfNebraska()'), 'MockBug'));
    }
    
    /**
     * Tests the 'isRuntime' method
     *
     */
    public function testIsRuntimeBadType()
    {
        try {
            $this->_parser->isRuntime(array(), 'MockBug');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'isValidField' method
     *
     */
    public function testIsValidField()
    {
        $this->assertFalse($this->_parser->isValidField('fieldName!'));
        $this->assertTrue($this->_parser->isValidField('fieldName'));
        $this->assertTrue($this->_parser->isValidField('fieldName->another'));
        $this->assertTrue($this->_parser->isValidField('fieldName->another()'));
        $this->assertTrue($this->_parser->isValidField('testMethodCall(123,"AOEU",anotherField)'));
        $this->assertFalse($this->_parser->isValidField('testMethodCall(123,%%%,anotherField)'));
    }
    
    /**
     * Tests the 'parseCriterion' method
     *
     */
    public function testParseCriterion()
    {
        $result = $this->_parser->parseCriterion('( field1 = 123 AND field2 not LIKE "%something%" AND ( field3 BETWEEN "A" anD "Z" ) ) AND field3 > 5');
        
        $this->assertType('Xyster_Data_Junction', $result);
        $this->assertEquals(4, count(Xyster_Data_Criterion::getFields($result)));
        
        $result = $this->_parser->parseCriterion('field1 = 123 AND field2 not LIKE "%something%" AND field3 > 5');
        
        $this->assertType('Xyster_Data_Junction', $result);
        $this->assertEquals(3, count(Xyster_Data_Criterion::getFields($result)));
        
        $result = $this->_parser->parseCriterion('(field1 = 123 AND field2 not LIKE "%something%") AND field3 > 5');
        $this->assertType('Xyster_Data_Junction', $result);
        $this->assertEquals(3, count(Xyster_Data_Criterion::getFields($result)));
    }

    /**
     * Tests the 'parseCriterion' method with nested 'OR' junctions
     *
     */
    public function testParseCriterionWithSubs()
    {
        $result = $this->_parser->parseCriterion('( (username = "mmouse" OR username = "dduck" OR username = "goofy") AND (((country <> "United States" AND city = "Paris")) OR (country <> "Japan")) )');

        $this->assertType('Xyster_Data_Junction', $result);
        $this->assertEquals(6, count(Xyster_Data_Criterion::getFields($result)));
    }
    
    /**
     * Tests the 'parseCriterion' method with nested 'OR' junctions
     *
     */
    public function testParseCriterionWithSubsAndAggregates()
    {
        $result = $this->_parser->parseCriterion('( (max(username) = "mmouse" OR max(username) = "dduck" OR max(username) = "goofy") AND (((min(country) <> "United States" AND min(city) = "Paris")) OR (min(country) <> "Japan")) )');

        $this->assertType('Xyster_Data_Junction', $result);
        $this->assertEquals(6, count(Xyster_Data_Criterion::getFields($result)));
    }
    
    /**
     * Tests parsing an expression with a bad literal
     *
     */
    public function testParseExpressionBadLiteral()
    {
        try {
            $this->_parser->parseExpression('username > %aoeu!');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }

    /**
     * Tests parsing an expression with a bad operator
     *
     */
    public function testParseExpressionBadOperator()
    {
        try {
            $this->_parser->parseExpression('username %%% "value"');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests parsing an expression with a between operator and just one value
     *
     */
    public function testParseExpressionBetween()
    {
        try {
            $this->_parser->parseExpression('username BETWEEN "value"');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests parsing an expression with an "in" operator
     *
     */
    public function testParseExpressionIn()
    {
        $result = $this->_parser->parseExpression('username NOT in ("mmouse", "dduck","goofy")');
        $this->assertType('Xyster_Data_Expression', $result);
        $this->assertEquals('NOT IN', $result->getOperator());
        $this->assertEquals(array("mmouse","dduck", "goofy"), $result->getRight());
    }
    
    /**
     * Tests parsing an expression with a between operator and just one value
     *
     */
    public function testParseExpressionInBadValue()
    {
        try {
            $this->_parser->parseExpression('username in "value"');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'parseField' method
     *
     */
    public function testParseField()
    {
        $field = $this->_parser->parseField('myField');
        
        $this->assertType('Xyster_Data_Field', $field);
        $this->assertEquals('myField', $field->getName());
        
        $field = $this->_parser->parseField('MAX(createdOn)');
        $this->assertType('Xyster_Data_Field_Aggregate', $field);
        $this->assertEquals('createdOn', $field->getName());
        $this->assertEquals(Xyster_Data_Aggregate::Maximum(), $field->getFunction());
    }
    
    /**
     * Tests the 'parseFieldAlias' method
     *
     */
    public function testParseFieldAlias()
    {
        $field = $this->_parser->parseFieldAlias('myField as MyAlias');
        
        $this->assertType('Xyster_Data_Field', $field);
        $this->assertEquals('myField', $field->getName());
        $this->assertEquals('MyAlias', $field->getAlias());
        
        $field = $this->_parser->parseFieldAlias('username "UserNick"');
        
        $this->assertType('Xyster_Data_Field', $field);
        $this->assertEquals('username', $field->getName());
        $this->assertEquals('UserNick', $field->getAlias());
    }
    
    /**
     * Tests the 'parseQuery' method
     *
     */
    public function testParseQuery()
    {
        require_once 'Xyster/Orm/Query.php';
        $query = new Xyster_Orm_Query('MockBug', $this->_mockFactory()->getManager());
        $xsql = 'WHerE getCapitalOfNebraska() = "Lincoln" aND bugId > 0 ' 
            . 'ORDeR bY bugDescription asc LIMIT 5 OFFSET 0';
        $this->_parser->parseQuery($query, $xsql);
        
        $this->assertEquals(1, count($query->getWhere()));
        $this->assertEquals(1, count($query->getOrder()));
    }
    
    /**
     * Tests the 'parseReportQuery' method
     * 
     */
    public function testParseReportQuery()
    {
        require_once 'Xyster/Orm/Query/Report.php';
        $query = new Xyster_Orm_Query_Report('MockBug', $this->_mockFactory()->getManager());
        $xsql = 'SelecT DISTINCT reportedBy, count(bugId) as numOfBugs '
            . 'WHerE getCapitalOfNebraska() = "Lincoln" aND bugId > 0 ' 
        	. 'GRoup By reportedBy '
        	. 'HaVING count(bugId) > 0 '
            . 'ORDeR bY reportedBy asc LIMIT 5 OFFSET 0';
        $this->_parser->parseReportQuery($query, $xsql);
        
        $this->assertEquals(2, count($query->getFields()));
        $this->assertEquals(1, count($query->getGroup()));
        $this->assertEquals(1, count($query->getWhere()));
        $this->assertEquals(1, count($query->getOrder()));
        $this->assertEquals(1, count($query->getHaving()));
        $this->assertEquals(Xyster_Data_Expression::gt('count(bugId)', '0'), current($query->getHaving()));
        
        try {
            $this->_parser->parseReportQuery($query, 'doesnT start with "select"');
        } catch ( Xyster_Orm_Query_Parser_Exception $thrown ) {
            return;
        }
        $this->fail('Exception not thrown');
    }
    
    /**
     * Tests the 'parseSort' method
     *
     */
    public function testParseSort()
    {
        $sort = $this->_parser->parseSort('myField DESC');
        
        $this->assertType('Xyster_Data_Sort', $sort);
        $this->assertEquals('DESC', $sort->getDirection());
        $this->assertEquals('myField', $sort->getField()->getName());
    }
}