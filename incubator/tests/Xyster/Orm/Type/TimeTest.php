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
require_once 'Xyster/Orm/Type/Time.php';
require_once 'Xyster/Orm/ISession.php';

/**
 * Test class for Xyster_Orm_Type_Time.
 */
class Xyster_Orm_Type_TimeTest extends Xyster_Orm_Type_ScalarTestSupport
{
    /**
     * @var Xyster_Orm_Type_Time
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new Xyster_Orm_Type_Time;
    }
    
    public function testGetFetchType()
    {
        self::assertNull($this->object->getFetchType());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Time(), $this->object->getDataType());
    }
    
    public function testGetName()
    {
        self::assertSame("time", $this->object->getName());
    }
    
    public function testHasTranslate()
    {
        self::assertTrue($this->object->hasTranslate());
    }
    
    public function testGetReturnedType()
    {
        self::assertEquals(new Xyster_Type('Zend_Date'), $this->object->getReturnedType());
    }
    
    public function testIsDirty()
    {
        $date = new Zend_Date('11:59:45', Zend_Date::TIMES);
        $date2 = new Zend_Date('01:23:45', Zend_Date::TIMES);
        self::assertTrue($this->object->isDirty($date, $date2, array(true)));
        self::assertFalse($this->object->isDirty($date, $date, array(true)));
    }
    
    public function testCompare()
    {
        $date = new Zend_Date('11:59:45', Zend_Date::TIMES);
        $date2 = new Zend_Date('01:23:45', Zend_Date::TIMES);
        self::assertEquals(-1, $this->object->compare($date2, $date));
        self::assertEquals(0, $this->object->compare($date, $date));
        self::assertEquals(1, $this->object->compare($date, $date2));
    }
    
    public function testCopy()
    {
        $date = new Zend_Date('11:59:45', Zend_Date::TIMES);
        self::assertEquals($date, $this->object->copy($date));
    }

    public function testIsEqual()
    {
        $a = date('H:i:s');
        $b = new Zend_Date($a, Zend_Date::TIMES);
        self::assertTrue($this->object->isEqual($a, $b));
        self::assertFalse($this->object->isEqual($a, '-1 day'));
    }
    
    public function testTranslateFrom()
    {
        $date = new Zend_Date('11:59:45', Zend_Date::TIMES);
        $sess = $this->getMock('Xyster_Orm_ISession');
        $this->assertEquals($date, $this->object->translateFrom(array('11:59:45'), null, $sess));
    }
    
    public function testTranslateTo()
    {
        $date = '15:29:12';
        $date2 = new Zend_Date($date, Zend_Date::TIMES);
        $result = $this->object->translateTo($date, null, $this->getMock('Xyster_Orm_ISession'));
        $this->assertEquals(array($date2->get(Zend_Date::TIMES)), $result);
    }
    
    protected function _getFixture()
    {
        return $this->object;
    }
}