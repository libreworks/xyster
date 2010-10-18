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
namespace Xyster\Data;
/**
 * A set that holds rows and columns
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Tuple extends Set
{
    protected $_names = array();
    protected $_values = array();

    /**
     * Creates a new tuple
     *
     * @param array $values An associative array of group names and their values
     * @param \Xyster\Collection\ICollection $contents The objects or arrays to add to the tuple
     */
    public function __construct(array $values, \Xyster\Collection\ICollection $contents = null)
    {
        parent::__construct($contents);

        $this->_values = $values;
        $this->_names = array_keys($values);
    }

    /**
     * Gets the names of the groups
     *
     * @return array
     */
    public function getNames()
    {
        return $this->_names;
    }

    /**
     * Gets the value of a group
     *
     * @param string $name  The group name
     * @return string
     */
    public function getValue($name)
    {
        return $this->_values[$name];
    }

    /**
     * Gets the values for the groups
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Flattens the tuple into a data row
     *
     * @param Symbol\FieldClause $fields
     * @return array
     */
    public function toRow(Symbol\FieldClause $fields)
    {
        $values = array();
        foreach ($fields as $field) {
            $values[$field->getAlias()] = ( $field instanceof Symbol\AggregateField ) ?
                    $this->aggregate($field) : $this->_values[$field->getAlias()];
        }
        return $values;
    }

    /**
     * Creates the Tuples for a collection
     *
     * @param Set $rs  The dataset to add rows representing the tuples
     * @param mixed $collection  The collection of objects/hashtables to use
     * @param Symbol\FieldClause $fields  The field objects to evaluate
     * @param array $having  Optional. An array of {@link Symbol\Criterion} objects
     * @param int $limit  Optional. The maximum number of tuples to create
     * @param int $offset  Optional.  The number of tuples to skip before adding
     * @throws \Xyster\Data\DataException if there are no grouped columns in the $fields array
     */
    static public function makeTuples(Set $rs, $collection, Symbol\FieldClause $fields, array $having = null, $limit = 0, $offset = 0)
    {
        $groups = array();
        foreach ($fields as $v) {
            if ($v instanceof Symbol\GroupField) {
                $groups[] = $v;
            }
        }
        if (!count($groups)) {
            throw new \Xyster\Data\DataException('You must specify at least one grouped field');
        }

        $tuples = array();
        $tupleValues = array();
        foreach ($collection as $v) {
            $groupValues = array();
            $groupHash = '';
            foreach ($groups as $group) {
                $value = Symbol\Evaluator::get($v, $group);
                $groupValues[$group->getAlias()] = $value;
                $groupHash .= "['" . $value . "']";
            }
            $tupleValues[$groupHash] = $groupValues;
            if (!isset($tuples[$groupHash])) {
                $tuples[$groupHash] = array();
            }
            $tuples[$groupHash][] = $v;
        }

        $loffset = 0;
        foreach ($tupleValues as $hash => $values) {
            $tuple = new self($values,
                    \Xyster\Collection\Collection::using($tuples[$hash]));
            $ok = true;
            if ($having) {
                foreach ($having as $crit) {
                    if (!$crit->evaluate($tuple)) {
                        $ok = false;
                    }
                }
            }
            if ($ok) {
                if ($loffset < $offset) {
                    $loffset++;
                } else {
                    $rs->add($tuple->toRow($fields));
                    if ($limit > 0 && count($rs) == $limit) {
                        break;
                    }
                }
            }
        }
    }
}