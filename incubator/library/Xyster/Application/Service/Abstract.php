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
 * @package   Xyster_Application
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * An abstract Service Layer
 * 
 * Randy Stafford describes a Service Layer as "[establishing] a set of
 * available operations and [coordinating] the application's response in each
 * operation."
 * {@link http://martinfowler.com/eaaCatalog/serviceLayer.html}
 *
 * @category  Xyster
 * @package   Xyster_Application
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Application_Service_Abstract
{
    /**
     * @var Xyster_Application
     */
    protected $_application;
    
    /**
     * Called after the {@link Xyster_Application_ServiceBroker} creates it
     * 
     * If you instantiate a service yourself, this method will not be called 
     * automatically.
     */
    public function init()
    {
    }
    
    /**
     * Gets the application to which this service applies
     *
     * @return Xyster_Application
     */
    public function getApplication()
    {
        return $this->_application;
    }
    
    /**
     * Gets the name of this service
     *
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);

        return ( strpos($className, '_') !== false ) ?
            ltrim(strrchr($className, '_'), '_') : $className;
    }
    
    /**
     * Sets the application
     *
     * @param Xyster_Application $application
     * @return Xyster_Application_Service_Abstract provides a fluent interface
     */
    public function setApplication( Xyster_Application $application )
    {
        $this->_application = $application;
        return $this;
    }
}