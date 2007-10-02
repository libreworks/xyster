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
 * Zend_Loader
 */
require_once 'Zend/Loader.php';
/**
 * A broker for Application Services
 *
 * @category  Xyster
 * @package   Xyster_Application
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Application_Service_Broker
{
    /**
     * @var Xyster_Application
     */
    protected $_application;

    /**
     * @var array
     */
    protected $_paths = array();
        
    /**
     * @var Xyster_Application_Service_Abstract[]
     */
    protected $_services = array();

    /**
     * Creates a new ServiceBroker
     *
     * @param Xyster_Application $application
     */
    public function __construct( Xyster_Application $application )
    {
        $this->_application = $application;
    }
    
    /**
     * Property overloader
     *
     * @param string $name
     * @magic
     * @return Xyster_Application_Service_Abstract
     */
    public function __get( $name )
    {
        return $this->getService($name);
    }
    
    /**
     * Add a path to Application Services
     *
     * @param string $path
     * @param string $prefix Optional; defaults to 'Xyster_Application_Service_'
     * @return Xyster_Application_Service_Broker provides a fluent interface
     */
    public function addPath( $path, $prefix = 'Xyster_Application_Service_' )
    {
        if ( substr($path, -1, 1) != DIRECTORY_SEPARATOR ) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $prefix = rtrim($prefix, '_') . '_';

        $this->_paths[] = array('dir'=>$path, 'prefix'=>$prefix);
        return $this;
    }
    
    /**
     * Add a class prefix for service layers
     *
     * @param string $prefix
     * @return Xyster_Application_Service_Broker provides a fluent interface
     */
    public function addPrefix( $prefix )
    {
        $prefix = rtrim($prefix, '_');
        $path = str_replace('_', DIRECTORY_SEPARATOR, $prefix);
        return $this->addPath($path, $prefix);
    }
    
	/**
     * Add an application service layer
     *
     * @param Xyster_Application_Service_Abstract $service
     * @return Xyster_Application_Service_Broker provides a fluent interface
     */
    public function addService( Xyster_Application_Service_Abstract $service )
    {
        $name = $service->getName();
        $this->_services[$name] = $service;
        return $this;
    }
    
	/**
     * Get a service by name
     *
     * @param string $name
     * @return Xyster_Application_Service_Abstract
     */
    public function getService( $name )
    {
        $name = self::_normalizeServiceName($name);

        if ( !array_key_exists($name, $this->_services) ) {
            $this->_services[$name] = $this->_loadService($name);
        }

        return $this->_services[$name];
    }
    
	/**
     * Check to see if a named service is in the broker
     *
     * @param string $name
     * @return boolean whether the service is loaded
     */
    public function hasService( $name )
    {
        $name = self::_normalizeServiceName($name);
        return array_key_exists($name, $this->_services);
    }

    /**
     * Remove a service from the broker
     *
     * @param string $name
     * @return boolean whether a service was removed
     */
    public function removeService( $name )
    {
        $name = self::_normalizeServiceName($name);
        if ( array_key_exists($name, $this->_services) ) {
            unset($this->_services[$name]);
            return true;
        }

        return false;
    }
    
    /**
     * Clears out any service layer objects
     *
     * @return Xyster_Application_Service_Broker provides a fluent interface
     */
    public function resetServices()
    {
        $this->_services = array();
        return $this;
    }
    
    /**
     * Loads a service
     *
     * @param string $name
     * @return Xyster_Application_Service_Abstract
     */
    protected function _loadService( $name )
    {
        $file = $name . '.php';

        foreach( $this->_paths as $info ) {
            $dir    = $info['dir'];
            $prefix = $info['prefix'];

            $class = $prefix . $name;
            
            if ( !class_exists($class, false) && Zend_Loader::isReadable($dir . $file) ) {
                include_once $dir . $file;
            }
            
            if ( class_exists($class, false) ) {
                $service = new $class();

                if (! $service instanceof Xyster_Application_Service_Abstract ) {
                    require_once 'Xyster/Application/Exception.php';
                    throw new Xyster_Application_Exception($class . ' is not an instance of Xyster_Application_Service_Abstract');
                }
                
                $service->setApplication($this->_application)
                    ->init();

                $this->_services[$service->getName()] = $service;
                return $service;
            }

        }

        require_once 'Xyster/Application/Exception.php';
        throw new Xyster_Application_Exception('Service not found: ' . $name);
    }
    
    /**
     * Normalize service name
     *
     * @param string $name
     * @return string the normalized name
     */
    static protected function _normalizeServiceName( $name )
    {
        if ( strpos($name, '_') !== false ) {
            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        }

        return ucfirst($name);
    }
}