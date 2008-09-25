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
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Type_Property_Method implements Xyster_Type_Property_Interface
{
    /**
     * The getter name
     *
     * @var string
     */
    protected $_getName;
    
    /**
     * The setter name
     *
     * @var string
     */
    protected $_setName;
    
    /**
     * Creates a new field mapper
     *
     * @param string $name The name of the property
     */
    public function __construct( $name )
    {
        $ucname = ucfirst($name);
        $this->_getName = 'get' . $ucname;
        $this->_setName = 'set' . $ucname;
    }
    
    /**
     * Gets the value in the field of the target
     *
     * @param mixed $target
     * @return mixed
     */
    public function get( $target )
    {
        $methodName = $this->_getName;
        return $target->$methodName();
    }
    
    /**
     * Sets the value in the field of the target
     *
     * @param mixed $target
     * @param mixed $value
     */
    public function set( $target, $value )
    {
        $methodName = $this->_setName;
        $target->$methodName($value);
    }
}