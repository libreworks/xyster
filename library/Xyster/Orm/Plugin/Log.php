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
 * @see Xyster_Orm_Plugin_Abstract
 */
require_once 'Xyster/Orm/Plugin/Abstract.php';
/**
 * @see Zend_Auth
 */
require_once 'Zend/Auth.php';
/**
 * An ORM plugin for an audit trail
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Plugin_Log extends Xyster_Orm_Plugin_Abstract
{
    /**
     * @var Zend_Log
     */
    protected $_log;
    
    protected $_useAuth;
    
    /**
     * Creates a new log plugin
     *
     * @param Zend_Log $log The log to use
     * @param boolean $useAuth Enables the use of Zend_Auth to look up username
     */
    public function __construct( Zend_Log $log, $useAuth = true )
    {
        $this->_log = $log;
        $this->_useAuth = $useAuth;
    }
    
    /**
     * Called after an entity is deleted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postDelete( Xyster_Orm_Entity $entity )
    {
        $this->_logMessage($entity, 'DELETED');
    }
    
    /**
     * Called after an entity is inserted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postInsert( Xyster_Orm_Entity $entity )
    {
        $string = '';
        $first = true;
        foreach( $entity->toArray() as $name => $value ) {
            if ( !$first ) {
                $string .= ',';
            }
            $string .= $name . '=' . $value;
            $first = false;
        }
        $this->_logMessage($entity, 'INSERTED', $string);
    }
    
    /**
     * Called prior to an entity being updated
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preUpdate( Xyster_Orm_Entity $entity )
    {
        $updates = array();
        $current = $entity->toArray();
        foreach( $entity->getBase() as $name => $value ) {
            if ( $value != $current[$name] ) {
                $updates[] = $name . ': ' . $value . '=>' . $current[$name];
            }
        }
        $this->_logMessage($entity, 'UPDATED', implode('; ', $updates));
    }
    
    /**
     * Logs an event to the logger
     *
     * @param Xyster_Orm_Entity $entity
     * @param string $event
     * @param string $details
     */
    protected function _logMessage( Xyster_Orm_Entity $entity, $event, $details = '' )
    {
        $message = '';
        if ( $this->_useAuth ) {
            // if we have an identity, it's "username verbed $entity"
            $message .= Zend_Auth::getInstance()->getIdentity() . ' ' .
                $event . ' ';
        }
        $message .= get_class($entity) . '(' .
            $entity->getPrimaryKeyAsString(true) . ')';
        if ( !$this->_useAuth ) {
            // otherwise it's "$entity was verbed"
            $message .= ' was ' . $event;
        }
        if ( $details ) {
            $message .= ' [' . $details . ']';
        }
        $this->_log->log($message, Zend_Log::INFO);
    }
}