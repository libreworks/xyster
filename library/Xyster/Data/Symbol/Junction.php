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
 * A Junction is an infix expression of {@link \Xyster\Data\Symbol\Criterion} objects
 * 
 * A typical example of a junction is a SQL where clause: 
 * <code>( <var>expression</var> AND <var>expression</var> )</code>
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Junction extends Criterion implements IClause
{
    /**
     * The criteria in the junction (an array of {@link \Xyster\Data\Symbol\Criterion})
     *
     * @var Criterion[]
     */
    protected $_criteria = array();

    protected $_conjunction = true;

    /**
     * Creates a new junction
     *
     * @param Criterion $lc
     * @param Criterion $rc
     * @param bool $conjunction
     */
    protected function __construct( Criterion $lc, Criterion $rc, $conjunction )
    {
        $this->_conjunction = $conjunction !== false;

        if ( $lc instanceof Junction && $lc->_conjunction == $this->_conjunction ) {
            $this->_criteria = array_merge($this->_criteria, $lc->_criteria);
        } else {
            $this->_criteria[] = $lc;
        }

        if ( $rc instanceof Junction && $rc->_conjunction == $this->_conjunction ) {
            $this->_criteria = array_merge($this->_criteria, $rc->_criteria);
        } else {
            $this->_criteria[] = $rc;
        }
    }

    /**
     * Adds a Criterion to this Junction
     *
     * @param Criterion $c
     * @return Junction
     */
    public function add( Criterion $c )
    {
        if ( $c instanceof Junction && $c->_conjunction == $this->_conjunction ) {
            $this->_criteria = array_merge($this->_criteria, $c->_criteria);
        } else {
            $this->_criteria[] = $c;
        }
        return $this;
    }
    
    /**
     * Gets the number of entries in the clause
     *
     * @return int
     */
    public function count()
    {
    	return count($this->_criteria);
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
        if ( !$this->_conjunction ) {
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
     * Gets all fields in the Criterion.
     *
     * @return Field[]
     */
    public function getAllFields()
    {
        $fields = array();
        foreach($this->_criteria as $c){
            /* @var $c Criterion */
            $fields = array_merge($fields, $c->getAllFields());
        }
        return $fields;
    }

    /**
     * Gets the Criteria in this Junction
     *
     * @return \Xyster\Collection\Collection
     */
    public function getCriteria()
    {
        return \Xyster\Collection\Collection::using($this->_criteria, true);
    }

    /**
     * Gets the iterator for this clause
     *
     * @return Iterator
     */
    public function getIterator()
    {
    	// no need to return an emptyIterator -- at least 2 items always
    	return new \ArrayIterator($this->_criteria);
    }

    /**
     * Gets whether this Junction is a conjunction (AND) or a disjunction (OR)
     *
     * @return bool
     */
    public function isConjunction()
    {
        return $this->_conjunction;
    }

    /**
     * Gets the Junction operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->_conjunction ? "AND" : "OR";
    }
    
    /**
     * Converts the clause into an array of its symbols
     *
     * @return array
     */
    public function toArray()
    {
    	return array_values($this->_criteria);
    }

    /**
     * Returns the string syntax for this Junction
     *
     * @return string
     */
    public function __toString()
    {
        $criteria = array();
        foreach( $this->_criteria as $v ) {
            $criteria[] = $v->__toString();
        }
        return "( " . implode(" " . $this->getOperator() . " ", $criteria) . " )";
    }

    /**
     * Create a new 'OR' junction, i.e. ( x OR y )
     *
     * @param Criterion $left
     * @param Criterion $right
     * @return Junction
     */
    static public function any( Criterion $left, Criterion $right )
    {
        return new Junction($left, $right, false);
    }
    
    /**
     * Create a new 'AND' junction, i.e. ( x AND y )
     *
     * @param Criterion $left
     * @param Criterion $right
     * @return Junction
     */
    static public function all( Criterion $left, Criterion $right )
    {
        return new Junction($left, $right, true);
    }
}