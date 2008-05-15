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
 * @see Zend_Date
 */
require_once 'Zend/Date.php';
/**
 * Property lookups for date values
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Entity_Lookup_Date extends Xyster_Orm_Entity_Lookup_Abstract
{
    /**
     * The entity date field name to use for the lookup
     * @var string
     */
    protected $_field;
    
    /**
     * The date output format
     * @var mixed
     */
    protected $_format = Zend_Date::ISO_8601;
    
    /**
     * The current locale
     * @var Zend_Locale
     */
    protected $_locale;
    
    /**
     * @var Xyster_Type
     */
    private static $_type;
    
    /**
     * Creates a new date lookup
     *
     * The default field name for this lookup will be the field name provided 
     * appended with 'Date' (ex. 'createdOn' will yield 'createdOnDate').  This
     * field name should not already exist
     * 
     * @param string $field The name of the field on the entity
     * @param string $name Optional. The name of this lookup field
     */
    public function __construct( Xyster_Orm_Entity_Type $type, $field, $name = null )
    {
        $this->_field = $field;
        if ( $name === null ) {
            $name = $field . 'Date';
        }
        parent::__construct($type, $name);
    }
    
    /**
     * Gets the assigned locale
     *
     * @return Zend_Locale or null if none is specified
     */
    public function getLocale()
    {
        return $this->_locale;
    }
    
    /**
     * Gets the type of object or value returned by this lookup
     *
     * @return Xyster_Type The value type
     */
    public function getType()
    {
        if ( !self::$_type ) {
            self::$_type = new Xyster_Type('Zend_Date');
        }
        return self::$_type;
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
            return ( $entity->{$this->_field} ) ? 
                new Zend_Date($entity->{$this->_field}, null, $this->_locale) :
                null;
        } catch ( Zend_Date_Exception $thrown ) {
            require_once 'Xyster/Orm/Entity/Exception.php';
            throw new Xyster_Orm_Entity_Exception('Invalid date format: ' . $thrown->getMessage());
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
            /* @var $value Zend_Date */
            $entity->{$this->_field} = $value->get($this->_format);
        }
    }
    
    /**
     * Sets the current locale
     *
     * @param Zend_Locale $locale
     */
    public function setLocale( Zend_Locale $locale )
    {
        $this->_locale = $locale;
    }
}