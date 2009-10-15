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
require_once 'Xyster/Orm/Meta/EntityBuilder.php';
require_once 'Xyster/Orm/Meta/Property.php';
require_once 'Xyster/Orm/Meta/IdProperty.php';
require_once 'Xyster/Orm/Id/IGenerator.php';
require_once 'Xyster/Orm/Type/String.php';
require_once 'Xyster/Db/Column.php';
require_once 'Xyster/Db/Table.php';
require_once 'Xyster/Orm/Meta/Value/Basic.php';
require_once 'Xyster/Type/Property/Direct.php';

/**
 * Test class for Xyster_Orm_Meta_EntityBuilder.
 */
class Xyster_Orm_Meta_EntityBuilderTest extends PHPUnit_Framework_TestCase
{
    protected $type;
    protected $table;
    /**
     * @var Xyster_Orm_Meta_EntityBuilder
     */
    protected $object;
    
    protected function setUp()
    {
        $this->type = new Xyster_Type(__CLASS__);
        $this->table = new Xyster_Db_Table("user");
        $this->object = new Xyster_Orm_Meta_EntityBuilder($this->type, $this->table);
    }
    
    public function testBasic1()
    {
        $proxyType = new Xyster_Type(__CLASS__);
        $tuplizerType = new Xyster_Type(__CLASS__);
        $persisterType = new Xyster_Type(__CLASS__);
        $where = "version > -1";
        $column = new Xyster_Db_Column("test_property");
        $type = new Xyster_Orm_Type_String;
        $value = new Xyster_Orm_Meta_Value_Basic($this->table, $type, array($column));
        $property = new Xyster_Type_Property_Direct("testProperty");
        $prop = new Xyster_Orm_Meta_Property("testProperty", $value, $property, true, true);
        $idCol = new Xyster_Db_Column('entity_id');
        $idValue = new Xyster_Orm_Meta_Value_Basic($this->table, $type, array($idCol));
        $idProp = new Xyster_Orm_Meta_IdProperty("id", $idValue, new Xyster_Type_Property_Direct('id'),
            $this->getMock('Xyster_Orm_Id_IGenerator'));
        self::assertSame($this->object, $this->object->setLazy(true));
        self::assertSame($this->object, $this->object->setTuplizerType($tuplizerType));
        self::assertSame($this->object, $this->object->setProxyInterfaceType($proxyType));
        self::assertSame($this->object, $this->object->setPersisterType($persisterType));
        self::assertSame($this->object, $this->object->setWhere($where));
        self::assertSame($this->object, $this->object->addProperty($prop));
        self::assertSame($this->object, $this->object->setIdProperty($idProp));
        
        $entity = $this->object->build();
        self::assertType('Xyster_Orm_Meta_Entity', $entity);
        self::assertSame($idProp, $entity->getIdProperty());
        self::assertNull($entity->getVersion());
        self::assertTrue($entity->hasIdProperty());
        self::assertFalse($entity->isVersioned());
        self::assertTrue($entity->hasNaturalId());
        self::assertEquals(__CLASS__, $entity->getClassName());
        self::assertSame($this->table, $entity->getTable());
        self::assertEquals('version > -1', $entity->getWhere());
        self::assertTrue($entity->isLazy());
        self::assertTrue($entity->isMutable());
        self::assertSame($this->type, $entity->getType());
        self::assertSame($persisterType, $entity->getPersisterType());
        self::assertSame($tuplizerType, $entity->getTuplizerType());
        self::assertSame($proxyType, $entity->getProxyInterfaceType());
        self::assertSame($prop, $entity->getProperty('testProperty'));
    }
    
    public function testBasic2()
    {
        $where = "version > -1";
        $column = new Xyster_Db_Column("test_property");
        $type = new Xyster_Orm_Type_String;
        $value = new Xyster_Orm_Meta_Value_Basic($this->table, $type, array($column));
        $property = new Xyster_Type_Property_Direct("testProperty");
        $prop = new Xyster_Orm_Meta_Property("testProperty", $value, $property, false, false);
        $idCol = new Xyster_Db_Column('entity_id');
        $idValue = new Xyster_Orm_Meta_Value_Basic($this->table, $type, array($idCol));
        $idProp = new Xyster_Orm_Meta_IdProperty("id", $idValue, new Xyster_Type_Property_Direct('id'),
            $this->getMock('Xyster_Orm_Id_IGenerator'));
        self::assertSame($this->object, $this->object->setMutable(false));
        self::assertSame($this->object, $this->object->setWhere($where));
        self::assertSame($this->object, $this->object->addProperty($prop));
        self::assertSame($this->object, $this->object->setVersion($idProp));
        
        $entity = $this->object->build();
        self::assertType('Xyster_Orm_Meta_Entity', $entity);
        self::assertNull($entity->getIdProperty());
        self::assertSame($idProp, $entity->getVersion());
        self::assertFalse($entity->hasIdProperty());
        self::assertTrue($entity->isVersioned());
        self::assertFalse($entity->hasNaturalId());
        self::assertEquals(__CLASS__, $entity->getClassName());
        self::assertSame($this->table, $entity->getTable());
        self::assertEquals('version > -1', $entity->getWhere());
        self::assertFalse($entity->isLazy());
        self::assertFalse($entity->isMutable());
        self::assertSame($this->type, $entity->getType());
        self::assertSame($prop, $entity->getProperty('testProperty'));
    }    
}