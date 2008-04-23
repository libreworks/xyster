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
 * An abstract builder
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_Gateway_Builder
{
    /**
     * @var string
     */
    private $_name;
    
    /**
     * @var Xyster_Db_Gateway_Abstract
     */
    private $_gateway;
    
    /**
     * @var string
     */
    private $_schema;
    
    /**
     * Creates a new builder
     *
     * @param Xyster_Db_Gateway_Abstract $gateway
     * @param string $name The object name
     * @param string $schema Optional. The schema name of the object 
     */
    public function __construct( Xyster_Db_Gateway_Abstract $gateway, $name, $schema=null )
    {
        $this->_name = $name;
        $this->_gateway = $gateway;
        $this->_schema = $schema;
    }
    
    /**
     * Gets the name of the object
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the schema of the object
     * 
     * @return string 
     */
    public function getSchema()
    {
        return $this->_schema;
    }
    
    /**
     * Gets the gateway
     *
     * @return Xyster_Db_Gateway_Abstract
     */
    protected function _getGateway()
    {
        return $this->_gateway;
    }
       
    /**
     * Executes the builder
     *
     */
    abstract public function execute();
}