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
 * @see Xyster_Type_Property_Interface
 */
require_once 'Xyster/Type/Property/Interface.php';
/**
 * A binder setter for entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Binder_Setter implements Xyster_Type_Property_Interface
{
    /**
     * @var Xyster_Orm_Entity_Type
     */
    protected $_type;
    
    protected $_field;
    
    /**
     * Creates a new entity binder 
     *
     * @param Xyster_Orm_Entity_Type $type The entity type for which the setter applies
     */
    public function __construct( Xyster_Orm_Entity_Type $type, $field )
    {
        $this->_type = $type;
    }
    
    public function get($target)
    {
        $name = $this->_field;
        return $target->$name;
    }
    
    /**
     * Sets the value in the target 
     *
     * @param object $target An object
     * @param mixed $value The value to set
     * @throws Xyster_Data_Binder_Exception if there was a problem setting
     */
    public function set($target, $value)
    {
        if ( !$this->_type->isInstance($target) ) {
            require_once 'Xyster/Data/Binder/Exception.php';
            throw new Xyster_Data_Binder_Exception('Only ' . $this->_type . ' can be used with this setter');
        }
        $field = $this->_field;
        $target->$field = $value; // yay simple!
    }
}