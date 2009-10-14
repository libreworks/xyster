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
require_once 'Xyster/Orm/Type/Timestamp.php';
require_once 'Xyster/Orm/ISession.php';

/**
 * Test class for Xyster_Orm_Type_Timestamp.
 */
class Xyster_Orm_Type_TimestampTest extends Xyster_Orm_Type_ScalarTestSupport
{
    /**
     * @var Xyster_Orm_Type_Timestamp
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new Xyster_Orm_Type_Timestamp;
    }
    
    public function testGetFetchType()
    {
        self::assertNull($this->object->getFetchType());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Timestamp(), $this->object->getDataType());
    }
    
    public function testGetName()
    {
        self::assertSame("timestamp", $this->object->getName());
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
        $date = new Zend_Date('2008-07-27T11:59:45+05:00');
        $date2 = new Zend_Date('2008-07-27T01:23:45+05:00');
        self::assertTrue($this->object->isDirty($date, $date2, array(true)));
        self::assertFalse($this->object->isDirty($date, $date, array(true)));
    }
    
    public function testCompare()
    {
        $date = new Zend_Date('2008-07-27T11:59:45+05:00');
        $date2 = new Zend_Date('2008-07-27T01:23:45+05:00');
        self::assertEquals(-1, $this->object->compare($date2, $date));
        self::assertEquals(0, $this->object->compare($date, $date));
        self::assertEquals(1, $this->object->compare($date, $date2));
    }
    
    public function testCopy()
    {
        $date = new Zend_Date('2008-07-27T11:59:45+05:00');
        self::assertEquals($date, $this->object->copy($date));
    }

    public function testIsEqual()
    {
        $a = date('Y-m-d H:i:s');
        $b = new Zend_Date($a);
        self::assertTrue($this->object->isEqual($a, $b));
        self::assertFalse($this->object->isEqual($a, '-2 days'));
    }
    
    public function testTranslateFrom()
    {
        $timestamp = '2008-07-27T11:59:45+05:00';
        $date = new Zend_Date($timestamp);
        $sess = $this->getMock('Xyster_Orm_ISession');
        $this->assertEquals($date, $this->object->translateFrom(array($timestamp), null, $sess));
    }
    
    public function testTranslateTo()
    {
        $date = '2009-10-14T15:29:12+05:00';
        $date2 = new Zend_Date($date);
        $result = $this->object->translateTo($date, null, $this->getMock('Xyster_Orm_ISession'));
        $this->assertEquals(array($date2->get(Zend_Date::ISO_8601)), $result);
    }
    
    public function testGetComparator()
    {
        self::assertSame($this->object, $this->object->getComparator());
    }
    
    public function testInitial()
    {
        self::assertType('Zend_Date', $this->object->initial());
    }
    
    public function testNext()
    {
        self::assertType('Zend_Date', $this->object->next(2));
    }
    
    protected function _getFixture()
    {
        return $this->object;
    }
}