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
 * @see Xyster_Data_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Enumerated type of Expression operators
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Operator_Expression extends Xyster_Enum
{
	const Eq = '=';
	const Neq = '<>';
	const Lt = '<';
	const Gt = '>';
	const Gte = '>=';
	const Lte = '<=';
	const Like = 'LIKE';
	const NotLike = 'NOT LIKE';
	const Between = 'BETWEEN';
	const NotBetween = 'NOT BETWEEN';
	const In = 'IN';
	const NotIn = 'NOT IN';
	
    /**
     * Evaluates the operator for two values
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function evaluate( $a, $b )
    {
        $operator = $this->getValue();

        $bool = false;
        $eval = "\$bool = (bool)( \$a %s \$b );";
        switch( $operator ) {
            case "=":
                eval(sprintf($eval, '=='));
                break;
                
            case "<>":
                eval(sprintf($eval, '!='));
                break;
                
            case "LIKE":
            case "NOT LIKE":
                $lookend = ( substr($b, 0, 1) == '%' );
                $lookbeg = ( substr($b, -1, 1) == '%' );
                $lookin = ( $lookbeg && $lookend );
                if ( $lookin ) {
                    $bool = strpos($a, substr($b, 1, strlen($b)-2)) > -1;
                } else if ( $lookbeg ) {
                    $bool = (substr($b, 0, -1) == substr($a, 0, strlen($b)-1));
                } else if ( $lookend ) {
                    $match = substr($b, 1);
                    $bool = $match == substr($a, -strlen($match));
                }
                if ( $operator == "NOT LIKE" ) {
                    $bool = !$bool;
                }
                break;
                
            case "IN":
                $bool = ( in_array($a, $b) );
                break;

            case "NOT IN":
                $bool = ( !in_array($a, $b) );
                break;
                
            case "BETWEEN":
                $bool = ( $a >= $b[0] ) && ( $a <= $b[1] );
                break;

            case "NOT BETWEEN":
                $bool = ( $a < $b[0] ) || ( $a > $b[1] );
                break;
    
            case ">":
            case "<":
            case ">=":
            case "<=":
            default:
                eval(sprintf($eval, $operator));
        }
        
        return $bool;
    }
	
    /**
     * Uses the Eq operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Eq()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Neq operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Neq()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Lt operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Lt()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Lte operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Lte()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Gt operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Gt()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Gte operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Gte()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Like operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Like()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the NotLike operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function NotLike()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the Between operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function Between()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the NotBetween operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function NotBetween()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the In operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function In()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * Uses the NotIn operator
     *
     * @return Xyster_Data_Operator_Expression
     */
    public static function NotIn()
    {
       return Xyster_Enum::_factory();
    }
}