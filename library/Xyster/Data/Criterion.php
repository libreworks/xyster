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
 * The base class for Criteria
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Data_Criterion
{
	/**
	 * Evaluates the Criterion for a given object
	 *
	 * @param mixed $value
	 * @return boolean
	 */
    abstract public function evaluate( $value );

	/**
	 * Creates a Junction from an array of Criterion objects
	 *
	 * @param string $operator
	 * @param array $criteria
	 * @return Xyster_Data_Criterion
	 * @throws Xyster_Data_Exception if the operator is unknown
	 */
	static public function fromArray( $operator, array $criteria )
	{
		if ( count($criteria) < 1 ) {
			return;
		}
		if ( count($criteria) == 1 ) {
			return $criteria[0];
		}
		else {
			if ( strtoupper($operator) != 'AND' && strtoupper($operator) != 'OR' ) {
				require_once 'Xyster/Data/Exception.php';
				throw new Xyster_Data_Exception($operator . ' is not a valid Junction operator');
			}
			$j = new Xyster_Data_Junction($criteria[0], $criteria[1], strtoupper($operator));
			$j->_criteria = $criteria;
			return $j;
		}
	}
    
    /**
     * Recursively gets all of the fields in the Criterion 
     *
     * @param Xyster_Data_Criterion $criteria
     * @return Xyster_Collection
     */
    static public function getFields( Xyster_Data_Criterion $criteria )
    {
        require_once 'Xyster/Collection.php';
        return Xyster_Collection::using(self::_getFields($criteria), true);
    }

    /**
     * Gets fields in a criterion
     *
     * @param Xyster_Data_Criterion $criteria
     * @return array
     */
    static protected function _getFields( Xyster_Data_Criterion $criteria )
    {
        $fields = array();
        if ( $criteria instanceof Xyster_Data_Junction ) {
            foreach( $criteria->_criteria as $crit ) {
                $fields = array_merge($fields, self::_getFields($crit));
            }
        } else {
            $fields[] = $criteria->getLeft();
            if ( $criteria->getRight() instanceof Xyster_Data_Field ) {
                $fields[] = $criteria->getRight();
            }
        }
        return $fields;
    }
}