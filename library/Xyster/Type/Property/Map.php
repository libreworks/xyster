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
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Type_Property_Interface
 */
require_once 'Xyster/Type/Property/Interface.php';
/**
 * A mediator for setting and getting values from a named field
 * 
 * This field mapper will work with arrays and ArrayAccess objects
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Type_Property_Map implements Xyster_Type_Property_Interface
{
    /**
     * The property name
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Creates a new field mapper
     *
     * @param string $name The name of the property
     */
    public function __construct( $name )
    {
        $this->_name = $name;
    }
    
    /**
     * Gets the value in the field of the target
     *
     * @param mixed $target
     * @return mixed
     */
    public function get( $target )
    {
        return $target[$this->_name];
    }
    
    /**
     * Sets the value in the field of the target
     *
     * @param mixed $target
     * @param mixed $value
     */
    public function set( $target, $value )
    {
        $target[$this->_name] = $value;
    }
}