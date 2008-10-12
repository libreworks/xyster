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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Orm_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Xyster/Orm/Context/CollectionEntryTest.php';
require_once 'Xyster/Orm/Context/CollectionKeyTest.php';
require_once 'Xyster/Orm/Context/EntityEntryTest.php';
require_once 'Xyster/Orm/Context/EntityKeyTest.php';
require_once 'Xyster/Orm/Context/EntityUniqueKeyTest.php';
require_once 'Xyster/Orm/Engine/ForeignKeyDirectionTest.php';
require_once 'Xyster/Orm/Engine/IdentifierValueTest.php';
require_once 'Xyster/Orm/Engine/StatusTest.php';
require_once 'Xyster/Orm/Engine/ValueInclusionTest.php';
require_once 'Xyster/Orm/Engine/VersioningTest.php';
require_once 'Xyster/Orm/Engine/VersionValueTest.php';
require_once 'Xyster/Orm/Mapping/ComponentTest.php';
require_once 'Xyster/Orm/Mapping/GenerationTest.php';
require_once 'Xyster/Orm/Mapping/PropertyTest.php';
require_once 'Xyster/Orm/Mapping/ValueTest.php';
require_once 'Xyster/Orm/Runtime/ComponentMetaTest.php';
require_once 'Xyster/Orm/Runtime/EntityMetaTest.php';
require_once 'Xyster/Orm/Runtime/Property/IdentifierTest.php';
require_once 'Xyster/Orm/Runtime/Property/StandardTest.php';
require_once 'Xyster/Orm/Runtime/Property/VersionTest.php';
require_once 'Xyster/Orm/Tuplizer/ComponentTest.php';
require_once 'Xyster/Orm/Tuplizer/EntityTest.php';
require_once 'Xyster/Orm/Type/AbstractTest.php';
require_once 'Xyster/Orm/Type/BigIntegerTest.php';
require_once 'Xyster/Orm/Type/BooleanTest.php';
require_once 'Xyster/Orm/Type/Boolean/TrueFalseTest.php';
require_once 'Xyster/Orm/Type/Boolean/YesNoTest.php';
require_once 'Xyster/Orm/Type/ComponentTest.php';
require_once 'Xyster/Orm/Type/DateTest.php';
require_once 'Xyster/Orm/Type/DecimalTest.php';
require_once 'Xyster/Orm/Type/FloatTest.php';
require_once 'Xyster/Orm/Type/IntegerTest.php';
require_once 'Xyster/Orm/Type/NullableTest.php';
require_once 'Xyster/Orm/Type/RealTest.php';
require_once 'Xyster/Orm/Type/StringTest.php';
require_once 'Xyster/Orm/Type/TextTest.php';
require_once 'Xyster/Orm/Type/TimeTest.php';
require_once 'Xyster/Orm/Type/TimestampTest.php';

error_reporting(E_ALL | E_STRICT);

class Xyster_Orm_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Xyster Framework - Xyster_Orm');
        $suite->addTestSuite('Xyster_Orm_Context_CollectionEntryTest');
        $suite->addTestSuite('Xyster_Orm_Context_CollectionKeyTest');
        $suite->addTestSuite('Xyster_Orm_Context_EntityEntryTest');
        $suite->addTestSuite('Xyster_Orm_Context_EntityKeyTest');
        $suite->addTestSuite('Xyster_Orm_Context_EntityUniqueKeyTest');
        $suite->addTestSuite('Xyster_Orm_Engine_ForeignKeyDirectionTest');
        $suite->addTestSuite('Xyster_Orm_Engine_IdentifierValueTest');
        $suite->addTestSuite('Xyster_Orm_Engine_ValueInclusionTest');
        $suite->addTestSuite('Xyster_Orm_Engine_StatusTest');
        $suite->addTestSuite('Xyster_Orm_Engine_VersioningTest');
        $suite->addTestSuite('Xyster_Orm_Engine_VersionValueTest');
        $suite->addTestSuite('Xyster_Orm_Mapping_ComponentTest');
        $suite->addTestSuite('Xyster_Orm_Mapping_GenerationTest');
        $suite->addTestSuite('Xyster_Orm_Mapping_PropertyTest');
        $suite->addTestSuite('Xyster_Orm_Mapping_ValueTest');
        $suite->addTestSuite('Xyster_Orm_Runtime_ComponentMetaTest');
        $suite->addTestSuite('Xyster_Orm_Runtime_EntityMetaTest');
        $suite->addTestSuite('Xyster_Orm_Runtime_Property_IdentifierTest');
        $suite->addTestSuite('Xyster_Orm_Runtime_Property_StandardTest');
        $suite->addTestSuite('Xyster_Orm_Runtime_Property_VersionTest');
        $suite->addTestSuite('Xyster_Orm_Tuplizer_ComponentTest');
        $suite->addTestSuite('Xyster_Orm_Tuplizer_EntityTest');
        $suite->addTestSuite('Xyster_Orm_Type_AbstractTest');
        $suite->addTestSuite('Xyster_Orm_Type_BigIntegerTest');
        $suite->addTestSuite('Xyster_Orm_Type_BooleanTest');
        $suite->addTestSuite('Xyster_Orm_Type_Boolean_TrueFalseTest');
        $suite->addTestSuite('Xyster_Orm_Type_Boolean_YesNoTest');
        $suite->addTestSuite('Xyster_Orm_Type_ComponentTest');
        $suite->addTestSuite('Xyster_Orm_Type_DateTest');
        $suite->addTestSuite('Xyster_Orm_Type_DecimalTest');
        $suite->addTestSuite('Xyster_Orm_Type_FloatTest');
        $suite->addTestSuite('Xyster_Orm_Type_IntegerTest');
        $suite->addTestSuite('Xyster_Orm_Type_NullableTest');
        $suite->addTestSuite('Xyster_Orm_Type_RealTest');
        $suite->addTestSuite('Xyster_Orm_Type_StringTest');
        $suite->addTestSuite('Xyster_Orm_Type_TextTest');
        $suite->addTestSuite('Xyster_Orm_Type_TimeTest');
        $suite->addTestSuite('Xyster_Orm_Type_TimestampTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Xyster_Orm_AllTests::main') {
    Xyster_Orm_AllTests::main();
}
