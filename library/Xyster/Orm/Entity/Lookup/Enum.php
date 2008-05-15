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
 * @see Xyster_Orm_Entity_Lookup_Abstract
 */
require_once 'Xyster/Orm/Entity/Lookup/Abstract.php';
/**
 * @see Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Property lookups for enum values
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity_Lookup_Enum extends Xyster_Orm_Entity_Lookup_Abstract
{
    /**
     * The entity field name to use for the lookup
     * @var string
     */
    protected $_field;
    
    /**
     * True to use the value, false to use the name
     * @var boolean
     */
    protected $_value = true;
    
    /**
     * @var Xyster_Type
     */
    protected $_type;
    
    /**
     * Creates a new enum lookup
     *
     * The default field name for this lookup will be the field name provided 
     * appended with 'Type' (ex. 'status' will yield 'statusType').  This
     * field name should not already exist.
     * 
     * @param Xyster_Orm_Entity_Type $type The entity type supported
     * @param Xyster_Type $enum The enum type to generate
     * @param string $field The name of the field on the entity
     * @param string $value True to use the value of the enum, false to use the name
     * @param string $name Optional. The name of this lookup field
     * @throws Xyster_Orm_Entity_Exception if the type provided for $enum isn't a subclass of Xyster_Enum
     */
    public function __construct( Xyster_Orm_Entity_Type $type, Xyster_Type $enum, $field, $value = true, $name = null )
    {
        $this->_field = $field;
        if ( !$enum->getClass()->isSubclassOf('Xyster_Enum') ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('Type for $enum must be a subclass of Xyster_Enum');
        }
        $this->_type = $enum;
        if ( $name === null ) {
            $name = $field . 'Enum';
        }
        parent::__construct($type, $name);
    }
        
    /**
     * Gets the type of object or value returned by this lookup
     *
     * @return Xyster_Type The value type
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Gets the lookup value for the entity given
     *
     * @param Xyster_Orm_Entity $entity
     * @return mixed The lookup value or object
     */
    public function get( Xyster_Orm_Entity $entity )
    {
        $this->_checkEntity($entity);
        try {
            $methodName = $this->_value ? 'valueOf' : 'parse';
            $value = $entity->{$this->_field};
            return ( $value ) ?
                Xyster_Enum::$methodName($this->getType()->getName(), $value) :
                null;
        } catch ( Exception $thrown ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('Invalid value for ' . $this->getType());
        }
    }
    
    /**
     * Sets any fields affected by changing the value of this lookup
     * 
     * @param Xyster_Orm_Entity $entity
     * @param mixed $value The new value for the lookup
     * @throws Xyster_Orm_Entity_Exception if the value is invalid
     */
    public function set( Xyster_Orm_Entity $entity, $value )
    {
        if ( $value === null ) { 
            $entity->{$this->_field} = null;
        } else {
            $this->_checkSet($entity, $value);
            $entity->{$this->_field} = $this->_value ?
                $value->getValue() : $value->getName();
        }
    }
}