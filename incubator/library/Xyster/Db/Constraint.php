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
 * @see Xyster_Db_ColumnOwner
 */
require_once 'Xyster/Db/ColumnOwner.php';
/**
 * A relational database constraint
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Db_Constraint extends Xyster_Db_ColumnOwner
{
    /**
     * @var Xyster_Db_Table
     */
    protected $_table;
    
    /**
     * Gets the table related to this constraint
     *
     * @return Xyster_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     * Sets the table related to this constraint 
     *
     * @param Xyster_Db_Table $table
     * @return Xyster_Db_Constraint provides a fluent interface
     */
    public function setTable( Xyster_Db_Table $table )
    {
        $this->_table = $table;
        return $this;
    }
}