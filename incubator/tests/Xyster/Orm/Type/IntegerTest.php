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
require_once 'Xyster/Orm/Type/Integer.php';

/**
 * Test class for Xyster_Orm_Type_Integer.
 */
class Xyster_Orm_Type_IntegerTest extends Xyster_Orm_Type_ScalarTestSupport
{
    /**
     * @var Xyster_Orm_Type_Integer
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new Xyster_Orm_Type_Integer;
    }
    
    public function testGetFetchType()
    {
        self::assertEquals(Zend_Db::PARAM_INT, $this->object->getFetchType());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Integer(), $this->object->getDataType());
    }
    
    public function testCopy()
    {
        self::assertSame(4, $this->object->copy(4));
    }
    
    public function testGetName()
    {
        self::assertSame("integer", $this->object->getName());
    }
    
    public function testGetReturnedType()
    {
        self::assertSame(Xyster_Type::integer(), $this->object->getReturnedType());
    }
    
    public function testIsDirty()
    {
        self::assertTrue($this->object->isDirty(5, 6, array(true)));
        self::assertFalse($this->object->isDirty(6, 6, array(true)));
    }
    
    public function testCompare()
    {
        self::assertLessThan(0, $this->object->compare(1, 2));
        self::assertGreaterThan(0, $this->object->compare(2, 1));
        self::assertEquals(0, $this->object->compare(1, 1));
    }
    
    public function testGetComparator()
    {
        self::assertSame($this->object, $this->object->getComparator());
    }
    
    public function testInitial()
    {
        self::assertSame(0, $this->object->initial());
    }
    
    public function testNext()
    {
        self::assertSame(3, $this->object->next(2));
    }
    
    protected function _getFixture()
    {
        return $this->object;
    }
}