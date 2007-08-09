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
     * Tests constructing a field and getting its properties
     *
     */
    public function testField()
    {
        $name = 'awesomeId';
        
        $details = array(
            'COLUMN_NAME' => 'awesome_id',
            'DATA_TYPE' => 'int',
            'LENGTH' => 4,
            'NULLABLE' => false,
            'DEFAULT' => null,
            'PRIMARY' => true,
            'PRIMARY_POSITION' => 1,
            'IDENTITY' => true
        );
        
        $field = new Xyster_Orm_Entity_Field($name, $details);
        
        $this->assertEquals($name, $field->getName());
        $this->assertEquals($details['COLUMN_NAME'], $field->getOriginalName());
        $this->assertEquals($details['DATA_TYPE'], $field->getType());
        $this->assertEquals($details['LENGTH'], $field->getLength());
        $this->assertEquals($details['NULLABLE'], $field->isNullable());
        $this->assertEquals($details['DEFAULT'], $field->getDefault());
        $this->assertEquals($details['PRIMARY'], $field->isPrimary());
        $this->assertEquals($details['PRIMARY_POSITION'], $field->getPrimaryPosition());
        $this->assertEquals($details['IDENTITY'], $field->isIdentity());
    }
}