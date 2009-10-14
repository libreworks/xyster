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
require_once 'Xyster/Orm/Type/Text.php';

/**
 * Test class for Xyster_Orm_Type_Text.
 */
class Xyster_Orm_Type_TextTest extends Xyster_Orm_Type_ScalarTestSupport
{
    /**
     * @var Xyster_Orm_Type_Text
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new Xyster_Orm_Type_Text;
    }
    
    public function testGetFetchType()
    {
        self::assertEquals(Zend_Db::PARAM_STR, $this->object->getFetchType());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Clob(), $this->object->getDataType());
    }
    
    public function testCopy()
    {
        self::assertSame("test", $this->object->copy("test"));
    }
    
    public function testGetName()
    {
        self::assertSame("text", $this->object->getName());
    }
    
    public function testGetReturnedType()
    {
        self::assertSame(Xyster_Type::string(), $this->object->getReturnedType());
    }
    
    public function testIsDirty()
    {
        self::assertTrue($this->object->isDirty("test", "test2", array(true)));
        self::assertFalse($this->object->isDirty("test", "test", array(true)));
    }
    
    public function testCompare()
    {
        self::assertLessThan(0, $this->object->compare("a", "b"));
        self::assertGreaterThan(0, $this->object->compare("b", "a"));
        self::assertEquals(0, $this->object->compare("a", "a"));
    }
    
    protected function _getFixture()
    {
        return $this->object;
    }
}