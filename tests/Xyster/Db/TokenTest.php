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
use Xyster\Db\Token;
/**
 * Test for Xyster_Db_Token
 *
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests basic operation of the class
     *
     */
    public function testBasic()
    {
        $sql = 'WHERE somethingCool = :value';
        $bind = array('value'=>'Coffee');
        
        $token = new Token($sql, $bind);
        $this->assertEquals($sql, $token->getSql());
        $this->assertEquals($bind, $token->getBindValues());
    }
    
    /**
     * Tests the addBindValues method
     *
     */
    public function testAddBindValues()
    {
        $sql = 'WHERE somethingCool = :value';
        $bind = array('value'=>'Coffee');
        $token = new Token($sql, $bind);
        
        $sql2 = 'WHERE somethingCool = :value AND somethingBogus = :value2';
        $bind2 = array('value2'=>'Decaf');
        $token2 = new Token($sql2, $bind2);
        
        $token2->addBindValues($token);
        
        $combined = array_merge($bind, $bind2);
        $this->assertEquals($combined, $token2->getBindValues());
    }
}
