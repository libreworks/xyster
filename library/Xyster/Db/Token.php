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
 * Token containing a SQL fragment and bind values 
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Token
{
    /**
     * The SQL fragment
     *
     * @var string
     */
    protected $_sql;

    /**
     * The bind values, if any
     *
     * @var array
     */
    protected $_bind = array();

    /**
     * Creates a new SqlToken
     *
     * @param string $sql
     * @param array $bind
     */
    public function __construct( $sql, array $bind = array() )
    {
        $this->_sql = $sql;
        $this->_bind = $bind;
    }

    /**
     * Gets the SQL text
     *
     * @return string
     */
    public function getSql()
    {
        return $this->_sql;
    }
    
    /**
     * Gets the bind values for the SQL text (if any)
     *
     * @return array
     */
    public function getBindValues()
    {
        return $this->_bind;
    }
    
    /**
     * Merges the bind values from another token
     * 
     * If keys are the same, values in this token will be overwritten.
     *
     * @param Xyster_Db_Token $token
     */
    public function addBindValues( Xyster_Db_Token $token )
    {
        $this->_bind = array_merge($this->_bind, $token->_bind);
    }
}