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
 * @subpackage Xyster_Acl
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Call Xyster_AclTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_AclTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Xyster_Acl
 */
require_once 'Xyster/Acl.php';
require_once 'Zend/Acl/Resource.php';
require_once 'Zend/Acl/Role.php';
/**
 * Test for Xyster_Acl
 *
 */
class Xyster_AclTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Xyster_Acl
     */
    protected $_acl;
    
    /**
     * Runs the test methods of this class.
     *
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Xyster_AclTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Sets up the test suite
     *
     */
    protected function setUp()
    {
        $this->_acl = new Xyster_Acl();
    }
    
    /**
     * Tests adding an authorizer to the ACL works as expected
     *
     */
    public function testAddAuthorizer()
    {
        $authorizer = new Xyster_AclTest_Authorizer();
        $return = $this->_acl->addAuthorizer($authorizer);
        $this->_acl->add(new Zend_Acl_Resource('test'));
        
        $this->assertType('Xyster_Acl', $return);
        $this->assertSame($authorizer, $this->_acl->getAuthorizer('test'));
    }
    
    /**
     * Tests the 'assertAllowed' method
     *
     */
    public function testAssertAllowedSuccess()
    {
        $role = new Zend_Acl_Role('doublecompile');
        $resource = new Zend_Acl_Resource('free time');
        
        $this->_acl->addRole($role)
            ->add($resource)
            ->allow($role, $resource);
        
        $this->_acl->assertAllowed($role, $resource, 'anything');
    }
    
    /**
     * Tests the 'assertAllowed' method
     *
     */
    public function testAssertAllowedFailure()
    {
        $role = new Zend_Acl_Role('doublecompile');
        $resource = new Zend_Acl_Resource('free time');
        
        $this->_acl->addRole($role)
            ->add($resource)
            ->deny($role, $resource);
        
        try {
            $this->_acl->assertAllowed($role, $resource, 'anything');
            $this->fail('Exception not thrown');
        } catch ( Zend_Acl_Exception $thrown ) {
            $this->assertEquals('Insufficient permissions: doublecompile -> free time (anything)', $thrown->getMessage());
        }
    }
    
    /**
     * Tests isAllowed with authorizer
     *
     */
    public function testIsAllowedWithAuthorizerSuccess()
    {
        $authorizer = new Xyster_AclTest_Authorizer();
        $role = new Zend_Acl_Role('doublecompile');
        $resource = new Zend_Acl_Resource('coffee');
        $this->_acl->addAuthorizer($authorizer)
            ->add($resource)
            ->addRole($role);
        
        $return = $this->_acl->isAllowed($role, $resource);
        
        $this->assertTrue($return);
        
        $return = $this->_acl->isAllowed($role, $resource);
        
        $this->assertTrue($return);
        $this->assertAttributeEquals(1, '_called', $authorizer);
    }
    
    /**
     * Tests the 'isAllowed' method with an authorizer that denies access
     *
     */
    public function testIsAllowedWithAuthorizerFailure()
    {
        $authorizer = new Xyster_AclTest_Authorizer();
        $role = new Zend_Acl_Role('rspeed');
        $resource = new Zend_Acl_Resource('coffee');
        $this->_acl->addAuthorizer($authorizer)
            ->add($resource)
            ->addRole($role);
        
        $return = $this->_acl->isAllowed($role, $resource);
        
        $this->assertFalse($return);
        
        $return = $this->_acl->isAllowed($role, $resource);
        
        $this->assertFalse($return);
        $this->assertAttributeEquals(1, '_called', $authorizer);
    }
}

/**
 * @see Xyster_Acl_Authorizer_Interface
 */
require_once 'Xyster/Acl/Authorizer/Interface.php';
/**
 * A stub authorizer for unit testing
 *
 */
class Xyster_AclTest_Authorizer implements Xyster_Acl_Authorizer_Interface
{
    protected $_called = 0;
     
	public function applies( Zend_Acl_Resource_Interface $resource )
	{
	    return true;
	}
	
	public function isAllowed( Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null )
	{
	    ++$this->_called;
	    return $role instanceof Zend_Acl_Role && $role->getRoleId() == 'doublecompile';
	}
}

// Call Xyster_AclTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_AclTest::main') {
    Xyster_AclTest::main();
}
