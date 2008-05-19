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
// Call Xyster_Orm_Entity_Lookup_EnumTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_Entity_Lookup_EnumTest::main');
}
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestSetup.php';
require_once 'PHPUnit/Framework.php';
require_once 'Xyster/Orm/Entity/Lookup/Enum.php';
require_once 'Xyster/Data/Aggregate.php';
/**
 * Test class for Xyster_Orm_Entity_Lookup_Enum.
 * Generated by PHPUnit on 2008-05-14 at 20:20:42.
 */
class Xyster_Orm_Entity_Lookup_EnumTest extends Xyster_Orm_TestSetup
{
    /**
     * @var    Xyster_Orm_Entity_Lookup_Enum
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Orm_Entity_Lookup_EnumTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    public function setUp()
    {
        $mf = $this->_mockFactory();
        $this->object = new Xyster_Orm_Entity_Lookup_Enum($mf->getEntityType('MockBug'),
            new Xyster_Type('Xyster_Data_Aggregate'), 'bugDescription');
    }

    /**
     * Tests the 'getType' method
     */
    public function testGetType()
    {
        $type = $this->object->getType();
        $this->assertType('Xyster_Type', $type);
        $this->assertEquals('Xyster_Data_Aggregate', $type->getName());
    }

    /**
     * Tests the 'get' method
     */
    public function testGet()
    {
        $entity = $this->_getMockEntity();
        $entity->bugDescription = 'MAX';
        $agg = $this->object->get($entity);
        $this->assertType('Xyster_Data_Aggregate', $agg);
        $this->assertSame(Xyster_Data_Aggregate::Maximum(), $agg);
        
        $entity->bugDescription = null;
        $this->assertNull($this->object->get($entity));
    }

    /**
     * Tests the 'set' method
     */
    public function testSet()
    {
        $entity = $this->_getMockEntity();
        $enum = Xyster_Data_Aggregate::Count();
        $this->object->set($entity, $enum);
        $this->assertEquals('COUNT', $entity->bugDescription);
        
        $this->object->set($entity, null);
        $this->assertNull($entity->bugDescription);
    }
    
    /**
     * Tests passing an invalid entity type to the constructor
     *
     */
    public function testBadConstruct()
    {
        $mf = $this->_mockFactory();
        $type = $mf->getEntityType('MockBug');
        $this->setExpectedException('Xyster_Orm_Entity_Exception');
        $object = new Xyster_Orm_Entity_Lookup_Enum($type, $type, 'bugDescription');
    }
}

// Call Xyster_Orm_Entity_Lookup_EnumTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_Entity_Lookup_EnumTest::main') {
    Xyster_Orm_Entity_Lookup_EnumTest::main();
}
