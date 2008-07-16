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
 * @see Xyster_Db_Constraint
 */
require_once 'Xyster/Db/Constraint.php';
/**
 * @see Xyster_Db_ReferentialAction
 */
require_once 'Xyster/Db/ReferentialAction.php';
/**
 * A foreign key constraint
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_ForeignKey extends Xyster_Db_Constraint
{
    protected $_onDelete;
    protected $_onUpdate;
    protected $_fcolumns = array();
    protected $_fnames = array();
    protected $_ftable;
    
    /**
     * Adds a column to the constraint if it's not already contained
     *
     * @param Xyster_Db_Column $column The column
     * @return Xyster_Db_ForeignKey provides a fluent interface
     */
    public function addReferencedColumn( Xyster_Db_Column $column )
    {
        $name = strtolower($column->getName());
        if ( !in_array($name, $this->_fnames) ) {
            $this->_fcolumns[] = $column;
            $this->_fnames[] = $name;
        }
        return $this;
    }
            
    /**
     * Gets the onDelete behavior
     *
     * @return Xyster_Db_ReferentialAction
     */
    public function getOnDelete()
    {
        return $this->_onDelete;
    }
    
    /**
     * Gets the onUpdate behavior
     *
     * @return Xyster_Db_ReferentialAction
     */
    public function getOnUpdate()
    {
        return $this->_onUpdate;
    }
    
    /**
     * Gets the referenced columns
     *
     * @return array of {@link Xyster_Db_Column} objects
     */
    public function getReferencedColumns()
    {
        return array_values($this->_fcolumns);
    }
    
    /**
     * Gets the foreign table
     *
     * @return Xyster_Db_Table The referenced table
     */
    public function getReferencedTable()
    {
        return $this->_ftable;
    }
    
    /**
     * Sets the onDelete behavior
     *
     * @param Xyster_Db_ReferentialAction $action
     * @return Xyster_Db_ForeignKey provides a fluent interface
     */
    public function setOnDelete( Xyster_Db_ReferentialAction $action )
    {
        $this->_onDelete = $action;
        return $this;
    }
    
    /**
     * Sets the onUpdate behavior
     *
     * @param Xyster_Db_ReferentialAction $action
     * @return Xyster_Db_ForeignKey provides a fluent interface
     */
    public function setOnUpdate( Xyster_Db_ReferentialAction $action )
    {
        $this->_onUpdate = $action;
        return $this;
    }
    
    /**
     * Sets the referenced table
     *
     * @param Xyster_Db_Table $table
     * @return Xyster_Db_ForeignKey provides a fluent interface
     */
    public function setReferencedTable( Xyster_Db_Table $table )
    {
        $this->_ftable = $table;
        return $this;
    }
}