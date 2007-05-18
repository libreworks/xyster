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
 * A Junction is an infix expression of Xyster_Data_Criterion objects
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Junction extends Xyster_Data_Criterion
{
	protected $_criteria = array();
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
		$this->_criteria[] = $lc;
		$this->_criteria[] = $rc;
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
	 * Adds a Criterion to this Junction
	 *
	 * @param Xyster_Data_Criterion $c
	 */
	public function add( Xyster_Data_Criterion $c )
	{
		$this->_criteria[] = $c;
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
	 * Create a new OR Junction ( x OR y )
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
	 * Create a new AND Junction ( x AND y )
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