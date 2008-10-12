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
 * A session with the ORM layer
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Session_Interface
{
    /**
     * Gets the context associated with the session
     * 
     * @return Xyster_Orm_Context_Interface
     */
    function getContext();
    
    /**
     * Gets the identifier of the entity or null if not associated
     * 
     * @param object $entity
     * @return mixed
     */
    function getContextEntityId($entity);
    
    /**
     * Gets the entity persister for the entity name given
     * 
     * @param string $entityName
     * @param object $entity
     * @return Xyster_Orm_Persister_Entity_Interface
     */
    function getEntityPersister($entityName, $entity);
    
    /**
     * Gets the session factory that created this session
     * 
     * @return Xyster_Orm_Session_Factory_Interface
     */
    function getFactory();
    
    /**
     * Gets the interceptor in use by the session
     * 
     * @return Xyster_Orm_Session_Interceptor_Interface
     */
    function getInterceptor();

    /**
     * Initialize the collection (if not initialized)
     * 
     * @param Xyster_Orm_Collection_Interface $collection
     * @param boolean $writing
     */
    function initCollection(Xyster_Orm_Collection_Interface $collection, $writing);
}