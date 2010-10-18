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
 * A struct that holds a field and a direction
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Sort implements ISymbol
{
    /**
     * The direction of the sort
     * 
     * @var bool
     */
    private $_ascending;

    /**
     * @var Field
     */
    private $_field;

    /**
     * Creates a new Sort
     * 
     * @param Field|string $field
     * @param bool $ascending
     */
    private function __construct( $field, $ascending )
    {
        $this->_ascending = $ascending !== false;
        if (! $field instanceof Field) {
            $field = Field::named($field);
        }
        $this->_field = $field;
    }

    /**
     * Gets the field
     *
     * @return Xyster_Data_Field
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Gets the sort direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->_ascending ? 'ASC' : 'DESC';
    }

    /**
     * Gets whether the sort is ascending
     *
     * @return bool
     */
    public function isAscending()
    {
        return $this->_ascending;
    }

    /**
     * Returns the string syntax for this Sort
     *
     * @magic
     * @return string
     */
    public function __toString()
    {
        return $this->_field->getName() . ' ' . $this->getDirection();
    }

    /**
     * Create a new ascending sort for the column specified
     *
     * @param string $field
     * @return Xyster_Data_Sort
     */
    static public function asc( $field )
    {
        return new self($field, true);
    }

    /**
     * Create a new descending Sort for the column specified
     *
     * @param string $field
     * @return Xyster_Data_Sort
     */
    static public function desc( $field )
    {
        return new self($field, false);
    }
}