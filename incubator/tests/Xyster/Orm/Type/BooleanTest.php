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
require_once 'Xyster/Orm/Type/Boolean.php';

/**
 * Test class for Xyster_Orm_Type_Boolean.
 */
class Xyster_Orm_Type_BooleanTest extends Xyster_Orm_Type_ScalarTestSupport
{
    /**
     * @var Xyster_Orm_Type_Boolean
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new Xyster_Orm_Type_Boolean;
    }
    
    public function testGetFetchType()
    {
        self::assertEquals(Zend_Db::PARAM_BOOL, $this->object->getFetchType());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Boolean(), $this->object->getDataType());
    }
    
    public function testCopy()
    {
        self::assertSame(true, $this->object->copy(true));
    }
    
    public function testGetName()
    {
        self::assertSame("boolean", $this->object->getName());
    }
    
    public function testGetReturnedType()
    {
        self::assertSame(Xyster_Type::boolean(), $this->object->getReturnedType());
    }
    
    public function testIsDirty()
    {
        self::assertTrue($this->object->isDirty(true, false, array(true)));
        self::assertFalse($this->object->isDirty(true, true, array(true)));
    }
    
    public function testCompare()
    {
        self::assertLessThan(0, $this->object->compare(false, true));
        self::assertGreaterThan(0, $this->object->compare(true, false));
        self::assertEquals(0, $this->object->compare(false, false));
    }
    
    protected function _getFixture()
    {
        return $this->object;
    }
}