<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   UnitTests
 * @subpackage Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
// Call Xyster_Db_TranslatorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_TranslatorTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Xyster_Db_Translator
 */
require_once 'Xyster/Db/Translator.php';
require_once 'Zend/Db/Adapter/Static.php';
require_once 'Xyster/Data/Field.php';
require_once 'Xyster/Data/Junction.php';

/**
 * Test for Xyster_Db_Translator
 *
 */
class Xyster_Db_TranslatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Db_Translator
     */
    public $translator;
    
    /**
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_TranslatorTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Sets up the test
     *
     */
    protected function setUp()
    {
        $adapter = new Zend_Db_Adapter_Static(array('dbname'=>':memory:'));
        $this->translator = new Xyster_Db_Translator($adapter);     
    }

    /**
     * A callback function for the translator
     *
     * @param string $field
     * @return string
     */
    public function renameCallback( $field )
    {
        return strtoupper($field);
    }
    
    /**
     * Tests the 'setRenameCallback' method
     *
     */
    public function testSetRenameCallback()
    {
        $callback = array($this, 'renameCallback');
        $return = $this->translator->setRenameCallback($callback);
        $this->assertSame($this->translator, $return);
        $this->assertAttributeSame($callback, '_renameCallback', $this->translator);
    }
    
    /**
     * Tests the 'setRenameCallback' method with an invalid argument
     *
     */
    public function testSetRenameCallbackInvalid()
    {
        $this->setExpectedException('Xyster_Db_Exception');
        $this->translator->setRenameCallback('123doesntExist');
    }
    
    /**
     * Tests the 'setTable' method
     *
     */
    public function testSetTable()
    {
        $return = $this->translator->setTable('my_table');
        $this->assertSame($this->translator, $return);
        $this->assertAttributeEquals('my_table', '_table', $this->translator);
    }
    
    /**
     * Tests the 'translate' method with an invalid parameter
     *
     */
    public function testTranslateInvalid()
    {
        $this->setExpectedException('Xyster_Db_Exception');
        $this->translator->translate('invalid object');
    }

    /**
     * Tests the 'translate' method with a field
     *
     */
    public function testTranslateField()
    {
        $field = Xyster_Data_Field::named('testing');
        $token = $this->translator->translate($field);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('"testing"', $token->getSql());
        
        $token = $this->translator->translate($field, false);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('testing', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with a field and a rename callback
     *
     */
    public function testTranslateFieldRename()
    {
        $this->translator->setRenameCallback(array($this, 'renameCallback'));
        $this->translator->setTable('my_table');
        $field = Xyster_Data_Field::named('testing');
        
        $token = $this->translator->translate($field);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('my_table."TESTING"', $token->getSql());
        
        $token = $this->translator->translate($field, false);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('my_table.TESTING', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with an aggregate field
     *
     */
    public function testTranslateFieldAggregate()
    {
        $this->translator->setRenameCallback(array($this, 'renameCallback'));
        $this->translator->setTable('my_table');
        $field = Xyster_Data_Field::max('testing');
        
        $token = $this->translator->translate($field);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('MAX(my_table."TESTING")', $token->getSql());
        
        $token = $this->translator->translate($field, false);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('MAX(my_table.TESTING)', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with a sort
     *
     */
    public function testTranslateSort()
    {
        $field = Xyster_Data_Field::named('testing');
        $sort = $field->desc();
        
        $token = $this->translator->translate($sort);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('"testing" DESC', $token->getSql());
        
        $token = $this->translator->translate($sort, false);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertEquals('testing DESC', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with a junction
     *
     */
    public function testTranslateJunction()
    {
        $field = Xyster_Data_Field::named('testing');
        $field2 = Xyster_Data_Field::named('unitTesting');
        $junction = Xyster_Data_Junction::any($field->neq('foo'), $field->neq(null))
            ->add($field->neq($field2));
        
        $token = $this->translator->translate($junction);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertRegExp('/\( "testing" <> :P[\S]+ OR "testing" IS NOT NULL OR "testing" <> "unitTesting" \)/', $token->getSql());
        
        $token = $this->translator->translate($junction, false);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertRegExp('/\( testing <> :P[\S]+ OR testing IS NOT NULL OR testing <> unitTesting \)/', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with an Expression
     *
     */
    public function testTranslateExpression()
    {
        $field = Xyster_Data_Field::named('testing');
        $exp = $field->between('A', 'M');
        
        $token = $this->translator->translate($exp);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertRegExp('/"testing" BETWEEN :P([\S]+)1 AND :P(?:\1)+2/', $token->getSql());
        
        $exp2 = $field->in(array('A', 'B', 'C', 'D'));
        
        $token = $this->translator->translate($exp2);
        
        $this->assertType('Xyster_Db_Token', $token);
        $this->assertRegExp('/"testing" IN \((:P[\S]+\d,){3}:P[\S]+\d\)/', $token->getSql());
    }
}

// Call Xyster_Db_TranslatorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_TranslatorTest::main') {
    Xyster_Db_TranslatorTest::main();
}
