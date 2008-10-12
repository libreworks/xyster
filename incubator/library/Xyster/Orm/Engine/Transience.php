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
 * Logic related to transient entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Engine_Transience
{
    private function __construct() {}

    /**
     * Whether the instance is persistent or detached
     * 
     * @param string $entityName
     * @param object $entity
     * @param boolean $assumed
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    public static function isNotTransient($entityName, $entity, $assumed, Xyster_Orm_Session_Interface $session)
    {
        if ( $entity instanceof Xyster_Orm_Proxy_Interface ||
            $session->getContext()->isEntryFor($entity) ) {
            return true;
        }
        return !self::isTransient($entityName, $entity, $assumed, $session);
    }
    
    /**
     * Whether the instance transient
     * 
     * @param string $entityName
     * @param object $entity
     * @param boolean $assumed
     * @param Xyster_Orm_Session_Interface $session
     * @return boolean
     */
    public static function isTransient($entityName, $entity, $assumed, Xyster_Orm_Session_Interface $session)
    {
        if ( Xyster_Orm_Helper::isUnfetchedProperty($entity) ) {
            return false;
        }
        
        $unsaved = $session->getInterceptor()->isTransient($entity);
        if ( $unsaved != null ) {
            return $unsaved;
        }
        $persister = $session->getEntityPersister($entityName, $entity);
        $unsaved = $persister->isTransient($entity, $session);
        if ( $unsaved != null ) {
            return $unsaved;
        }
        if ( $assumed != null ) {
            return $assumed;
        }
        $snapshot = $session->getContext()->getDatabaseSnapshot($persister->getId($entity), $persister);
        return ($snapshot == null);
    }
    
    /**
     * Gets the identifier of the object or throw an exception if not saved
     * 
     * @param string $entityName
     * @param object $object
     * @param Xyster_Orm_Session_Interface $session
     * @return mixed
     * @throws Xyster_Orm_Exception if the entity is unsaved
     */
    public static function getEntityIdIfSaved($entityName, $object, Xyster_Orm_Session_Interface $session)
    {
        if ( $object == null ) {
            return null;
        } else {
            $id = $session->getContextEntityId($object);
            if ( $id === null ) {
                if ( self::isTransient($entityName, $object, false, $session) ) {
                    require_once 'Xyster/Orm/Exception.php';
                    throw new Xyster_Orm_Exception('Object is transient. Save before flushing: ' .
                        ($entinyName == null ?
                            $session->guessEntityName($object) : $entityName));
                }
                $id = $session->getEntityPersister($entityName, $entity)
                    ->getId($entity);
            }
            return $id;
        }
    }
}