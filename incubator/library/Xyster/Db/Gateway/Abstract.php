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
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * A gateway and abstraction layer for database administration
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_Gateway_Abstract
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db = null;
    
    /**
     * Creates a new DB gateway
     * 
     * This constructor should be overloaded with one that has the concrete
     * type hint.
     *
     * @param Zend_Db_Adapter_Abstract $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Abstract $db = null )
    {
    	$this->_db = $db;
    }
    
    /**
     * Gets the database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
    	return $this->_db;
    }
    
    /**
     * Sets the database adapter
     * 
     * Each class should implement a public setAdapter method with a type hint
     * for the exact type of adapter expected.  For instance, the
     * Xyster_Db_Gateway_Pdo_Pgsql class will want a Zend_Db_Adapter_Pdo_Pgsql
     * for its adapter. 
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @return Xyster_Db_Gateway_Abstract provides a fluent interface
     */
    protected function _setAdapter( Zend_Db_Adapter_Abstract $db )
    {
    	$this->_db = $db;
    	return $this;
    }
}