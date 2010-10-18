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
namespace XysterTest\Data\Symbol;
use Xyster\Data\Symbol\AggregateField;
/**
 * Test for Xyster_Data_Field
 *
 */
class AggregateFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AggregateField
     */
    protected $_commonField;
     
    public function setUp()
    {
        $this->_commonField = \Xyster\Data\Symbol\Field::count('userid', 'users');
    }
    
    public function testToString()
    {
        $this->assertEquals($this->_commonField->getFunction()->getValue() . '(' . 
            $this->_commonField->getName() . ')', (string)$this->_commonField);
    }
}