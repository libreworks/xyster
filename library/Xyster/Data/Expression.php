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
 * Xyster_Data_Criterion
 */
require_once 'Xyster/Data/Criterion.php';
/**
 * An expression is a boolean evaluation comparing a column against a value
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Expression extends Xyster_Data_Criterion
{
    /**
     * Internal list of valid expression operators
     *
     * @var array
     */
    static private $_operators = array(
        "eq" => "=",
        "neq" => "<>",
        "lt" => "<",
        "gt" => ">",
        "gte" => ">=",
        "lte" => "<=",
        "like" => "LIKE",
        "notLike" => "NOT LIKE",
        "between" => "BETWEEN",
        "notBetween" => "NOT BETWEEN",
        "in" => "IN",
        "notIn" => "NOT IN"
    );

    /**
     * The left value, always a field
     *
     * @var Xyster_Data_Field
     */
    protected $_left;
    /**
     * The operator
     * 
     * @var string
     */
    protected $_operator;
    /**
     * The right value, could be a scalar or a {@link Xyster_Data_Field}
     * 
     * @var mixed
     */
    protected $_right;

    /**
     * Creates a new expression
     * 
     * @param Xyster_Data_Field|string $field
     * @param string $operator
     * @param mixed $value
     */
    private function __construct( $field, $operator, $value )
    {
        if (! $field instanceof Xyster_Data_Field) {
            $field = Xyster_Data_Field::named($field);
        }
        $this->_left = $field;
        $this->_operator = $operator;
        if ( $value === null ) {
            $value = "NULL";
        }
        $this->_right = $value;
    }

    /**
     * Gets the field
     *
     * @return Xyster_Data_Field
     */
    public function getLeft()
    {
        return $this->_left;
    }

    /**
     * Gets the operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->_operator;
    }

    /**
     * Gets the value
     *
     * @return mixed
     */
    public function getRight()
    {
        return $this->_right;
    }

    /**
     * Returns the syntax for this Expression
     *
     * @magic
     * @return string
     */
    public function __toString()
    {
        $string = "";
        $val = $this->_right;
        if ( $val == "NULL" || $val === null ) {
            $string .= 'NULL';
        } else if ( is_array($val) && strpos($this->_operator,'BETWEEN') ) {
            $string .= ( preg_match("/^[0-9.]+$/",$val[0]) ) ?
                $val[0] : "'".str_replace("'","''",$val[0])."'";
            $string .= ' AND ';
            $string .= ( preg_match("/^[0-9.]+$/",$val[1]) ) ?
                $val[1] : "'".str_replace("'","''",$val[1])."'";
        } else if ( is_array($val)
            && $this->_operator == "IN" || $this->_operator == "NOT IN" ) {
            $quoted = array();
            foreach( $val as $v ) {
                $quoted[] = ( preg_match("/^[0-9.]+$/",$v) ) ?
                    $v : "'".str_replace("'","''",$v)."'";
            }
            $string .= '('. implode(',',$quoted) . ')';
        } else if ( $val instanceof Xyster_Data_Field ) {
            $string .= (string)$val;
        } else if ( !is_numeric($val) ) {
            $string .= "'".str_replace("'","''",$val)."'";
        } else {
            $string .= $val;
        }
        return (string)$this->_left . " " . $this->_operator . " " . $string;
    }

    /**
     * Evaluates the Expression for a given array or object
     *
     * @param mixed $object
     * @return boolean
     */
    public function evaluate( $object )
    {
        $value = $this->_left->evaluate($object);
        $value2 = ( $this->_right instanceof Xyster_Data_Field ) ?
            $this->_right->evaluate($object) : $this->_right;
            $bool = false;
        $eval = "\$bool = (bool)( \$value %s \$value2 );";
        switch( $this->_operator ) {
            case "=":
                eval(sprintf($eval,'=='));
                break;
                
            case "<>":
                eval(sprintf($eval,'!='));
                break;
                
            case ">":
            case "<":
            case ">=":
            case "<=":
                eval(sprintf($eval,$this->_operator));
                break;
                
            case "LIKE":
            case "NOT LIKE":
                $lookend = ( substr($value2,0,1) == '%' );
                $lookbeg = ( substr($value2,-1,1) == '%' );
                $lookin = ( $lookbeg && $lookend );
                if ( $lookin ) {
                    $bool = strpos($value,substr($value2,1,strlen($value2)-2)) > -1;
                } else if ( $lookbeg ) {
                    $bool = ( substr($value2,0,-1) == substr($value,0,strlen($value2)-1) );
                } else if ( $lookend ) {
                    $match = substr($value2,1);
                    $bool = ( $match == substr($value,-strlen($match)) );
                }
                if ( $this->_operator == "NOT LIKE" ) {
                    $bool = !$bool;
                }
                break;
                
            case "IN":
                $bool = ( in_array($value,$value2) );
                break;

            case "NOT IN":
                $bool = ( !in_array($value,$value2) );
                break;
                
            case "BETWEEN":
                $bool = ( $value >= $value2[0] ) && ( $value <= $value2[1] );
                break;

            case "NOT BETWEEN":
                $bool = ( $value < $value2[0] ) && ( $value > $value2[1] );
                break;
    
            default:
                break;
        }
        return $bool;
    }

    /**
     * Tests whether the string passed is a valid Expression operator
     *
     * @param string $operator
     * @return boolean
     */
    static public function isOperator( $operator )
    {
        return in_array($operator,self::$_operators);
    }

    /**
     * Returns the name of the static method to call for the operator passed
     *
     * @param string $operator
     * @return string
     */
    static public function getMethodName( $operator )
    {
        return array_search($operator,self::$_operators);
    }

    /**
     * Equal To Xyster_Data_Expression ( field = 'value' )
     *
     * For $value to reference another field, pass a {@link Xyster_Data_Field} object
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function eq( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Not Equal To Xyster_Data_Expression ( field <> 'value' )
     *
     * For $value to reference another field, pass a {@link Xyster_Data_Field} object

     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function neq( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Less Than Xyster_Data_Expression ( field < 3 )
     *
     * For $value to reference another field, pass a {@link Xyster_Data_Field} object
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function lt( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Less Than or Equal To Xyster_Data_Expression ( field <= 3 )
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function lte( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Greater Than Xyster_Data_Expression ( field > 2 )
     *
     * For $value to reference another field, pass a {@link Xyster_Data_Field} object
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function gt( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Greater Than or Equal To Xyster_Data_Expression ( field >= 2 )
     *
     * For $value to reference another field, pass a {@link Xyster_Data_Field} object
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function gte( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * LIKE Xyster_Data_Expression ( field LIKE '%value' )
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function like( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * NOT LIKE Xyster_Data_Expression ( field NOT LIKE '%value' )
     *
     * @param string $field
     * @param string $value
     * @return Xyster_Data_Expression
     */
    static public function notLike( $field, $value )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * BETWEEN Xyster_Data_Expression ( field BETWEEN 'value' AND 'value' )
     *
     * @param string $field
     * @param string $start
     * @param string $end
     * @return Xyster_Data_Expression
     */
    static public function between( $field, $start, $end )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], array($start, $end));
    }

    /**
     * Equal To Xyster_Data_Expression ( field NOT BETWEEN 'value' AND 'value' )
     *
     * @param string $field
     * @param string $start
     * @param string $end
     * @return Xyster_Data_Expression
     */
    static public function notBetween( $field, $start, $end )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], array($start, $end));
    }

    /**
     * In expression ( field IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param string $field
     * @param array $choices
     * @return Xyster_Data_Expression
     */
    static public function in( $field, array $choices )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $choices);
    }

    /**
     * Not in expression ( field NOT IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param string $field
     * @param array $choices
     * @return Xyster_Data_Expression
     */
    static public function notIn( $field, array $choices )
    {
        return new Xyster_Data_Expression($field, self::$_operators[__FUNCTION__], $choices);
    }
}