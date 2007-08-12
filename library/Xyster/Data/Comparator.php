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
 * Xyster_Collection_Comparator_Interface
 */
require_once 'Xyster/Collection/Comparator/Interface.php';
/**
 * Comparator for objects or associative arrays
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Comparator implements Xyster_Collection_Comparator_Interface
{
	/**
	 * The array of {@link Xyster_Data_Sort} objects
	 *
	 * @var array
	 */
	protected $_sorts = array();

	/**
	 * Create a new Data Comparator object
	 *
	 * @param array $sort  An array of {@link Xyster_Data_Sort} objects
	 */
	public function __construct( array $sorts )
	{
		foreach( $sorts as $sort ) {
			if ( ! $sort instanceof Xyster_Data_Sort ) {
				require_once 'Xyster/Data/Exception.php';
				throw new Xyster_Data_Exception('Only Xyster_Data_Sort objects can be used');
			}
			$this->_sorts[] = $sort;
		}
	}

	/**
	 * Compares two arguments for sorting
	 * 
	 * Returns a negative integer, zero, or a positive integer as the first
	 * argument is less than, equal to, or greater than the second.
	 *
	 * @param mixed $a
	 * @param mixed $b
	 */
	public function compare( $a, $b )
	{
		foreach( $this->_sorts as $sort ) {
			$av = $sort->getField()->evaluate($a);
			$bv = $sort->getField()->evaluate($b);
			if ( $av == $bv ) {
				continue;
			} else if ( $av < $bv ) { 
				return ( $sort->getDirection() == 'ASC' ) ? -1 : 1;
			} else if ( $av > $bv ) {
				return ( $sort->getDirection() == 'ASC' ) ? 1 : -1;
			}
		}
		return 0;
	}
}