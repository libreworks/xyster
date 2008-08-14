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
 * @see Xyster_Orm_Runtime_Property_Standard
 */
require_once 'Xyster/Orm/Runtime/Property/Standard.php';
/**
 * Runtime metamodel component information
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Runtime_ComponentMeta
{
    protected $_isKey;
    protected $_properties = array();
    protected $_propertySpan;
    protected $_propertyIndexes = array();
    protected $_role;
    /**
     * @var Xyster_Orm_Tuplizer_Component_Interface
     */
    protected $_tuplizer;
    
    /**
     * Creates a new componentmeta object
     *
     * @param Xyster_Orm_Mapping_Component $value
     */
    public function __construct( Xyster_Orm_Mapping_Component $value )
    {
        $this->_role = $value->getRoleName();
        $this->_isKey = $value->isKey();
        $this->_propertySpan = $value->getPropertySpan();
        foreach( $value->getProperties() as $prop ) {
            $this->_properties[] = Xyster_Orm_Runtime_Property_Standard::build($prop);
            $this->_propertyIndexes[$prop->getName()] = count($this->_propertyIndexes);
        }
    }
    
    /**
     * Gets the properties assigned to the component
     *
     * @return array of {@link Xyster_Orm_Runtime_Property} objects
     */
    public function getProperties()
    {
        return array() + $this->_properties;
    }
    
    /**
     * Gets a property by index
     *
     * @param int $index
     * @return Xyster_Orm_Runtime_Property
     */
    public function getProperty( $index )
    {
        if ( $index < 0 || $index > $this->_propertySpan ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Invalid index');
        }
        return $this->_properties[$index];
    }
    
    /**
     * Gets a property by name
     *
     * @param string $name
     * @return Xyster_Orm_Runtime_Property
     * @throws Xyster_Orm_Exception if the property was not found 
     */
    public function getPropertyByName( $name )
    {
        return $this->getProperty($this->getPropertyIndex($name));
    }
    
    /**
     * Gets the index of the property
     *
     * @param string $name
     * @return int
     * @throws Xyster_Orm_Exception if the property was not found 
     */
    public function getPropertyIndex( $name )
    {
        if ( !array_key_exists($name, $this->_propertyIndexes) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Property not found: ' . $name);
        }
        return $this->_propertyIndexes[$name];
    }
    
    /**
     * Gets the number of properties in the component
     *
     * @return int
     */
    public function getPropertySpan()
    {
        return $this->_propertySpan;
    }
    
    /**
     * Gets the tuplizer 
     *
     * @return Xyster_Orm_Tuplizer_Component_Interface
     */
    public function getTuplizer()
    {
        
    }
    
    /**
     * Whether the component is a key
     *
     * @return boolean
     */
    public function isKey()
    {
        return $this->_isKey;
    }
}