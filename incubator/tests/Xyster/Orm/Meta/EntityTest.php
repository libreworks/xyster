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
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Orm/Meta/Entity.php';
require_once 'Xyster/Orm/Meta/Property.php';
require_once 'Xyster/Orm/Type/String.php';
require_once 'Xyster/Db/Column.php';
require_once 'Xyster/Db/Table.php';
require_once 'Xyster/Orm/Meta/Value/Basic.php';
require_once 'Xyster/Type/Property/Direct.php';

/**
 * Test class for Xyster_Orm_Meta_Entity.
 */
class Xyster_Orm_Meta_EntityTest extends PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $column = new Xyster_Db_Column("test_property");
        $table = new Xyster_Db_Table("foobar");
        $type = new Xyster_Orm_Type_String;
        $value = new Xyster_Orm_Meta_Value_Basic($table, $type, array($column));
        $property = new Xyster_Type_Property_Direct("testProperty");
        $prop = new Xyster_Orm_Meta_Property("foobar", $value, $property, true, true);
        $entityType = new Xyster_Type('Xyster_Orm_Meta_EntityTest');
        $properties = array($prop);
        $idCol = new Xyster_Db_Column('entity_id');
        $idValue = new Xyster_Orm_Meta_Value_Basic($table, $type, array($idCol));
        $idProp = new Xyster_Orm_Meta_Property("id", $idValue, new Xyster_Type_Property_Direct('id'), false, false);
        $vCol = new Xyster_Db_Column('version');
        $vValue = new Xyster_Orm_Meta_Value_Basic($table, $type, array($vCol));
        $vProp = new Xyster_Orm_Meta_Property('version', $vValue, new Xyster_Type_Property_Direct('version'), false, false);
        $options = array(
            Xyster_Orm_Meta_Entity::OPTION_LAZY => true,
            Xyster_Orm_Meta_Entity::OPTION_MUTABLE => true,
            Xyster_Orm_Meta_Entity::OPTION_PERSISTER => $entityType,
            Xyster_Orm_Meta_Entity::OPTION_PROXY => $entityType,
            Xyster_Orm_Meta_Entity::OPTION_TUPLIZER => $entityType,
            Xyster_Orm_Meta_Entity::OPTION_WHERE => "version > -1"
            );
        $object = new Xyster_Orm_Meta_Entity($entityType, $properties, $table,
            $idProp, $vProp, $options);
        
        self::assertSame($vProp, $object->getVersion());
        self::assertSame($idProp, $object->getIdProperty());
        self::assertTrue($object->hasIdProperty());
        self::assertTrue($object->isVersioned());
        self::assertTrue($object->hasNaturalId());
        self::assertEquals(__CLASS__, $object->getClassName());
        self::assertSame($table, $object->getTable());
        self::assertEquals('version > -1', $object->getWhere());
        self::assertTrue($object->isLazy());
        self::assertTrue($object->isMutable());
        self::assertSame($entityType, $object->getType());
        self::assertSame($entityType, $object->getPersisterType());
        self::assertSame($entityType, $object->getTuplizerType());
        self::assertSame($entityType, $object->getProxyInterfaceType());
        self::assertSame($prop, $object->getProperty('foobar'));
        self::assertType('Iterator', $object->getProperties());
    }
    
    public function testBasic2()
    {
        $column = new Xyster_Db_Column("test_property");
        $table = new Xyster_Db_Table("foobar");
        $type = new Xyster_Orm_Type_String;
        $value = new Xyster_Orm_Meta_Value_Basic($table, $type, array($column));
        $property = new Xyster_Type_Property_Direct("testProperty");
        $prop = new Xyster_Orm_Meta_Property("foobar", $value, $property, false, false);
        $entityType = new Xyster_Type('Xyster_Orm_Meta_EntityTest');
        $properties = array($prop);
        $object = new Xyster_Orm_Meta_Entity($entityType, $properties, $table);
        
        self::assertNull($object->getVersion());
        self::assertNull($object->getIdProperty());
        self::assertFalse($object->hasIdProperty());
        self::assertFalse($object->isVersioned());
        self::assertFalse($object->hasNaturalId());
        self::assertEquals(__CLASS__, $object->getClassName());
        self::assertSame($table, $object->getTable());
        self::assertNull($object->getWhere());
        self::assertFalse($object->isLazy());
        self::assertTrue($object->isMutable());
        self::assertSame($entityType, $object->getType());
        self::assertNull($object->getPersisterType());
        self::assertNull($object->getTuplizerType());
        self::assertNull($object->getProxyInterfaceType());
    }
    
    public function testGetProperty()
    {
        $column = new Xyster_Db_Column("test_property");
        $table = new Xyster_Db_Table("foobar");
        $type = new Xyster_Orm_Type_String;
        $value = new Xyster_Orm_Meta_Value_Basic($table, $type, array($column));
        $property = new Xyster_Type_Property_Direct("testProperty");
        $prop = new Xyster_Orm_Meta_Property("foobar", $value, $property, false, false);
        $entityType = new Xyster_Type('Xyster_Orm_Meta_EntityTest');
        $properties = array($prop);
        $object = new Xyster_Orm_Meta_Entity($entityType, $properties, $table);
        
        $this->setExpectedException('Xyster_Orm_Meta_Exception', 'Property not found: none');
        $object->getProperty('none');
    }
    
    public function testBadConstruct()
    {
        $table = new Xyster_Db_Table("foobar");
        $entityType = new Xyster_Type('Xyster_Orm_Meta_EntityTest');
        $properties = array(1);
        $this->setExpectedException('Xyster_Orm_Meta_Exception', 'Invalid property supplied');
        new Xyster_Orm_Meta_Entity($entityType, $properties, $table);
    }
}