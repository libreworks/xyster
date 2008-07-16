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
 * @see Xyster_Db_Schema_AbstractMysql
 */
require_once 'Xyster/Db/Schema/AbstractMysql.php';
/**
 * An abstraction layer for schema manipulation in MySQL (PDO version) 
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Schema_Pdo_Mysql extends Xyster_Db_Schema_AbstractMysql
{
    /**
     * Creates a new MySQL schema adapter
     *
     * @param Zend_Db_Adapter_Pdo_Mysql $db The database adapter to use
     */
    public function __construct( Zend_Db_Adapter_Pdo_Mysql $db = null )
    {
        parent::__construct($db);
    }
    
    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Pdo_Mysql $db The database adapter to use
     */
    public function setAdapter( Zend_Db_Adapter_Pdo_Mysql $db )
    {
        $this->_setAdapter($db);
    }
}