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
 * The base class for Criteria
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Criterion implements ISymbol
{
    /**
     * Evaluates the Criterion for a given object
     *
     * @param mixed $value
     * @return boolean
     */
    abstract public function evaluate($value);
    
    /**
     * Gets all fields within the Criterion
     * 
     * @return Field[]
     */
    abstract public function getAllFields();

    /**
     * Creates a Junction from an array of Criterion objects
     *
     * @param string $operator
     * @param array $criteria
     * @return Xyster_Data_Criterion
     * @throws Xyster_Data_Exception if the operator is unknown
     */
    static public function fromArray($operator, array $criteria)
    {
        if (count($criteria) < 1) {
            return;
        }
        if (count($criteria) == 1) {
            return $criteria[0];
        } else {
            if (strtoupper($operator) != 'AND' && strtoupper($operator) != 'OR') {
                throw new InvalidArgumentException($operator . ' is not a valid Junction operator');
            }
            $j = new Junction($criteria[0], $criteria[1], strtoupper($operator));
            $j->_criteria = $criteria;
            return $j;
        }
    }
}