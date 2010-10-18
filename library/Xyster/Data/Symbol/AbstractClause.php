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

use Xyster\Type\Type;

/**
 * Abstract clause of symbols
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractClause implements IClause
{
    /**
     * @var array
     */
    protected $_items = array();
    /**
     * @var Type
     */
    private $_type;

    /**
     * Creates a new data clause
     *
     * Extending classes MUST pass the type to this constructor for the class to
     * work as expected.
     *
     * @param Type $type The class type allowed in this clause
     * @param ISymbol $symbol A clause or symbol to add
     */
    public function __construct(Type $type, ISymbol $symbol = null)
    {
        $this->_type = $type;
        if ($symbol instanceof AbstractClause) {
            $this->merge($symbol);
        } else if ($symbol instanceof IClause) {
            foreach ($symbol as $v) {
                $this->add($v);
            }
        } else if ($symbol !== null) {
            $this->add($symbol);
        }
    }

    /**
     * Adds an item to this clause
     *
     * @param ISymbol $symbol
     * @return AbstractClause provides a fluent interface
     * @throws InvalidArgumentException if the symbol is of the incorrect type for the clause
     */
    public function add(ISymbol $symbol)
    {
        if (!$this->_type->isInstance($symbol)) {
            throw new InvalidArgumentException("This clause only supports " . $this->_type);
        }
        $this->_items[] = $symbol;
        return $this;
    }

    /**
     * Gets the number of entries in the clause
     *
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * Gets the iterator for this clause
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return count($this->_items) ?
                new \ArrayIterator($this->_items) : new \EmptyIterator;
    }

    /**
     * Adds the items from one clause to the end of this one
     *
     * @param AbstractClause $clause
     * @return AbstractClause provides a fluent interface
     * @throws InvalidArgumentException if the symbol is of the incorrect type for the clause
     */
    public function merge(AbstractClause $clause)
    {
        if (!$this->_type->equals($clause->_type)) {
            throw new InvalidArgumentException("This clause only supports " . $this->_type);
        }
        $this->_items = array_merge($this->_items, $clause->_items);
        return $this;
    }

    /**
     * Removes an entry in the clause
     *
     * @param ISymbol $symbol
     * @return boolean Whether the clause was changed
     */
    public function remove(ISymbol $symbol)
    {
        foreach ($this->_items as $k => $v) {
            if (Type::areDeeplyEqual($v, $symbol)) {
                unset($this->_items[$k]);
                return true;
            }
        }
        return false;
    }

    /**
     * Converts the clause into an array of its symbols
     *
     * @return array
     */
    public function toArray()
    {
        return array_values($this->_items);
    }

    /**
     * Gets the string representation of this object
     *
     * @magic
     * @return string
     */
    public function __toString()
    {
        return implode(', ', $this->_items);
    }
}