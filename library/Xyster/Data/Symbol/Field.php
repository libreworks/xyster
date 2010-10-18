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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Data\Symbol;
/**
 * A simple concept for data fields and columns
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Field implements ISymbol
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
     * @throws InvalidArgumentException if the field name is empty
     */
    protected function __construct( $name, $alias=null )
    {
        $this->_name = trim($name);
        if ( !strlen($this->_name) ) {
            throw new InvalidArgumentException('A field name cannot be empty');
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
     * @throws InvalidArgumentException if the alias is invalid
     */
    public function setAlias( $alias )
    {
        if ( !strlen(trim($alias)) ) {
            throw new InvalidArgumentException('An alias cannot be empty');
        }
        $this->_alias = trim($alias);
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
     * Factories an ascending {@link Sort} for this field name
     *
     * @return Sort
     */
    public function asc()
    {
        return Sort::asc($this);
    }
    
    /**
     * Factories a descending {@link Sort} for this field name
     *
     * @return Sort
     */
    public function desc()
    {
        return Sort::desc($this);
    }
    
    /**
     * Factories an Equal To Expression ( column = 'value' )
     *
     * @param mixed $value
     * @return Expression
     */
    public function eq( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a Not Equal To Expression ( column <> 'value' )
     *
     * @param mixed $value
     * @return Expression
     */
    public function neq( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a Less Than Expression ( column < 3 )
     *
     * @param mixed $value
     * @return Expression
     */
    public function lt( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a Less Than or Equal To Expression ( column <= 3 )
     *
     * @param mixed $value
     * @return Expression
     */
    public function lte( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a Greater Than Expression ( column > 2 )
     *
     * @param mixed $value
     * @return Expression
     */
    public function gt( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a Greater Than or Equal To Expression ( column >= 2 )
     *
     * @param mixed $value
     * @return Expression
     */
    public function gte( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a LIKE Expression ( column LIKE '%value' )
     *
     * @param mixed $value
     * @return Expression
     */
    public function like( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a NOT LIKE Expression ( column NOT LIKE '%value' )
     *
     * @param mixed $value
     * @return Expression
     */
    public function notLike( $value )
    {
        return $this->_expression(__FUNCTION__, array($this, $value));
    }
    
    /**
     * Factories a BETWEEN Expression ( column BETWEEN 'value' AND 'value' )
     *
     * @param mixed $start
     * @param mixed $end
     * @return Expression
     */
    public function between( $start, $end )
    {
        return $this->_expression(__FUNCTION__, array($this, $start, $end));
    }
    
    /**
     * Factories a NOT BETWEEN Expression ( column NOT BETWEEN 'value' AND 'value' )
     *
     * @param mixed $start
     * @param mixed $end
     * @return Expression
     */
    public function notBetween( $start, $end )
    {
        return $this->_expression(__FUNCTION__, array($this, $start, $end));
    }
    
    /**
     * Factories an In expression ( column IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param array $choices
     * @return Expression
     */
    public function in( array $choices )
    {
        return $this->_expression(__FUNCTION__, array($this, $choices));
    }
    
    /**
     * Factories a Not in expression ( column NOT IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param array $choices
     * @return Expression
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
     * @return Expression
     */
    protected function _expression( $function, array $params = array() )
    {
        return call_user_func_array(
                array('\Xyster\Data\Symbol\Expression',$function),
                $params
            );
    }

    /**
     * Creates a Field by name with an alias
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return Field
     */
    static public function named( $name, $alias = null )
    {
        if ( $match = AggregateField::match($name) ) {
            $function = \Xyster\Enum\Enum::valueOf('\Xyster\Data\Symbol\Aggregate', $match['function']);
            return self::aggregate($function, $match['field'], $alias);
        } else {
            return new self($name, $alias);
        }
    }

    /**
     * Creates a Field that defines a group in a result
     *
     * @param string $name
     * @param string $alias
     * @return GroupField
     */
    static public function group( $name, $alias = null )
    {
        return new GroupField($name, $alias);
    }
    
    /**
     * Creates an {@link Aggregate} field
     * 
     * @param Aggregate $function The aggregate used
     * @param string $name The name of the field to aggregate
     * @param string $alias Optional; the alias of the aggregate field
     * @return AggregateField
     */
    static public function aggregate( Aggregate $function, $name, $alias = null )
    {
        return new AggregateField($function, $name, $alias);
    }
    
    /**
     * Creates an {@link Aggregate} field to count items in a tuple
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return AggregateField
     */
    static public function count( $name, $alias = null )
    {
        return self::aggregate(Aggregate::Count(), $name, $alias);
    }
    
    /**
     * Creates an {@link Aggregate} field to sum the values in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return AggregateField
     */
    static public function sum( $name, $alias = null )
    {
        return self::aggregate(Aggregate::Sum(), $name, $alias);
    }
    
    /**
     * Creates an {@link Aggregate} field to average the values in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return AggregateField
     */
    static public function avg( $name, $alias = null )
    {
        return self::aggregate(Aggregate::Average(), $name, $alias);
    }
    
    /**
     * Creates an {@link Aggregate} field to find the maximum value in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return AggregateField
     */
    static public function max( $name, $alias = null )
    {
        return self::aggregate(Aggregate::Maximum(), $name, $alias);
    }
    
    /**
     * Creates an {@link Aggregate} field to find the minimum value in a field
     *
     * @param string $name The name of the field
     * @param string $alias The alias to assign
     * @return AggregateField
     */
    static public function min( $name, $alias = null )
    {
        return self::aggregate(Aggregate::Minimum(), $name, $alias);
    }
}