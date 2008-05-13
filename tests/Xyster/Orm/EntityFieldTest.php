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
require_once dirname(__FILE__) . '/TestSetup.php';
/**
 * @see Xyster_Orm_Entity_Field
 */
require_once 'Xyster/Orm/Entity/Field.php';
/**
 * Test for Xyster_Orm_Entity_Field
 *
 */
class Xyster_Orm_Entity_FieldTest extends Xyster_Orm_TestSetup
{
    /**
     * @var Xyster_Orm_Entity_Field
     */
    protected $object;
    
    protected $_details = array();
    
    public function setUp()
    {
        $name = 'awesomeId';
        
        $this->_details = array(
            'COLUMN_NAME' => 'awesome_id',
            'DATA_TYPE' => 'int',
            'LENGTH' => 4,
            'NULLABLE' => false,
            'DEFAULT' => null,
            'PRIMARY' => true,
            'PRIMARY_POSITION' => 1,
            'IDENTITY' => true
        );
        
        $this->object = new Xyster_Orm_Entity_Field('awesomeId', $this->_details);
    }
    
    /**
     * Tests constructing a field and getting its properties
     *
     */
    public function testField()
    {
        $field = $this->object;
        $details = $this->_details;
        
        $this->assertEquals('awesomeId', $field->getName());
        $this->assertEquals($details['COLUMN_NAME'], $field->getOriginalName());
        $this->assertEquals($details['DATA_TYPE'], $field->getType());
        $this->assertEquals($details['LENGTH'], $field->getLength());
        $this->assertEquals($details['NULLABLE'], $field->isNullable());
        $this->assertEquals($details['DEFAULT'], $field->getDefault());
        $this->assertEquals($details['PRIMARY'], $field->isPrimary());
        $this->assertEquals($details['PRIMARY_POSITION'], $field->getPrimaryPosition());
        $this->assertEquals($details['IDENTITY'], $field->isIdentity());
    }
    
    /**
     * Tests the 'addValidator' method
     *
     */
    public function testAddValidator()
    {
        require_once 'Zend/Validate/NotEmpty.php';
        $validator = new Zend_Validate_NotEmpty;
        $this->assertNull($this->object->getValidator());
        $return = $this->object->addValidator($validator, true);
        $this->assertSame($this->object, $return);
        $this->assertType('Zend_Validate', $this->object->getValidator());
    }
}