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
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'FloatTestSupport.php';
require_once 'Xyster/Orm/Type/BigInteger.php';

/**
 * Test class for Xyster_Orm_Type_BigInteger.
 */
class Xyster_Orm_Type_BigIntegerTest extends Xyster_Orm_Type_FloatTestSupport
{
    /**
     * @var Xyster_Orm_Type_BigInteger
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new Xyster_Orm_Type_BigInteger;
    }

    public function testGetName()
    {
        self::assertSame("big-integer", $this->object->getName());
    }
    
    public function testGetDataType()
    {
        self::assertEquals(Xyster_Db_DataType::Bigint(), $this->object->getDataType());
    }
    
    protected function _getFixture()
    {
        return $this->object;
    }
}