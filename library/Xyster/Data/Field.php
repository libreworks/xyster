<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * A simple concept for data fields and columns
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Field
{
    /**
     * The name of the field
     *
     * @var string
     */
    protected $_name;
    /**
     * An alias for the field, equal to the field name by default
     *
     * @var string
     */
    protected $_alias;

    /**
     * Creates a new Field
     *
     * @param string $name  The field name (be it a property, column, whatever)
     * @param string $alias  The alias for this field
     */
    protected function __construct( $name, $alias=null )
    {
        $this->_name = trim($name);
        if ( !strlen($this->_name) ) {
            require_once 'Xyster/Data/Field/Exception.php';
            throw new Xyster_Data_Field_Exception('A field name cannot be empty');
        }
        $this->_alias = ( !strlen(trim($alias)) ) ? $name : trim($alias);
    }

    /**
     * Gets the name of this field
     *
     * @return string The field name
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * Gets the alias assigned to this field
     *
     * @return string The field alias
     */
    public function getAlias()
    {
        return $this->_alias;
    }
    /**
     * Sets the alias assigned to this column
     *
     * @param string $alias  The alias to assign
     * @throws Xyster_Data_Field_Exception if the alias is invalid
     */
    public function setAlias( $alias )
    {
        if ( !strlen(trim($alias)) ) {
            require_once 'Xyster/Data/Field/Exception.php';
            throw new Xyster_Data_Field_Exception('An alias cannot be empty');
        }
        $this->_alias = trim($alias);
    }
    /**
     * Evaluates the reference for the given object
     *
     * @param mixed $object
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
     * String representation of this object
     * 
     * @magic
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
    /**
     * Factories an ascending {@link Xyster_Data_Sort} for this field name
     *
     * @return Xyster_Data_Sort
     */
    public function asc()
    {
        require_once 'Xyster/Data/Sort.php';
        return Xyster_Data_Sort::asc($this);
    }
    /**
     * Factories a descending {@link Xyster_Data_Sort} for this field name
     *
     * @return Xyster_Data_Sort
     */
    public function desc()
    {
        require_once 'Xyster/Data/Sort.php';
        return Xyster_Data_Sort::desc($this);
    }
    /**
     * Factories an Equal To Xyster_Data_Expression ( column = 'value' )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function eq( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a Not Equal To Xyster_Data_Expression ( column <> 'value' )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function neq( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a Less Than Xyster_Data_Expression ( column < 3 )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function lt( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a Less Than or Equal To Xyster_Data_Expression ( column <= 3 )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function lte( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a Greater Than Xyster_Data_Expression ( column > 2 )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function gt( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a Greater Than or Equal To Xyster_Data_Expression ( column >= 2 )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function gte( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a LIKE Xyster_Data_Expression ( column LIKE '%value' )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function like( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a NOT LIKE Xyster_Data_Expression ( column NOT LIKE '%value' )
     *
     * @param mixed $value
     * @return Xyster_Data_Expression
     */
    public function notLike( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    /**
     * Factories a BETWEEN Xyster_Data_Expression ( column BETWEEN 'value' AND 'value' )
     *
     * @param mixed $start
     * @param mixed $end
     * @return Xyster_Data_Expression
     */
    public function between( $start, $end )
    {
        return $this->_expression(__FUNCTION__, array($this, $start, $end));
    }
    /**
     * Factories a NOT BETWEEN Xyster_Data_Expression ( column NOT BETWEEN 'value' AND 'value' )
     *
     * @param mixed $start
     * @param mixed $end
     * @return Xyster_Data_Expression
     */
    public function notBetween( $start, $end )
    {
        return $this->_expression(__FUNCTION__, array($this, $start, $end));
    }
    /**
     * Factories an In expression ( column IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param array $choices
     * @return Xyster_Data_Expression
     */
    public function in( array $choices )
    {
        return $this->_expression(__FUNCTION__, array($this, $choices));
    }
    /**
     * Factories a Not in expression ( column NOT IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param array $choices
     * @return Xyster_Data_Expression
     */
    public function notIn( array $choices )
    {
        return $this->_expression(__FUNCTION__, array($this, $choices));
    }

    /**
     * Factories an expression 
     * 
     * @param string $function
     * @param array $params
     * @return Xyster_Data_Expression
     */
    protected function _expression( $function, array $params = array() )
    {
        require_once 'Xyster/Data/Expression.php';
        return call_user_func_array(
                array('Xyster_Data_Expression',$function),
                $params
            );
    }

    /**
     * Creates a Xyster_Data_Field by name with an alias
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Xyster_Data_Field
     */
    static public function named( $name, $alias = null )
    {
        return new Xyster_Data_Field($name, $alias);
    }
    /**
     * Creates a Xyster_Data_Field that defines a group in a result
     *
     * @param string $name
     * @param string $alias
     * @return Xyster_Data_Field_Group
     */
    static public function group( $name, $alias = null )
    {
        require_once 'Xyster/Data/Field/Group.php';
        return new Xyster_Data_Field_Group($name, $alias);
    }
    /**
     * Creates an {@link Xyster_Data_Aggregate} field
     * 
     * @param Xyster_Data_Aggregate $function The aggregate used
     * @param string $name The name of the field to aggregate
     * @param string $alias Optional; the alias of the aggregate field
     * @return Xyster_Data_Field_Aggregate
     */
    static public function aggregate( Xyster_Data_Aggregate $function, $name, $alias = null )
    {
        require_once 'Xyster/Data/Field/Aggregate.php';
        return new Xyster_Data_Field_Aggregate($function, $name, $alias);
    }
    /**
     * Creates an {@link Xyster_Data_Aggregate} field to count items in a tuple
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Xyster_Data_Field_Aggregate
     */
    static public function count( $name, $alias = null )
    {
        return self::aggregate(Xyster_Data_Aggregate::Count(), $name, $alias);
    }
    /**
     * Creates an {@link Xyster_Data_Aggregate} field to sum the values in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Xyster_Data_Field_Aggregate
     */
    static public function sum( $name, $alias = null )
    {
        return self::aggregate(Xyster_Data_Aggregate::Sum(), $name, $alias);
    }
    /**
     * Creates an {@link Xyster_Data_Aggregate} field to average the values in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Xyster_Data_Field_Aggregate
     */
    static public function avg( $name, $alias = null )
    {
        return self::aggregate(Xyster_Data_Aggregate::Average(), $name, $alias);
    }
    /**
     * Creates an {@link Xyster_Data_Aggregate} field to find the maximum value in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Xyster_Data_Field_Aggregate
     */
    static public function max( $name, $alias = null )
    {
        return self::aggregate(Xyster_Data_Aggregate::Maximum(), $name, $alias);
    }
    /**
     * Creates an {@link Xyster_Data_Aggregate} field to find the minimum value in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Xyster_Data_Field_Aggregate
     */
    static public function min( $name, $alias = null )
    {
        return self::aggregate(Xyster_Data_Aggregate::Minimum(), $name, $alias);
    }
}