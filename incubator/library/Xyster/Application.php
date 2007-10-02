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
 * @package   Xyster_Application
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Application_Service_Broker
 */
require_once 'Xyster/Application/Service/Broker.php';
/**
 * Zend_Config
 */
require_once 'Zend/Config.php';
/**
 * An application layer
 *
 * @category  Xyster
 * @package   Xyster_Application
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Application
{
    /**
     * @var Xyster_Application_ServiceBroker
     */
    protected $_broker;
    
    /**
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * @var Xyster_Application
     */
    static protected $_instance;
    
    /**
     * Creates a new Xyster_Application object
     *
     */
    protected function __construct()
    {
        $this->_broker = new Xyster_Application_Service_Broker($this); 
    }
    
    /**
     * Gets the singleton instance
     *
     * @return Xyster_Application
     */
    static public function getInstance()
    {
        if ( !self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Gets the configuration settings
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * Gets an application service
     *
     * @param string $name
     * @return Xyster_Application_Service_Abstract
     */
    public function getService( $name )
    {
        return $this->_broker->getService($name);
    }
    
    /**
     * Gets the service broker
     *
     * @return Xyster_Application_Service_Broker
     */
    public function getServiceBroker()
    {
        return $this->_broker;
    }
    
    /**
     * Sets the configuration for the application
     *
     * @param Zend_Config|array $config Either a Zend_Config object or an array 
     * @return Xyster_Application provides a fluent interface
     */
    public function setConfig( $config )
    {
        if ( $config instanceof Zend_Config ) {
            $this->_config = $config;            
        } else if ( is_array($config) ) {
            $this->_config = new Zend_Config($config);
        } else {
            require_once 'Xyster/Application/Exception.php';
            throw new Xyster_Application_Exception('Invalid configuration type');
        }
        return $this;
    }
}