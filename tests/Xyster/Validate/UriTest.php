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
 * @subpackage Xyster_Validate
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Validate;
use Xyster\Validate\Uri;
/**
 * Test class for Xyster_Validate_Uri.
 * Generated by PHPUnit on 2007-09-11 at 19:28:58.
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Uri
     */
    public $validator;

    /**
     * Sets up the fixture
     * 
     */
    protected function setUp()
    {
        $this->validator = new Uri();
    }
    
    /**
     * Tests basic validation for a URI
     *
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('', false),
            array('://xyster.devweblog.org', false),
            array('!@#$%^&*()', false),
            array('unsupported', false),
            array('unsupported://devweblog.org', false),
            array('http://xyster.devweblog.org', true),
            array('https://testing.devweblog.org:443/someDirectory/ItsOkay%20ToHave/', true)
            );
        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->validator->isValid($element[0]));
        }
    }
    
    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }
    
}
