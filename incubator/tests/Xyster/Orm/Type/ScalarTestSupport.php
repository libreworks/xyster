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

/**
 * Abstract test class for scalar types
 */
abstract class Xyster_Orm_Type_ScalarTestSupport extends PHPUnit_Framework_TestCase
{
    public function testHasTranslate()
    {
        $object = $this->_getFixture();
        self::assertFalse($object->hasTranslate());
    }
    
    public function testCommonPolymorphism()
    {
        $object = $this->_getFixture();
        self::assertFalse($object->isAssociation());
        self::assertFalse($object->isCollection());
        self::assertFalse($object->isComponent());
        self::assertFalse($object->isEntityType());
        self::assertEquals(($object instanceof Xyster_Orm_Type_Mutable), $object->isMutable());
    }
    
    public function testGetColumnSpan()
    {
        $object = $this->_getFixture();
        self::assertEquals(1, $object->getColumnSpan());
    }

    public function testGetDataTypes()
    {
        $object = $this->_getFixture();
        self::assertEquals(array($object->getDataType()), $object->getDataTypes());
    }
    
    public function testGetFetchTypes()
    {
        $object = $this->_getFixture();
        self::assertEquals(array($object->getFetchType()), $object->getFetchTypes());
    }
    
    public function testToColumnNullness()
    {
        $object = $this->_getFixture();
        self::assertEquals(array(false), $object->toColumnNullness(null));
        self::assertEquals(array(true), $object->toColumnNullness(false));
        self::assertEquals(array(true), $object->toColumnNullness("aoeu"));
    }
    
    /**
     * @return Xyster_Orm_Type_AbstractScalar
     */
    abstract protected function _getFixture();
}