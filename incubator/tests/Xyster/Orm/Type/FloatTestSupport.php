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
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ScalarTestSupport.php';

/**
 * Abstract test class for scalar float types
 */
abstract class Xyster_Orm_Type_FloatTestSupport extends Xyster_Orm_Type_ScalarTestSupport
{
    const F_LT = 10000000000000000.000000001;
    const F_GT = 20000000000000000.200000002;
    
    public function testCompare()
    {
        self::assertLessThan(0, $this->object->compare(self::F_LT, self::F_GT));
        self::assertGreaterThan(0, $this->object->compare(self::F_GT, self::F_LT));
        self::assertEquals(0, $this->object->compare(self::F_LT, self::F_LT));
    }
    
    public function testGetFetchType()
    {
        self::assertEquals(null, $this->object->getFetchType());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Float(), $this->object->getDataType());
    }
    
    public function testCopy()
    {
        self::assertSame(self::F_LT, $this->object->copy(self::F_LT));
    }
    
    public function testGetName()
    {
        self::assertSame("float", $this->object->getName());
    }
    
    public function testGetReturnedType()
    {
        self::assertSame(Xyster_Type::double(), $this->object->getReturnedType());
    }
    
    public function testIsDirty()
    {
        self::assertTrue($this->object->isDirty(self::F_LT, self::F_GT, array(true)));
        self::assertFalse($this->object->isDirty(self::F_LT, self::F_LT, array(true)));
    }    
}