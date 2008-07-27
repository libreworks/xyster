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
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Xyster/Db/Statement/Stub.php';

/**
 * Common class for php float-based types
 */
class Xyster_Orm_Type_TestFloatCommon extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the 'getReturnedType' method
     */
    public function testGetReturnedType()
    {
        $type = $this->object->getReturnedType();
        $this->assertType('Xyster_Type', $type);
        $this->assertEquals('double', $type->getName());
        $this->assertSame($type, $this->object->getReturnedType());
    }

    /**
     * Tests the 'set' method
     */
    public function testSet()
    {
        $stmt = new Xyster_Db_Statement_Stub;
        $sess = $this->getMock('Xyster_Orm_Session_Interface');
        $this->object->set($stmt, 24.06, 0, $sess, array(true));
        $this->assertEquals(24.06, $stmt->values[0]);
    }
}