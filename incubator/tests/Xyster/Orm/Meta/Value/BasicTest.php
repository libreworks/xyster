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
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Orm/Type/String.php';
require_once 'Xyster/Db/Column.php';
require_once 'Xyster/Db/Table.php';
require_once 'Xyster/Orm/Meta/Value/Basic.php';

/**
 * Test class for Xyster_Orm_Meta_Value_Basic.
 */
class Xyster_Orm_Meta_Value_BasicTest extends PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $column = new Xyster_Db_Column("user_name");
        $column->setNullable(false);
        $table = new Xyster_Db_Table("user");
        $type = new Xyster_Orm_Type_String;
        $object = new Xyster_Orm_Meta_Value_Basic($table, $type, array($column));
        
        self::assertType('Iterator', $object->getColumns());
        self::assertEquals(1, $object->getColumnSpan());
        self::assertSame($type, $object->getType());
        self::assertFalse($object->isNullable());
        self::assertSame($table, $object->getTable());
    }
}