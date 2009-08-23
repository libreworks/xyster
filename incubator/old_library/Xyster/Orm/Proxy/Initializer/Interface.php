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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Fetches the underlying entity for a proxy
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Proxy_Initializer_Interface
{
    /**
     * Gets the entity name 
     * 
     * @return string
     */
    function getEntityName();
    
    /**
     * Gets the identifier held by the proxy
     * 
     * @return mixed
     */
    function getId();
    
    /**
     * Gets the underlying object (initializes if necessary)
     * 
     * @param $session Optional. Uses the session to find the persistent object.
     * @return object
     */
    function getImplementation(Xyster_Orm_Session_Interface $session = null);
    
    /**
     * Gets the actual entity class
     * 
     * @return Xyster_Type
     */
    function getMappedType();
    
    /**
     * Gets the session (if the proxy is attached)
     * 
     * @return Xyster_Orm_Session_Interface
     */
    function getSession();
    
    /**
     * Initializes the proxy (fetches the target entity if necessary)
     * 
     * @throws Xyster_Orm_Exception if an error occurs
     */
    function initialize();
    
    /**
     * Whether the proxy has not been initialized yet
     * 
     * @return boolean
     */
    function isUninitialized();
    
    /**
     * Whether the proxy is unwrapped
     * 
     * @return boolean
     */
    function isUnwrap();
    
    /**
     * Sets the identifier held by the proxy
     * 
     * @param mixed $id
     */
    function setId($id);
    
    /**
     * Initializes the proxy manually
     * @param object $target
     */
    function setImplementation($target);
    
    /**
     * Sets the session to which the proxy is attached
     * 
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    function setSession(Xyster_Orm_Session_Interface $session);
    
    /**
     * Sets whether the proxy is unwrapped
     *
     * @param boolean $flag 
     */
    function setUnwrapped($flag);
}