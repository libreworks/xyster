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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Acl;
use Xyster\Acl\Acl;
/**
 * Test for Xyster_Acl
 *
 */
class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Acl
     */
    protected $_acl;
    
    /**
     * Sets up the test suite
     *
     */
    protected function setUp()
    {
        $this->_acl = new Acl();
    }
    
    /**
     * Tests adding an authorizer to the ACL works as expected
     *
     */
    public function testAddAuthorizer()
    {
        $authorizer = new AclTest_Authorizer();
        $return = $this->_acl->addAuthorizer($authorizer);
        $this->_acl->add(new \Zend_Acl_Resource('test'));
        
        $this->assertType('\Xyster\Acl\Acl', $return);
        $this->assertSame($authorizer, $this->_acl->getAuthorizer('test'));
    }
    
    /**
     * Tests the 'assertAllowed' method
     *
     */
    public function testAssertAllowedSuccess()
    {
        $role = new \Zend_Acl_Role('doublecompile');
        $resource = new \Zend_Acl_Resource('free time');
        
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
        $role = new \Zend_Acl_Role('doublecompile');
        $resource = new \Zend_Acl_Resource('free time');
        
        $this->_acl->addRole($role)
            ->add($resource)
            ->deny($role, $resource);
        
        try {
            $this->_acl->assertAllowed($role, $resource, 'anything');
            $this->fail('Exception not thrown');
        } catch ( \Zend_Acl_Exception $thrown ) {
            $this->assertEquals('Insufficient permissions: doublecompile -> free time (anything)', $thrown->getMessage());
        }
    }
    
    /**
     * Tests isAllowed with authorizer
     *
     */
    public function testIsAllowedWithAuthorizerSuccess()
    {
        $authorizer = new AclTest_Authorizer();
        $role = new \Zend_Acl_Role('doublecompile');
        $resource = new \Zend_Acl_Resource('coffee');
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
        $authorizer = new AclTest_Authorizer();
        $role = new \Zend_Acl_Role('rspeed');
        $resource = new \Zend_Acl_Resource('coffee');
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
 * A stub authorizer for unit testing
 *
 */
class AclTest_Authorizer implements \Xyster\Acl\IAuthorizer
{
    protected $_called = 0;
     
	public function applies( \Zend_Acl_Resource_Interface $resource )
	{
	    return true;
	}
	
	public function isAllowed( \Zend_Acl_Role_Interface $role = null, \Zend_Acl_Resource_Interface $resource = null, $privilege = null )
	{
	    ++$this->_called;
	    return $role instanceof \Zend_Acl_Role && $role->getRoleId() == 'doublecompile';
	}
}
