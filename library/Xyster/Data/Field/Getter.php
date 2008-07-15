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
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * An object that pulls the values out of an object or array given a field
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Field_Getter
{
    /**
     * @var string
     */
    protected $_name;
    
    /**
     * Creates a new field getter
     *
     * @param string $name  The field name
     */
    public function __construct( $name )
    {
        if ( $name instanceof Xyster_Data_Field ) {
            $name = $name->getName();
        }
        $this->_name = (string)$name;
    }
    
    /**
     * Gets the named value out of an array or object
     *
     * @param mixed $object An array or object
     * @return mixed
     */
    public function evaluate( $object )
    {
        $value = null;
        if ( is_array($object) || $object instanceof ArrayAccess ) {
            if ( !isset($object[$this->_name]) && !array_key_exists($this->_name, $object) ) {
                require_once 'Xyster/Data/Field/Exception.php';
                throw new Xyster_Data_Field_Exception("Field name '{$this->_name}' is invalid");
            }
            $value = $object[$this->_name];
        } else if ( is_object($object) && method_exists($object, 'get'.ucfirst($this->_name)) ) {
            // JavaBean-style getter
            $method = 'get'.ucfirst($this->_name);
            $value = $object->$method();
        } else if ( is_object($object) && preg_match('/^[a-z_]\w*$/i', $this->_name) ) {
            // name of a real property or one caught by __get()
            $value = $object->{$this->_name};
        } else if ( is_object($object) ) {
            // this is eval'ed becuse $this->_name might be a method call 
            // maybe sometime in the future we can do this better...
            eval("\$value = \$object->{$this->_name};");
        } else {
            require_once 'Xyster/Data/Field/Exception.php';
            throw new Xyster_Data_Field_Exception("Only objects or arrays can be evaluated");
        }
        return $value;
    }
    
    /**
     * A shortcut instead of creating a new getter
     *  
     * @param mixed $object An array or object
     * @param string $field The field name (or an object that will toString) 
     * @return mixed
     */
    static public function get( $object, $field )
    {
        if ( $object instanceof Xyster_Data_Set && $field instanceof Xyster_Data_Field_Aggregate ) {
            return $object->aggregate($field);
        }
        
        $getter = new self($field);
        return $getter->evaluate($object);
    }
}