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
 * @see Xyster_Data_Binder_Setter_Interface
 */
require_once 'Xyster/Data/Binder/Setter/Interface.php';
/**
 * A binder setter for entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Binder_Setter implements Xyster_Data_Binder_Setter_Interface
{
    /**
     * @var Xyster_Orm_Entity_Type
     */
    protected $_type;
    
    /**
     * Creates a new entity binder 
     *
     * @param Xyster_Orm_Entity_Type $type The entity type for which the setter applies
     */
    public function __construct( Xyster_Orm_Entity_Type $type )
    {
        $this->_type = $type;
    }
    
    /**
     * Sets the value in the target 
     *
     * @param object $target An object
     * @param string $field The name of the field to set
     * @param mixed $value The value to set
     * @throws Xyster_Data_Binder_Setter_Exception if there was a problem setting
     */
    public function set($target, $field, $value)
    {
        if ( !$this->_type->isInstance($target) ) {
            require_once 'Xyster/Data/Binder/Setter/Exception.php';
            throw new Xyster_Data_Binder_Setter_Exception('Only ' . $this->_type . ' can be used with this setter');    
        }
        $target->$field = $value; // yay simple!
    }
}