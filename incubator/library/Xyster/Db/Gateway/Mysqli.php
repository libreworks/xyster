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
 * @see Xyster_Db_Gateway_Pdo_Mysql
 */
require_once 'Xyster/Db/Gateway/Pdo/Mysql.php';
/**
 * A gateway and abstraction layer for MySQLi connections
 *
 * We'll worry about inheritance later.
 * 
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_Mysqli extends Xyster_Db_Gateway_Pdo_Mysql
{
    /**
     * Creates a new MySQLi DB gateway
     *
     * @param Zend_Db_Adapter_Mysqli $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Mysqli $db = null )
    {
        if ( $db !== null ) {
            $this->setAdapter($db);
        }
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Mysqli $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Mysqli $db )
    {
        $this->_setAdapter($db);
    }
}