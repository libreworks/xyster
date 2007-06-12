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
 * @see Xyster_Data_Criterion
 */
require_once 'Xyster/Data/Criterion.php';
/**
 * A Junction is an infix expression of {@link Xyster_Data_Criterion} objects
 * 
 * A typical example of a junction is a SQL where clause: 
 * <code>( <var>expression</var> AND <var>expression</var> )</code>
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Junction extends Xyster_Data_Criterion
{
    /**
     * The criteria in the junction (an array of {@link Xyster_Data_Criterion})
     *
     * @var array
     */
    protected $_criteria = array();

    /**
     * The operator ('AND' or 'OR')
     *
     * @var string
     */
    protected $_operator;

    /**
     * Creates a new junction
     *
     * @param Xyster_Data_Criterion $lc
     * @param Xyster_Data_Criterion $rc
     * @param string $operator
     */
    private function __construct( Xyster_Data_Criterion $lc, Xyster_Data_Criterion $rc, $operator )
    {
        $this->_operator = $operator;

        if ( $lc instanceof Xyster_Data_Junction && $lc->_operator == $operator ) {
            foreach( $lc->_criteria as $criterion ) {
                $this->_criteria[] = $criterion;
            }
        } else {
            $this->_criteria[] = $lc;
        }

        if ( $rc instanceof Xyster_Data_Junction && $rc->_operator == $operator ) {
            foreach( $rc->_criteria as $criterion ) {
                $this->_criteria[] = $criterion;
            }
        } else {
            $this->_criteria[] = $rc;
        }
    }

    /**
     * Adds a Criterion to this Junction
     *
     * @param Xyster_Data_Criterion $c
     */
    public function add( Xyster_Data_Criterion $c )
    {
        if ( $c instanceof Xyster_Data_Junction && $c->_operator == $operator ) {
            foreach( $c->_criteria as $criterion ) {
                $this->_criteria[] = $criterion;
            }
        } else {
            $this->_criteria[] = $c;
        }
    }

    /**
     * Evaluates the Junction for a given object
     *
     * @param mixed $value
     * @return boolean
     */
    public function evaluate( $value )
    {
        $ok = true;
        if ( $this->_operator == "OR" ) {
            $ok = false;
            foreach( $this->_criteria as $crit ) {
                if ( $crit->evaluate($value) ) {
                    $ok = true;
                    break;
                }
            }
        } else {
            foreach( $this->_criteria as $crit ) {
                if ( !$crit->evaluate($value) ) {
                    $ok = false;
                    break;
                }
            }
        }
        return $ok;
    }

    /**
     * Gets the Criteria in this Junction
     *
     * @return Xyster_Collection
     */
    public function getCriteria()
    {
        require_once 'Xyster/Collection.php';
        return Xyster_Collection::fixedCollection($this->_criteria);
    }

    /**
     * Gets the Junction operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->_operator;
    }

    /**
     * Returns the string syntax for this Junction
     *
     * @return string
     */
    public function __toString()
    {
        $criteria = array();
        foreach( $this->_criteria as $v )
            $criteria[] = $v->__toString();
        return "( ".implode( " ".$this->_operator." ", $criteria )." )";
    }

    /**
     * Create a new 'OR' junction, i.e. ( x OR y )
     *
     * @param Xyster_Data_Criterion $left
     * @param Xyster_Data_Criterion $right
     * @return Xyster_Data_Junction
     */
    static public function any( Xyster_Data_Criterion $left, Xyster_Data_Criterion $right )
    {
        return new Xyster_Data_Junction( $left, $right, 'OR' ); 
    }
    /**
     * Create a new 'AND' junction, i.e. ( x AND y )
     *
     * @param Xyster_Data_Criterion $left
     * @param Xyster_Data_Criterion $right
     * @return Xyster_Data_Junction
     */
    static public function all( Xyster_Data_Criterion $left, Xyster_Data_Criterion $right )
    {
        return new Xyster_Data_Junction( $left, $right, 'AND' );
    }
}