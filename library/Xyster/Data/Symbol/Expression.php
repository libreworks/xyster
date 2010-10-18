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
 * An expression is a boolean evaluation comparing a column against a value
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Expression extends Criterion
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
     * @var Field
     */
    protected $_left;
    
    /**
     * The operator
     * 
     * @var Operator
     */
    protected $_operator;
    
    /**
     * The right value, could be a scalar or a {@link Field}
     * 
     * @var mixed
     */
    protected $_right;

    /**
     * Creates a new expression
     * 
     * @param Field|string $field
     * @param string $operator
     * @param mixed $value
     */
    protected function __construct( $field, $operator, $value )
    {
        if (! $field instanceof Field) {
            $field = Field::named($field);
        }
        $this->_left = $field;
        $this->_operator = \Xyster\Enum\Enum::valueOf('\Xyster\Data\Symbol\Operator', $operator);
        if ( $value == "NULL" ) {
            $value = null;
        }
        $this->_right = $value;
    }

    /**
     * @return Field[]
     */
    public function getAllFields()
    {
        $fields = array($this->_left);
        if ( $this->_right instanceof Field ) {
            $fields[] = $this->_right;
        }
        return $fields;
    }

    /**
     * Gets the field
     *
     * @return Field
     */
    public function getLeft()
    {
        return $this->_left;
    }

    /**
     * Gets the operator
     *
     * @return Operator
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
        $op = $this->_operator->getValue();
        $val = $this->_right;
        
        if ( $val === null ) {
            $string .= 'NULL';
        } else if ( is_array($val) && strpos($op, 'BETWEEN') !== false ) {
            $string .= ( preg_match("/^[0-9.]+$/", $val[0]) ) ?
                $val[0] : "'" . str_replace("'", "''", $val[0]) . "'";
            $string .= ' AND ';
            $string .= ( preg_match("/^[0-9.]+$/", $val[1]) ) ?
                $val[1] : "'" . str_replace("'", "''", $val[1]) . "'";
        } else if ( is_array($val) && $op == "IN" || $op == "NOT IN" ) {
            $quoted = array();
            foreach( $val as $v ) {
                $quoted[] = ( preg_match("/^[0-9.]+$/", $v) ) ?
                    $v : "'" . str_replace("'", "''", $v) . "'";
            }
            $string .= '(' . implode(',', $quoted) . ')';
        } else if ( $val instanceof Field ) {
            $string .= (string)$val;
        } else if ( !is_numeric($val) ) {
            $string .= "'" . str_replace("'", "''", $val) . "'";
        } else {
            $string .= $val;
        }
        
        return (string)$this->_left . " " . $op . " " . $string;
    }

    /**
     * Evaluates the Expression for a given array or object
     *
     * @param mixed $object
     * @return boolean
     */
    public function evaluate( $object )
    {
        $a = Evaluator::get($object, $this->_left);
        $b = ( $this->_right instanceof Field ) ?
            Evaluator::get($object, $this->_right) :
            $this->_right;
        return $this->_operator->evaluate($a, $b);
    }

    /**
     * Returns the name of the static method to call for the operator passed
     *
     * @param string $operator
     * @return string
     */
    static public function getMethodName( $operator )
    {
        $method = array_search($operator, array_flip(\Xyster\Enum\Enum::values('\Xyster\Data\Symbol\Operator')));
        return strlen($method) > 1 ? strtolower($method[0]) . substr($method, 1) : false;
    }

    /**
     * Equal To Expression ( field = 'value' )
     *
     * For $value to reference another field, pass a {@link Field} object
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function eq( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Not Equal To Expression ( field <> 'value' )
     *
     * For $value to reference another field, pass a {@link Field} object

     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function neq( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Less Than Expression ( field < 3 )
     *
     * For $value to reference another field, pass a {@link Field} object
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function lt( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Less Than or Equal To Expression ( field <= 3 )
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function lte( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Greater Than Expression ( field > 2 )
     *
     * For $value to reference another field, pass a {@link Field} object
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function gt( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * Greater Than or Equal To Expression ( field >= 2 )
     *
     * For $value to reference another field, pass a {@link Field} object
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function gte( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * LIKE Expression ( field LIKE '%value' )
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function like( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * NOT LIKE Expression ( field NOT LIKE '%value' )
     *
     * @param string $field
     * @param string $value
     * @return Expression
     */
    static public function notLike( $field, $value )
    {
        return new self($field, self::$_operators[__FUNCTION__], $value);
    }

    /**
     * BETWEEN Expression ( field BETWEEN 'value' AND 'value' )
     *
     * @param string $field
     * @param string $start
     * @param string $end
     * @return Expression
     */
    static public function between( $field, $start, $end )
    {
        return new self($field, self::$_operators[__FUNCTION__], array($start, $end));
    }

    /**
     * Equal To Expression ( field NOT BETWEEN 'value' AND 'value' )
     *
     * @param string $field
     * @param string $start
     * @param string $end
     * @return Expression
     */
    static public function notBetween( $field, $start, $end )
    {
        return new self($field, self::$_operators[__FUNCTION__], array($start, $end));
    }

    /**
     * In expression ( field IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param string $field
     * @param array $choices
     * @return Expression
     */
    static public function in( $field, array $choices )
    {
        return new self($field, self::$_operators[__FUNCTION__], $choices);
    }

    /**
     * Not in expression ( field NOT IN ( 1,1,2,3,5,8,13,21,'fibonacci','sequence' ) )
     *
     * @param string $field
     * @param array $choices
     * @return Expression
     */
    static public function notIn( $field, array $choices )
    {
        return new self($field, self::$_operators[__FUNCTION__], $choices);
    }
}