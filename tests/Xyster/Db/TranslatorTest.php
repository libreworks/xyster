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
 * @subpackage Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Db;
use Xyster\Db\Translator;
use Xyster\Data\Symbol\Field;
use Xyster\Data\Symbol\Junction;
/**
 * Test for Xyster_Db_Translator
 *
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Translator
     */
    public $translator;
    
    /**
     * Sets up the test
     *
     */
    protected function setUp()
    {
        $this->translator = new Translator('"');
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
     * @expectedException \Xyster\Db\Exception
     */
    public function testSetRenameCallbackInvalid()
    {
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
     * Tests the 'translate' method with a clause
     *
     */
    public function testTranslateClause()
    {
    	$clause = new \Xyster\Data\Symbol\SortClause;
        $field = Field::named('testing');
        $sort = $field->desc();
        $clause->add($sort);
        $field2 = Field::named('testing2');
        $sort2 = $field2->asc();
        $clause->add($sort2);
        
        $token = $this->translator->translate($clause);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('"testing" DESC, "testing2" ASC', $token->getSql());
        
        $token = $this->translator->translate($clause, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('testing DESC, testing2 ASC', $token->getSql());
    }
    
    /**
     * The same test as 'testTranslateJunction' but another method
     *
     */
    public function testTranslateClauseJunction()
    {
    	$field = Field::named('testing');
        $field2 = Field::named('unitTesting');
        $junction = Junction::any($field->neq('foo'), $field->neq(null))
            ->add($field->neq($field2));
        
        $token = $this->translator->translateClause($junction);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertRegExp('/\( "testing" <> \? OR "testing" IS NOT NULL OR "testing" <> "unitTesting" \)/', $token->getSql());
        
        $token = $this->translator->translateClause($junction, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertRegExp('/\( testing <> \? OR testing IS NOT NULL OR testing <> unitTesting \)/', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with a field
     *
     */
    public function testTranslateField()
    {
        $field = Field::named('testing');
        $token = $this->translator->translate($field);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('"testing"', $token->getSql());
        
        $token = $this->translator->translate($field, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
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
        $field = Field::named('testing');
        
        $token = $this->translator->translate($field);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('my_table."TESTING"', $token->getSql());
        
        $token = $this->translator->translate($field, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
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
        $field = Field::max('testing');
        
        $token = $this->translator->translate($field);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('MAX(my_table."TESTING")', $token->getSql());
        
        $token = $this->translator->translate($field, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('MAX(my_table.TESTING)', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with a sort
     *
     */
    public function testTranslateSort()
    {
        $field = Field::named('testing');
        $sort = $field->desc();
        
        $token = $this->translator->translate($sort);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('"testing" DESC', $token->getSql());
        
        $token = $this->translator->translate($sort, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertEquals('testing DESC', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with a junction
     *
     */
    public function testTranslateJunction()
    {
        $field = Field::named('testing');
        $field2 = Field::named('unitTesting');
        $junction = Junction::any($field->neq('foo'), $field->neq(null))
            ->add($field->neq($field2));
        
        $token = $this->translator->translate($junction);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertRegExp('/\( "testing" <> \? OR "testing" IS NOT NULL OR "testing" <> "unitTesting" \)/', $token->getSql());
        
        $token = $this->translator->translate($junction, false);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertRegExp('/\( testing <> \? OR testing IS NOT NULL OR testing <> unitTesting \)/', $token->getSql());
    }
    
    /**
     * Tests the 'translate' method with an Expression
     *
     */
    public function testTranslateExpression()
    {
        $field = Field::named('testing');
        $exp = $field->between('A', 'M');
        
        $token = $this->translator->translate($exp);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertRegExp('/"testing" BETWEEN \? AND \?/', $token->getSql());
        
        $exp2 = $field->in(array('A', 'B', 'C', 'D'));
        
        $token = $this->translator->translate($exp2);
        
        $this->assertType('\Xyster\Db\Token', $token);
        $this->assertRegExp('/"testing" IN \((\?,){3}\?\)/', $token->getSql());
    }
}
